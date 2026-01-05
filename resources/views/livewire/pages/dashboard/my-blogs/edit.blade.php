<?php

use App\Models\Blog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    public Blog $blog;

    #[Validate('required|min:3|max:255')]
    public string $title = '';
    
    #[Validate('required')]
    public string $content = '';
    
    #[Validate('nullable|image|max:2048')]
    public $featured_image;

    public ?string $existing_image_url = null;

    #[Validate('nullable|string|max:500')]
    public string $excerpt = '';

    public function mount(Blog $blog)
    {
        if ($blog->user_id !== Auth::id()) {
            abort(403);
        }

        $this->blog = $blog;
        $this->title = $blog->title;
        $this->content = $blog->content;
        $this->excerpt = $blog->excerpt ?? '';
        $this->existing_image_url = $blog->getFirstMediaUrl('featured_image', 'medium');
    }

    public function removeExistingImage()
    {
        $this->blog->clearMediaCollection('featured_image');
        $this->existing_image_url = null;
    }

    public function update($action = 'draft')
    {
        $this->validate();

        $this->blog->update([
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'review_status' => $action === 'submit' ? 'pending_review' : $this->blog->review_status,
        ]);

        if ($this->featured_image) {
            $this->blog->clearMediaCollection('featured_image');
            $this->blog->addMedia($this->featured_image->getRealPath())
                ->usingFileName($this->featured_image->getClientOriginalName())
                ->toMediaCollection('featured_image');
        }

        session()->flash('status', 'Blog updated successfully.');

        return redirect()->route('dashboard.my-blogs.index');
    }
}; ?>

<div class="max-w-4xl mx-auto py-8 px-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <flux:button :href="route('dashboard.my-blogs.index')" wire:navigate variant="ghost" icon="arrow-left" size="sm" />
            <div>
                <flux:heading size="xl">Edit Blog</flux:heading>
                <flux:text class="text-zinc-500">Update your blog post</flux:text>
            </div>
        </div>

        {{-- Status Badges --}}
        <div class="flex items-center gap-2">
            <flux:badge :color="$blog->status === 'published' ? 'green' : 'zinc'">
                {{ ucfirst($blog->status) }}
            </flux:badge>
            
            @if($blog->review_status === 'pending_review')
                <flux:badge color="amber">Pending Review</flux:badge>
            @elseif($blog->review_status === 'approved')
                <flux:badge color="green">Approved</flux:badge>
            @elseif($blog->review_status === 'rejected')
                <flux:badge color="red">Rejected</flux:badge>
            @endif
        </div>
    </div>

    <form wire:submit="update('submit')" class="space-y-6">
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6 space-y-6">
            {{-- Title --}}
            <flux:input 
                wire:model="title" 
                label="Title" 
                placeholder="Enter a compelling title..."
            />

            {{-- Featured Image --}}
            <flux:field>
                <flux:label>Featured Image</flux:label>
                <flux:description>Upload a cover image for your blog post (max 2MB)</flux:description>
                
                @if($featured_image)
                    <div class="relative mt-2 mb-4">
                        <img src="{{ $featured_image->temporaryUrl() }}" class="w-full h-48 object-cover rounded-lg">
                        <flux:button 
                            type="button" 
                            wire:click="$set('featured_image', null)" 
                            size="sm" 
                            variant="filled"
                            icon="x-mark"
                            class="absolute top-2 right-2"
                        />
                    </div>
                @elseif($existing_image_url)
                    <div class="relative mt-2 mb-4">
                        <img src="{{ $existing_image_url }}" class="w-full h-48 object-cover rounded-lg">
                        <flux:button 
                            type="button" 
                            wire:click="removeExistingImage" 
                            size="sm" 
                            variant="filled"
                            icon="x-mark"
                            class="absolute top-2 right-2"
                        />
                    </div>
                @endif
                
                <flux:input type="file" wire:model="featured_image" accept="image/*" />
                <flux:error name="featured_image" />
            </flux:field>

            {{-- Excerpt --}}
            <flux:textarea 
                wire:model="excerpt" 
                label="Excerpt" 
                description="A brief summary that appears in blog listings"
                placeholder="Write a short description..."
                rows="3"
            />

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

        {{-- Footer --}}
        <div class="flex items-center justify-between">
            <flux:text size="sm" class="text-zinc-500">
                Last updated {{ $blog->updated_at->diffForHumans() }}
            </flux:text>
            
            <div class="flex items-center gap-3">
                <flux:button type="button" wire:click="update('draft')" variant="ghost">
                    Save Draft
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Update & Submit
                </flux:button>
            </div>
        </div>
    </form>
</div>
