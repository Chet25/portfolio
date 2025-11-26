<x-layouts.app :title="__('Switch Account')">
    <div class="flex h-full w-full items-center justify-center p-6">
        <div class="w-full max-w-md space-y-8">
            <div class="text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-6 w-6 text-zinc-900 dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                    </svg>
                </div>
                <h2 class="mt-6 text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    {{ __('Switch Account') }}
                </h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Select an account to switch to or add a new one.') }}
                </p>
            </div>

            <div class="space-y-2">
                @foreach($accounts as $account)
                    @if(auth()->id() === $account->id)
                        <!-- Current Account -->
                        <div
                            class="relative flex cursor-default items-center gap-4 rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                            <div
                                class="relative flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                <span
                                    class="text-lg font-medium text-zinc-900 dark:text-white">{{ $account->initials() }}</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">
                                        {{ $account->name }}
                                    </p>
                                    <span
                                        class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-400/10 dark:text-emerald-400 dark:ring-emerald-400/20">
                                        {{ __('Current') }}
                                    </span>
                                </div>
                                <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $account->email }}
                                </p>
                            </div>

                            <div
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-zinc-900 text-white dark:bg-white dark:text-zinc-900">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                    <path fill-rule="evenodd"
                                        d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    @else
                        <!-- Other Account -->
                        <form method="POST" action="{{ route('switch_account.switch') }}">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $account->id }}">
                            <button type="submit"
                                class="group relative flex w-full items-center gap-4 rounded-xl border border-zinc-200 bg-white/50 p-4 transition-all hover:border-zinc-300 hover:bg-white hover:shadow-sm dark:border-zinc-800 dark:bg-zinc-900/50 dark:hover:border-zinc-700 dark:hover:bg-zinc-900">
                                <div
                                    class="relative flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                                    <span
                                        class="text-lg font-medium text-zinc-900 dark:text-white group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">{{ $account->initials() }}</span>
                                </div>

                                <div class="flex-1 min-w-0 text-start">
                                    <p class="truncate text-sm font-semibold text-zinc-900 dark:text-white">
                                        {{ $account->name }}
                                    </p>
                                    <p class="truncate text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $account->email }}
                                    </p>
                                </div>

                                <div
                                    class="invisible flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-zinc-400 group-hover:visible group-hover:bg-zinc-100 group-hover:text-zinc-600 dark:group-hover:bg-zinc-800 dark:group-hover:text-zinc-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </div>
                            </button>
                        </form>
                    @endif
                @endforeach

                <!-- Add Account Button -->
                <div class="pt-2">
                    <a href="{{ route('auth.add_account') }}"
                        class="group relative flex w-full items-center gap-4 rounded-xl border border-dashed border-zinc-300 p-4 transition-all hover:border-zinc-400 hover:bg-zinc-50 dark:border-zinc-700 dark:hover:border-zinc-600 dark:hover:bg-zinc-800/50">
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-zinc-50 text-zinc-500 transition-colors group-hover:bg-white group-hover:text-zinc-900 dark:bg-zinc-800 dark:text-zinc-400 dark:group-hover:bg-zinc-700 dark:group-hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>

                        <div class="text-start">
                            <p
                                class="text-sm font-semibold text-zinc-900 dark:text-white group-hover:text-zinc-900 dark:group-hover:text-white">
                                {{ __('Add another account') }}
                            </p>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('Log in to another account') }}
                            </p>
                        </div>
                    </a>
                </div>

                <!-- Logout All/Current -->
                <form method="POST" action="{{ route('switch_account.logout') }}">
                    @csrf
                    <button type="submit"
                        class="mt-4 w-full rounded-lg px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 transition-colors">
                        {{ __('Log out of this account') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>