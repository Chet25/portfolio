<?php

use App\Models\Project;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app', ['title' => 'Portfolio'])] class extends Component {
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

<div class="p-6 md:p-12 max-w-7xl mx-auto">
    
<!--  -->

</div>
