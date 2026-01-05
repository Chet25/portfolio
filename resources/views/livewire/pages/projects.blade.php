<?php

use App\Models\Project;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    public function with(): array
    {
        return [
            'projects' => Project::with('tags')
                ->where('status', 'completed')
                ->orderBy('sort_order')
                ->orderByDesc('created_at')
                ->get(),
        ];
    }
}; ?>

<div class="flex flex-col gap-12 p-6 md:p-12 max-w-7xl mx-auto">
    {{-- Hero Section --}}
    <div class="relative overflow-hidden rounded-3xl bg-zinc-900 px-6 py-16 sm:px-12 sm:py-20 text-center shadow-2xl ring-1 ring-white/10">
        <div class="absolute inset-0 opacity-30 bg-[radial-gradient(#4f46e5_1px,transparent_1px)] [background-size:20px_20px]"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-indigo-500 rounded-full blur-3xl opacity-20"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-purple-500 rounded-full blur-3xl opacity-20"></div>
        
        <div class="relative z-10 max-w-2xl mx-auto">
            <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-6xl mb-6">
                My <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Work</span>
            </h1>
            <p class="text-lg md:text-xl text-zinc-300 leading-relaxed">
                A curated collection of my commercial projects, open-source tools, and technical experiments.
            </p>
        </div>
    </div>

    {{-- Projects Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($projects as $project)
            <div class="group relative flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl overflow-hidden hover:shadow-xl hover:border-indigo-500/30 hover:-translate-y-1 transition-all duration-300">
                
                {{-- Project Thumbnail --}}
                <div class="aspect-video w-full bg-zinc-100 dark:bg-zinc-800 overflow-hidden relative">
                    @if($project->hasMedia('thumbnail'))
                        <img src="{{ $project->getFirstMediaUrl('thumbnail', 'card') }}" 
                             alt="{{ $project->title }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    @else
                        <div class="flex items-center justify-center h-full text-zinc-400 bg-zinc-50 dark:bg-zinc-800/50">
                            <flux:icon name="photo" class="size-12 opacity-30" />
                        </div>
                    @endif
                    
                    {{-- Status Badge --}}
                    @if($project->is_featured)
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20 backdrop-blur-md">
                                <flux:icon name="star" variant="solid" class="size-3" /> Featured
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Content --}}
                <div class="flex flex-col flex-1 p-6">
                    <div class="mb-4">
                        <h3 class="text-xl font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            {{ $project->title }}
                        </h3>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400 line-clamp-3">
                            {{ $project->description }}
                        </p>
                    </div>

                    {{-- Tech Stack --}}
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($project->tags->take(4) as $tag)
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-zinc-50 dark:bg-zinc-800/80 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700/50">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                        @if($project->tags->count() > 4)
                            <span class="text-xs text-zinc-400 self-center">+{{ $project->tags->count() - 4 }}</span>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-3 mt-auto pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        @if($project->url_live)
                            <a href="{{ $project->url_live }}" target="_blank" 
                               class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 transition-colors">
                                <flux:icon name="globe-alt" class="size-4" /> Live Demo
                            </a>
                        @endif
                        
                        @if($project->url_repo)
                            <a href="{{ $project->url_repo }}" target="_blank" 
                               class="inline-flex items-center gap-2 text-sm font-medium text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200 transition-colors ml-auto">
                                <flux:icon name="code-bracket" class="size-4" /> Source
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-3xl bg-zinc-50/50 dark:bg-zinc-900/50">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 mb-4 ring-8 ring-white dark:ring-zinc-900">
                    <flux:icon name="beaker" class="size-8 text-zinc-400" />
                </div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white">No projects found</h3>
                <p class="mt-2 text-zinc-500 max-w-sm mx-auto">I'm currently working on some exciting things. Check back soon!</p>
            </div>
        @endforelse
    </div>
</div>