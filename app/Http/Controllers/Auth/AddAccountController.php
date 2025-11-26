<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\MultiAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AddAccountController extends Controller
{
    protected $multiAccountService;

    public function __construct(MultiAccountService $multiAccountService)
    {
        $this->multiAccountService = $multiAccountService;
    }

    /**
     * Show the form to add another account.
     */
    public function create()
    {
        return view('auth.add-account');
    }

    /**
     * Handle the request to add a new account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $this->ensureIsNotRateLimited($request);

        // Attempt to authenticate manually without logging out the current user yet
        if (! Auth::validate($request->only('email', 'password'))) {
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Credentials are valid, retrieve the user
        $user = \App\Models\User::where('email', $request->email)->first();

        // Add to keyring
        $this->multiAccountService->addAccount($user);

        // Now actually log them in as the active user
        // Note: We do NOT flush the session here, preserving the keyring
        Auth::login($user);
        
        // Regenerate ID for security (prevents fixation)
        $request->session()->regenerate();

        RateLimiter::clear($this->throttleKey($request));

        return redirect()->route('dashboard');
    }

    /**
     * Rate Limiting Helpers (copied from standard login logic)
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return \Illuminate\Support\Str::lower($request->input('email')) . '|' . $request->ip();
    }
}
