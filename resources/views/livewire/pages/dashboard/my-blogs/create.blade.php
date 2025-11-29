<?php

use App\Models\Blog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    #[Validate('required|min:3|max:255')]
    public string $title = '';

    #[Validate('required')]
    public string $content = '';

    #[Validate('nullable|image|max:2048')]
    public $featured_image;

    #[Validate('nullable|string|max:500')]
    public string $excerpt = '';

    public function store($action = 'draft')
    {
        $this->validate();

        $slug = Str::slug($this->title);

        $count = Blog::where('slug', $slug)->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $path = null;
        if ($this->featured_image) {
            $path = $this->featured_image->store('blogs', 'public');
        }

        Auth::user()->blogs()->create([
            'title' => $this->title,
            'slug' => $slug,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'featured_image' => $path,
            'status' => 'draft',
            'review_status' => $action === 'submit' ? 'pending_review' : 'pending_review',
        ]);

        return redirect()->route('dashboard.my-blogs.index');
    }
}; ?>

<div class="max-w-4xl mx-auto py-8 px-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <flux:button :href="route('dashboard.my-blogs.index')" wire:navigate variant="ghost" icon="arrow-left"
                size="sm" />
            <div>
                <flux:heading size="xl">Create New Blog</flux:heading>
                <flux:text class="text-zinc-500">Share your thoughts with the world</flux:text>
            </div>
        </div>
    </div>

    <form wire:submit="store('submit')" class="space-y-6">
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6 space-y-6">
            {{-- Title --}}
            <flux:input wire:model="title" label="Title" placeholder="Enter a compelling title..." />

            {{-- Featured Image --}}
            <flux:field>
                <flux:label>Featured Image</flux:label>
                <flux:description>Upload a cover image for your blog post (max 2MB)</flux:description>

                @if($featured_image)
                    <div class="relative mt-2 mb-4">
                        <img src="{{ $featured_image->temporaryUrl() }}" class="w-full h-48 object-cover rounded-lg">
                        <flux:button type="button" wire:click="$set('featured_image', null)" size="sm" variant="filled"
                            icon="x-mark" class="absolute top-2 right-2" />
                    </div>
                @endif

                <flux:input type="file" wire:model="featured_image" accept="image/*" />
                <flux:error name="featured_image" />
            </flux:field>

            {{-- Excerpt --}}
            <flux:textarea wire:model="excerpt" label="Excerpt"
                description="A brief summary that appears in blog listings" placeholder="Write a short description..."
                rows="3" />

            {{-- Content --}}
            <flux:field>
                <flux:label>Content</flux:label>
                <flux:description>Write your blog post using the editor below</flux:description>
                <div class="mt-2">
                    <x-editor-js wire:model="content" name="content" />
                </div>
                <flux:error name="content" />
            </flux:field>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <flux:button type="button" wire:click="store('draft')" variant="ghost">
                Save Draft
            </flux:button>
            <flux:button type="submit" variant="primary">
                Submit for Review
            </flux:button>
        </div>
    </form>
</div>