<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MultiAccountService
{
    public const SESSION_KEY = 'multi_account_ids';

    /**
     * Add a user to the session keyring.
     */
    public function addAccount(User $user): void
    {
        $accounts = $this->getAccountIds();
        
        if (!in_array($user->id, $accounts)) {
            $accounts[] = $user->id;
            Session::put(self::SESSION_KEY, $accounts);
        }
    }

    /**
     * Remove a user from the session keyring.
     */
    public function removeAccount(int $userId): void
    {
        $accounts = $this->getAccountIds();
        
        $accounts = array_filter($accounts, fn($id) => $id !== $userId);
        
        Session::put(self::SESSION_KEY, array_values($accounts));
    }

    /**
     * Get all users currently in the keyring.
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAccounts()
    {
        $ids = $this->getAccountIds();
        
        // Always ensure current user is in the list (if logged in)
        if (Auth::check() && !in_array(Auth::id(), $ids)) {
            $this->addAccount(Auth::user());
            $ids[] = Auth::id();
        }

        return User::whereIn('id', $ids)->get();
    }

    /**
     * Check if the current session is authorized to switch to this user.
     */
    public function canSwitchTo(int $userId): bool
    {
        return in_array($userId, $this->getAccountIds());
    }

    /**
     * Get raw array of IDs from session.
     */
    protected function getAccountIds(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }
}
