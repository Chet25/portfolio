<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $activeEndpoint = '';
    public string $response = '';
    public array $blogs = [];
    public bool $loading = false;
    public string $slug = '';
    public int $limit = 5;
    public int $statusCode = 0;

    public function fetchBlogs()
    {
        $this->activeEndpoint = '/api/blogs';
        $this->loading = true;
        
        $response = \Http::get(url('/api/blogs'));
        $this->statusCode = $response->status();
        $json = $response->json();
        $this->response = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $this->blogs = $json['data'] ?? [];
        $this->loading = false;
    }

    public function fetchFeatured()
    {
        $this->activeEndpoint = '/api/blogs/featured?limit=' . $this->limit;
        $this->loading = true;
        
        $response = \Http::get(url('/api/blogs/featured'), ['limit' => $this->limit]);
        $this->statusCode = $response->status();
        $json = $response->json();
        $this->response = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $this->blogs = $json['data'] ?? [];
        $this->loading = false;
    }

    public function fetchSingle()
    {
        if (empty($this->slug)) {
            $this->statusCode = 0;
            $this->response = json_encode(['error' => 'Please enter a slug'], JSON_PRETTY_PRINT);
            $this->blogs = [];
            return;
        }
        
        $this->activeEndpoint = '/api/blogs/' . $this->slug;
        $this->loading = true;
        
        $response = \Http::get(url('/api/blogs/' . $this->slug));
        $this->statusCode = $response->status();
        $json = $response->json();
        $this->response = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $this->blogs = isset($json['data']) ? [$json['data']] : [];
        $this->loading = false;
    }
}; ?>

<div class="max-w-6xl mx-auto py-10 px-6">
    {{-- Header --}}
    <div class="mb-8">
        <flux:heading size="xl">API Playground</flux:heading>
        <flux:text class="text-zinc-500 mt-1">Test the blog API endpoints live</flux:text>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Endpoints Column --}}
        <div class="space-y-4">
            <flux:text size="sm" class="text-zinc-400 font-medium">Endpoints</flux:text>

            {{-- GET /api/blogs --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <flux:badge color="green" size="sm">GET</flux:badge>
                        <code class="text-sm font-mono truncate">/api/blogs</code>
                    </div>
                    <flux:button wire:click="fetchBlogs" size="sm" icon="play" class="w-full sm:w-auto">Run</flux:button>
                </div>
                <flux:text size="sm" class="text-zinc-500 mt-2">List all published blogs</flux:text>
            </div>

            {{-- GET /api/blogs/featured --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <flux:badge color="green" size="sm">GET</flux:badge>
                        <code class="text-sm font-mono truncate">/api/blogs/featured</code>
                    </div>
                    <flux:button wire:click="fetchFeatured" size="sm" icon="play" class="w-full sm:w-auto">Run</flux:button>
                </div>
                <div class="flex items-center gap-3 mt-3">
                    <flux:input type="number" wire:model="limit" min="1" max="10" label="Limit" size="sm" class="w-20" />
                </div>
            </div>

            {{-- GET /api/blogs/{slug} --}}
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <flux:badge color="green" size="sm">GET</flux:badge>
                        <code class="text-sm font-mono truncate">/api/blogs/<span class="text-amber-500">{slug}</span></code>
                    </div>
                    <flux:button wire:click="fetchSingle" size="sm" icon="play" class="w-full sm:w-auto">Run</flux:button>
                </div>
                <div class="mt-3">
                    <flux:input type="text" wire:model="slug" placeholder="Enter blog slug..." size="sm" class="w-full sm:w-56" />
                </div>
            </div>
        </div>

        {{-- Response Column --}}
        <div class="flex flex-col">
            <div class="flex items-center justify-between mb-3">
                <flux:text size="sm" class="text-zinc-400 font-medium">Response</flux:text>
                @if($activeEndpoint)
                    <div class="flex items-center gap-2">
                        @if($statusCode)
                            <flux:badge :color="$statusCode === 200 ? 'green' : 'red'" size="sm">
                                {{ $statusCode }}
                            </flux:badge>
                        @endif
                        <code class="text-xs bg-zinc-100 dark:bg-zinc-700 px-2 py-1 rounded font-mono">
                            {{ $activeEndpoint }}
                        </code>
                    </div>
                @endif
            </div>

            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden h-[400px]">
                @if($loading)
                    <div class="h-full flex flex-col items-center justify-center bg-zinc-50 dark:bg-zinc-900">
                        <flux:icon name="arrow-path" class="size-6 animate-spin text-zinc-400" />
                        <flux:text class="mt-3 text-zinc-500">Loading...</flux:text>
                    </div>
                @elseif($response)
                    <div 
                        x-data="{ 
                            json: $wire.entangle('response'),
                            highlighted: '',
                            syntaxHighlight(json) {
                                if (!json) return '';
                                json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                                return json.replace(/(&quot;(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\&quot;])*&quot;(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                                    let cls = 'text-amber-600 dark:text-amber-400';
                                    if (/^&quot;/.test(match)) {
                                        if (/:$/.test(match)) {
                                            cls = 'text-sky-600 dark:text-sky-400';
                                        } else {
                                            cls = 'text-emerald-600 dark:text-emerald-400';
                                        }
                                    } else if (/true|false/.test(match)) {
                                        cls = 'text-purple-600 dark:text-purple-400';
                                    } else if (/null/.test(match)) {
                                        cls = 'text-zinc-500';
                                    }
                                    return '<span class=\'' + cls + '\'>' + match + '</span>';
                                });
                            }
                        }"
                        x-effect="highlighted = syntaxHighlight(json)"
                        class="h-full overflow-auto bg-zinc-50 dark:bg-zinc-900"
                    >
                        <pre class="p-4 text-sm font-mono leading-relaxed whitespace-pre-wrap break-words" x-html="highlighted"></pre>
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center bg-zinc-50 dark:bg-zinc-900">
                        <flux:icon name="command-line" class="size-10 text-zinc-300 dark:text-zinc-600" />
                        <flux:text class="mt-3 text-zinc-500">Click "Run" to see the response</flux:text>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Blog Cards Preview --}}
    @if(count($blogs) > 0)
        <div class="mt-10">
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="lg">Preview</flux:heading>
                <flux:badge color="zinc">{{ count($blogs) }} {{ Str::plural('blog', count($blogs)) }}</flux:badge>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach($blogs as $blog)
                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 overflow-hidden group hover:shadow-md transition-shadow duration-200">
                        {{-- Image --}}
                        <div class="aspect-[4/3] bg-zinc-100 dark:bg-zinc-700 overflow-hidden">
                            @if(!empty($blog['thumbnail']))
                                <img src="{{ $blog['thumbnail'] }}" alt="{{ $blog['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <flux:icon name="photo" class="size-5 text-zinc-400" />
                                </div>
                            @endif
                        </div>
                        
                        {{-- Content --}}
                        <div class="p-2.5">
                            <div class="flex flex-wrap items-center gap-1 mb-1.5">
                                <flux:badge :color="($blog['status'] ?? '') === 'published' ? 'green' : 'zinc'" size="sm">
                                    {{ ucfirst($blog['status'] ?? 'draft') }}
                                </flux:badge>
                                @if($blog['is_featured'] ?? false)
                                    <flux:badge color="amber" size="sm">★</flux:badge>
                                @endif
                            </div>
                            
                            <h3 class="text-xs font-medium text-zinc-900 dark:text-white line-clamp-2 mb-1">
                                {{ $blog['title'] ?? 'Untitled' }}
                            </h3>
                            
                            <div class="flex items-center justify-between text-[10px] text-zinc-400">
                                <span class="truncate max-w-[60px]">{{ $blog['author']['name'] ?? 'Unknown' }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="flex items-center gap-0.5">
                                        <flux:icon name="eye" class="size-2.5" />
                                        {{ number_format($blog['views'] ?? 0) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
