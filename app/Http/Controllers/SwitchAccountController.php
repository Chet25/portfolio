<?php

namespace App\Http\Controllers;

use App\Services\MultiAccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SwitchAccountController extends Controller
{
    protected $multiAccountService;

    public function __construct(MultiAccountService $multiAccountService)
    {
        $this->multiAccountService = $multiAccountService;
    }

    public function index()
    {
        $accounts = $this->multiAccountService->getAccounts();
        return view('switch-account', compact('accounts'));
    }

    public function switch(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $userId = $request->input('user_id');

        // SECURITY CHECK: Ensure the user is allowed to switch to this ID
        if (! $this->multiAccountService->canSwitchTo($userId)) {
            abort(403, 'You are not authorized to switch to this account.');
        }

        // Perform the switch
        Auth::loginUsingId($userId);

        // Regenerate session ID to prevent session fixation attacks
        $request->session()->regenerate();

        // Ensure the session keyring is up to date (just in case)
        $this->multiAccountService->addAccount(Auth::user());

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $userIdToRemove = Auth::id();

        // 1. Remove the current user from the keyring (session array)
        $this->multiAccountService->removeAccount($userIdToRemove);

        // 2. Log out the current user from Laravel's auth layer.
        //    This prevents MultiAccountService::getAccounts() from seeing us as "logged in"
        //    and automatically re-adding us to the list.
        Auth::guard('web')->logout();

        // 3. Check if there are any OTHER accounts left in the keyring
        $remainingAccounts = $this->multiAccountService->getAccounts();

        if ($remainingAccounts->count() > 0) {
            // 4. If yes, automatically log in as the next available user
            $nextUser = $remainingAccounts->first();
            Auth::login($nextUser);
            // Regenerate session ID since identity changed
            $request->session()->regenerate();
            
            return redirect()->route('dashboard');
        }

        // 5. If no accounts are left, fully invalidate the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
