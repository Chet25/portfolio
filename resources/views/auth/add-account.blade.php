<x-layouts.auth :title="__('Add Another Account')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Add another account')" :description="__('Enter your credentials to add another account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('auth.add_account.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
                :value="old('email')"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />
            </div>

            <div class="flex items-center justify-between">
                <flux:link :href="route('switch_account')" class="text-sm">{{ __('Cancel') }}</flux:link>
                <flux:button variant="primary" type="submit">{{ __('Log in') }}</flux:button>
            </div>
        </form>
    </div>
</x-layouts.auth>
