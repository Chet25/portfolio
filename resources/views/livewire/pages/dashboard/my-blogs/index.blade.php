<?php

use App\Models\Blog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public function delete(Blog $blog)
    {
        if ($blog->user_id !== Auth::id()) {
            abort(403);
        }

        $blog->delete();
    }

    public function with(): array
    {
        return [
            'blogs' => Auth::user()->blogs()->latest()->paginate(10),
            'stats' => [
                'total' => Auth::user()->blogs()->count(),
                'published' => Auth::user()->blogs()->where('status', 'published')->count(),
                'drafts' => Auth::user()->blogs()->where('status', 'draft')->count(),
                'pending' => Auth::user()->blogs()->where('review_status', 'pending_review')->count(),
            ]
        ];
    }
}; ?>

<div class="max-w-5xl mx-auto py-10 px-6 space-y-2">
    {{-- Header --}}
    <div class="flex items-start justify-between mb-10">
        <div>
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">My Blogs</h1>
            <p class="text-zinc-500 dark:text-zinc-400 mt-1">Manage and track your blog posts</p>
        </div>
        <flux:button :href="route('dashboard.my-blogs.create')" wire:navigate variant="primary" icon="plus">
            New Post
        </flux:button>
    </div>

    {{-- Stats --}}
    <div class="flex flex-wrap gap-4 mb-10">
        <div class="text-center py-5 px-8 rounded-xl bg-blue-500 shadow-sm">
            <h3 class="text-2xl font-bold text-white">{{ $stats['total'] }}</h3>
            <p class="text-sm text-blue-100 mt-1">Total Posts</p>
        </div>
        <div class="text-center py-5 px-8 rounded-xl bg-emerald-500 shadow-sm">
            <h3 class="text-2xl font-bold text-white">{{ $stats['published'] }}</h3>
            <p class="text-sm text-emerald-100 mt-1">Published</p>
        </div>
        <div class="text-center py-5 px-8 rounded-xl bg-zinc-500 shadow-sm">
            <h3 class="text-2xl font-bold text-white">{{ $stats['drafts'] }}</h3>
            <p class="text-sm text-zinc-200 mt-1">Drafts</p>
        </div>
        <div class="text-center py-5 px-8 rounded-xl bg-amber-500 shadow-sm">
            <h3 class="text-2xl font-bold text-white">{{ $stats['pending'] }}</h3>
            <p class="text-sm text-amber-100 mt-1">Pending Review</p>
        </div>
    </div>

    {{-- Blog List --}}
    <div
        class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
        @forelse($blogs as $blog)
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    @if($blog->featured_image)
                        <img src="{{ asset('storage/' . $blog->featured_image) }}"
                            class="w-16 h-12 object-cover rounded-lg flex-shrink-0">
                    @else
                        <div
                            class="w-16 h-12 bg-zinc-100 dark:bg-zinc-700 rounded-lg flex items-center justify-center flex-shrink-0">
                            <flux:icon name="document-text" variant="outline" class="size-5 text-zinc-400" />
                        </div>
                    @endif

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <flux:link :href="route('dashboard.my-blogs.edit', $blog)" wire:navigate
                                class="font-medium truncate">
                                {{ $blog->title }}
                            </flux:link>

                            <flux:badge size="sm" :color="$blog->status === 'published' ? 'green' : 'zinc'">
                                {{ ucfirst($blog->status) }}
                            </flux:badge>

                            @if($blog->review_status === 'pending_review')
                                <flux:badge size="sm" color="amber">Pending</flux:badge>
                            @elseif($blog->review_status === 'rejected')
                                <flux:badge size="sm" color="red">Rejected</flux:badge>
                            @endif
                        </div>

                        <flux:text size="sm" class="text-zinc-500 truncate">
                            {{ $blog->excerpt ?? Str::limit(strip_tags($blog->content), 80) }}
                        </flux:text>

                        <flux:text size="sm" class="text-zinc-400 mt-1">
                            Updated {{ $blog->updated_at->diffForHumans() }}
                            @if($blog->views)
                                · {{ number_format($blog->views) }} views
                            @endif
                        </flux:text>
                    </div>
                </div>

                <div class="flex items-center gap-1 ml-4">
                    <flux:button :href="route('dashboard.my-blogs.edit', $blog)" wire:navigate size="sm" variant="ghost"
                        icon="pencil-square" />
                    <flux:button wire:click="delete({{ $blog->id }})"
                        wire:confirm="Are you sure you want to delete this blog?" size="sm" variant="ghost" icon="trash"
                        class="text-red-500 hover:text-red-600" />
                </div>
            </div>
        @empty
            <div class="text-center py-16">
                <div
                    class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-zinc-100 dark:bg-zinc-700 mb-4">
                    <flux:icon name="document-text" variant="outline" class="size-6 text-zinc-400" />
                </div>
                <flux:heading size="lg">No blogs yet</flux:heading>
                <flux:text class="text-zinc-500 mb-6">Get started by creating your first blog post</flux:text>
                <flux:button :href="route('dashboard.my-blogs.create')" wire:navigate variant="primary" icon="plus">
                    Create your first post
                </flux:button>
            </div>
        @endforelse
    </div>

    @if($blogs->hasPages())
        <div class="mt-6">
            {{ $blogs->links() }}
        </div>
    @endif
</div>