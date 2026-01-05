<?php

use App\Models\Blog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public array $selected = [];
    public bool $selectAll = false;
    public ?int $deleteId = null;

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = Auth::user()->blogs()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function setDeleteId(int $id)
    {
        $this->deleteId = $id;
    }

    public function delete()
    {
        if (!$this->deleteId) return;

        $blog = Blog::find($this->deleteId);
        
        if ($blog && $blog->user_id === Auth::id()) {
            $blog->clearMediaCollection('featured_image');
            $blog->delete();
        }

        $this->deleteId = null;
    }

    public function bulkDelete()
    {
        $blogs = Blog::whereIn('id', $this->selected)
            ->where('user_id', Auth::id())
            ->get();

        foreach ($blogs as $blog) {
            $blog->clearMediaCollection('featured_image');
            $blog->delete();
        }

        $this->selected = [];
        $this->selectAll = false;
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
        <div class="flex items-center gap-2">
            @if(count($selected) > 0)
                <flux:modal.trigger name="confirm-bulk-delete">
                    <flux:button variant="danger" icon="trash">
                        Delete ({{ count($selected) }})
                    </flux:button>
                </flux:modal.trigger>
            @endif
            <flux:button :href="route('dashboard.my-blogs.create')" wire:navigate variant="primary" icon="plus">
                New Post
            </flux:button>
        </div>
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
    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
        {{-- Select All Header --}}
        @if($blogs->count() > 0)
            <div class="flex items-center gap-4 p-4 bg-zinc-50 dark:bg-zinc-900/50">
                <flux:checkbox wire:model.live="selectAll" />
                <flux:text size="sm" class="text-zinc-500">
                    @if($selectAll)
                        All {{ count($selected) }} blogs selected
                    @else
                        Select all
                    @endif
                </flux:text>
            </div>
        @endif

        @forelse($blogs as $blog)
            <div class="flex items-center justify-between p-4 {{ in_array((string)$blog->id, $selected) ? 'bg-blue-500/10 dark:bg-blue-500/20 ring-1 ring-inset ring-blue-500/20' : '' }}">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <flux:checkbox wire:model.live="selected" value="{{ $blog->id }}" />

                    @if($blog->hasMedia('featured_image'))
                        <img src="{{ $blog->getFirstMediaUrl('featured_image', 'thumbnail') }}"
                            class="w-16 h-12 object-cover rounded-lg flex-shrink-0">
                    @else
                        <div class="w-16 h-12 bg-zinc-100 dark:bg-zinc-700 rounded-lg flex items-center justify-center flex-shrink-0">
                            <flux:icon name="document-text" variant="outline" class="size-5 text-zinc-400" />
                        </div>
                    @endif

                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <flux:link :href="route('dashboard.my-blogs.edit', $blog)" wire:navigate class="font-medium truncate">
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
                    <flux:button :href="route('dashboard.my-blogs.edit', $blog)" wire:navigate size="sm" variant="ghost" icon="pencil-square" />
                    <flux:modal.trigger name="confirm-delete">
                        <flux:button 
                            wire:click="setDeleteId({{ $blog->id }})" 
                            size="sm" 
                            variant="ghost" 
                            icon="trash" 
                            class="text-red-500 hover:text-red-600" 
                        />
                    </flux:modal.trigger>
                </div>
            </div>
        @empty
            <div class="text-center py-16">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-zinc-100 dark:bg-zinc-700 mb-4">
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

    {{-- Delete Single Modal --}}
    <flux:modal name="confirm-delete" class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Blog</flux:heading>
                <flux:text class="mt-2">Are you sure you want to delete this blog? This action cannot be undone.</flux:text>
            </div>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="delete" variant="danger" x-on:click="$flux.modal('confirm-delete').close()">Delete</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Bulk Delete Modal --}}
    <flux:modal name="confirm-bulk-delete" class="max-w-md">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete {{ count($selected) }} Blogs</flux:heading>
                <flux:text class="mt-2">Are you sure you want to delete these blogs? This action cannot be undone.</flux:text>
            </div>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="bulkDelete" variant="danger" x-on:click="$flux.modal('confirm-bulk-delete').close()">Delete All</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
