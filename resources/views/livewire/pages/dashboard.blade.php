<?php

use App\Models\Project;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app', ['title' => 'Home'])] class extends Component {
    public function with(): array
    {
        return [
            'featured_projects' => Project::with('tags')
                ->where('status', 'completed')
                ->where('is_featured', true)
                ->orderBy('sort_order')
                ->take(3)
                ->get(),
        ];
    }
}; ?>

<div class="flex flex-col gap-12 p-6 md:p-12 max-w-7xl mx-auto">

    {{-- Hero Section --}}
    <div class="relative flex flex-col-reverse md:flex-row items-center justify-between gap-12 py-12">
        <div class="flex-1 space-y-6">
            <div class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 ring-1 ring-inset ring-indigo-700/10 dark:ring-indigo-700/30">
                Available for hire
            </div>
            
            <h1 class="text-5xl md:text-7xl font-bold tracking-tight text-zinc-900 dark:text-white">
                Building <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500">digital products</span> that matter.
            </h1>
            
            <p class="text-xl text-zinc-600 dark:text-zinc-400 max-w-2xl leading-relaxed">
                I'm <span class="font-semibold text-zinc-900 dark:text-white">Chetan</span>, a Full Stack Developer specializing in 
                <span class="text-indigo-600 dark:text-indigo-400">Laravel</span>, 
                <span class="text-cyan-600 dark:text-cyan-400">React</span>, and 
                <span class="text-emerald-600 dark:text-emerald-400">Cloud Architecture</span>. 
                I turn complex problems into simple, beautiful software.
            </p>

            <div class="flex items-center gap-4 pt-4">
                <flux:button href="/projects" variant="primary" icon="briefcase">View My Work</flux:button>
                <flux:button href="https://github.com/Chet25" target="_blank" variant="ghost" icon="code-bracket-square">GitHub</flux:button>
                <flux:button href="https://linkedin.com/in/chet25" target="_blank" variant="ghost" icon="link">LinkedIn</flux:button>
            </div>
        </div>

        {{-- Hero Visual / Avatar --}}
        <div class="relative shrink-0">
            <div class="absolute -inset-4 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full opacity-20 blur-3xl dark:opacity-40"></div>
            <div class="relative w-48 h-48 md:w-64 md:h-64 rounded-full overflow-hidden border-4 border-white dark:border-zinc-800 shadow-2xl">
                {{-- Replace with your actual avatar URL or keep this placeholder --}}
                <img src="https://ui-avatars.com/api/?name=Chetan&background=4f46e5&color=fff&size=256" 
                     alt="Chetan" 
                     class="w-full h-full object-cover">
            </div>
            
            {{-- Floating Badge --}}
            <div class="absolute -bottom-4 -right-4 bg-white dark:bg-zinc-800 p-3 rounded-xl shadow-lg border border-zinc-100 dark:border-zinc-700 flex items-center gap-3 animate-bounce-slow">
                <div class="flex -space-x-2">
                    <span class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-xs text-white border-2 border-white dark:border-zinc-800">L</span>
                    <span class="w-8 h-8 rounded-full bg-cyan-500 flex items-center justify-center text-xs text-white border-2 border-white dark:border-zinc-800">R</span>
                    <span class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-xs text-white border-2 border-white dark:border-zinc-800">V</span>
                </div>
                <div class="text-xs font-medium">
                    <div class="text-zinc-900 dark:text-white">Stack</div>
                    <div class="text-zinc-500">Expert</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tech Stack Marquee (Simplified) --}}
    <div class="py-8 border-y border-zinc-100 dark:border-zinc-800/50">
        <p class="text-center text-sm font-medium text-zinc-500 mb-6 uppercase tracking-wider">Technologies I work with</p>
        <div class="flex flex-wrap justify-center gap-8 md:gap-12 opacity-70 grayscale hover:grayscale-0 transition-all duration-500">
            {{-- Icons: You can replace these with SVGs --}}
            <span class="text-xl font-bold flex items-center gap-2"><flux:icon name="command-line" class="size-6" /> Laravel</span>
            <span class="text-xl font-bold flex items-center gap-2"><flux:icon name="globe-alt" class="size-6" /> React</span>
            <span class="text-xl font-bold flex items-center gap-2"><flux:icon name="server" class="size-6" /> Docker</span>
            <span class="text-xl font-bold flex items-center gap-2"><flux:icon name="circle-stack" class="size-6" /> MySQL</span>
            <span class="text-xl font-bold flex items-center gap-2"><flux:icon name="cloud" class="size-6" /> AWS</span>
        </div>
    </div>

    {{-- Featured Projects --}}
    <section>
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Featured Work</h2>
            <flux:button href="/projects" variant="ghost" icon="arrow-right" icon-position="right">View all projects</flux:button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($featured_projects as $project)
                <a href="{{ $project->url_live ?? '#' }}" target="_blank" class="group relative flex flex-col bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden hover:border-indigo-500/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="aspect-[4/3] bg-zinc-200 dark:bg-zinc-800 overflow-hidden relative">
                        @if($project->hasMedia('thumbnail'))
                            <img src="{{ $project->getFirstMediaUrl('thumbnail', 'card') }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-zinc-400">No Image</div>
                        @endif
                    </div>
                    <div class="p-6 flex flex-col flex-1">
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2">{{ $project->title }}</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 line-clamp-2 mb-4">{{ $project->description }}</p>
                        <div class="mt-auto flex flex-wrap gap-2">
                            @foreach($project->tags->take(3) as $tag)
                                <span class="text-xs px-2 py-1 rounded-md bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-3 p-12 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl">
                    <p class="text-zinc-500">No featured projects yet.</p>
                    @auth
                        <flux:button href="/admin" variant="subtle" class="mt-4">Add Project</flux:button>
                    @endauth
                </div>
            @endforelse
        </div>
    </section>

    {{-- Contact / Call to Action --}}
    <section class="rounded-3xl bg-zinc-900 dark:bg-zinc-950 p-8 md:p-12 text-center md:text-left relative overflow-hidden">
        {{-- Background Pattern --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-600 rounded-full blur-3xl opacity-20 -mr-16 -mt-16"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
            <div>
                <h2 class="text-3xl font-bold text-white mb-2">Ready to start a project?</h2>
                <p class="text-zinc-400 max-w-lg">I'm currently accepting new freelance opportunities. Let's discuss how I can help your business grow.</p>
            </div>
            <div class="flex gap-4">
                <flux:button href="mailto:chetdev26@gmail.com" variant="primary" class="!bg-white !text-zinc-900 hover:!bg-zinc-100 border-none">
                    Get in touch
                </flux:button>
                <flux:button href="/resume.pdf" variant="outline" class="border-zinc-700 text-white hover:bg-zinc-800 hover:border-zinc-600">
                    Download Resume
                </flux:button>
            </div>
        </div>
    </section>

</div>