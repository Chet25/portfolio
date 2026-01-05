<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-col gap-6 p-6 bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-950 dark:to-gray-900 rounded-2xl">

        {{-- Top Hero Section --}}
        <div class="relative flex items-center justify-between p-6 rounded-2xl shadow-lg bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Hi, I’m Chetan 👋
                </h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Web Developer | Building seamless digital experiences with Laravel, React & Tailwind.
                </p>
            </div>

            {{-- Theme Toggle --}}
            <div class="flex items-center gap-2" x-data>
                <flux:button x-show="$flux.appearance === 'light'" x-on:click="$flux.appearance = 'dark'" icon="sun" variant="ghost" class="text-zinc-500 dark:text-zinc-400" />
                <flux:button x-show="$flux.appearance === 'dark'" x-on:click="$flux.appearance = 'system'" icon="moon" variant="ghost" class="text-zinc-500 dark:text-zinc-400" />
                <flux:button x-show="$flux.appearance === 'system'" x-on:click="$flux.appearance = 'light'" icon="computer-desktop" variant="ghost" class="text-zinc-500 dark:text-zinc-400" />
            </div>
        </div>

        {{-- Dashboard Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            {{-- About Me --}}
            <div class="p-6 rounded-2xl shadow-md bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg hover:scale-[1.02] transition">
                <h2 class="text-lg font-semibold">👤 About Me</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    A passionate developer focused on solving real-world problems through clean, efficient code. 
                    Experienced in frontend, backend, and DevOps with a strong eye for design and performance.
                </p>
            </div>

            {{-- Skills --}}
            <div class="p-6 rounded-2xl shadow-md bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg hover:scale-[1.02] transition">
                <h2 class="text-lg font-semibold">⚡ Skills</h2>
                <ul class="mt-2 text-sm space-y-1 text-gray-600 dark:text-gray-400">
                    <li>PHP, Laravel, MySQL, JavaScript</li>
                    <li>React.js, Livewire, FilamentPHP</li>
                    <li>Tailwind CSS, Figma, SEO</li>
                    <li>VPS & Hosting, GitHub, SSH</li>
                </ul>
            </div>

            {{-- Featured Project --}}
            <div class="p-6 rounded-2xl shadow-md bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg hover:scale-[1.02] transition">
                <h2 class="text-lg font-semibold">🚀 Moneytree Realty</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Built a real estate platform with SEO-friendly listings, performance optimization, and 
                    a custom Laravel + Filament admin dashboard.
                </p>
                <a href="https://moneytreerealty.com" target="_blank"
                   class="mt-3 inline-block text-indigo-600 dark:text-indigo-400 text-sm font-medium hover:underline">
                    View Project →
                </a>
            </div>

            {{-- Additional Projects --}}
            <div class="p-6 col-span-1 md:col-span-2 rounded-2xl shadow-md bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg hover:scale-[1.02] transition">
                <h2 class="text-lg font-semibold">🌌 Space Tourism Website</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    A responsive React.js + Tailwind CSS project featuring dynamic routing, smooth animations, and 
                    interactive 3D planets, designed from Figma mockups.
                </p>
                <a href="https://chet-space-tourism.vercel.app/destination" target="_blank"
                   class="mt-3 inline-block text-indigo-600 dark:text-indigo-400 text-sm font-medium hover:underline">
                    Explore Project →
                </a>
            </div>

            {{-- Contact Me --}}
            <div class="p-6 rounded-2xl shadow-md bg-white/70 dark:bg-gray-800/70 backdrop-blur-lg hover:scale-[1.02] transition">
                <h2 class="text-lg font-semibold">📬 Contact</h2>
                <ul class="mt-2 text-sm space-y-1 text-gray-600 dark:text-gray-400">
                    <li>📧 <a href="mailto:chetdev26@gmail.com" class="hover:underline">chetdev26@gmail.com</a></li>
                    <li>📱 <a href="tel:8920793869" class="hover:underline">+91 8920793869</a></li>
                    <li>🔗 <a href="https://www.linkedin.com/in/chet25/" target="_blank" class="hover:underline">LinkedIn</a></li>
                    <li>💻 <a href="https://github.com/Chet25" target="_blank" class="hover:underline">GitHub</a></li>
                </ul>
            </div>
        </div>
    </div>
</x-layouts.app>
