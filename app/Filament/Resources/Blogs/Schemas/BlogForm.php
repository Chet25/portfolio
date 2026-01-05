<?php

namespace App\Filament\Resources\Blogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                // Title - full width
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(8)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => 
                        $set('slug', Str::slug($state))
                    ),

                // Status & Review Status - side by side
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'scheduled' => 'Scheduled',
                        'archived' => 'Archived',
                    ])
                    ->required()
                    ->default('draft')
                    ->columnSpan(2),

                Select::make('review_status')
                    ->options([
                        'pending_review' => 'Pending Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->default('pending_review')
                    ->columnSpan(2),

                // Slug - takes most of row
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->columnSpan(6),

                // Publish date
                DateTimePicker::make('published_at')
                    ->label('Publish Date')
                    ->columnSpan(3),

                // Featured toggle
                Toggle::make('is_featured')
                    ->label('Featured')
                    ->inline(false)
                    ->columnSpan(3),

                // Excerpt
                Textarea::make('excerpt')
                    ->rows(2)
                    ->columnSpan(12),

                // Content - full width
                Textarea::make('content')
                    ->required()
                    ->rows(12)
                    ->columnSpan(12),

                // Featured Image
                Section::make('Featured Image')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('featured_image')
                            ->collection('featured_image')
                            ->image()
                            ->imageEditor()
                            ->responsiveImages()
                            ->label(''),
                    ])
                    ->columnSpan(6),

                // Author & Editor
                Section::make('Author & Editor')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('author', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Author'),
                        Select::make('editor_id')
                            ->relationship('editor', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Editor'),
                    ])
                    ->columns(2)
                    ->columnSpan(6),

                // SEO
                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->maxLength(70)
                            ->helperText('50-60 characters recommended'),
                        Textarea::make('meta_description')
                            ->rows(2)
                            ->maxLength(160)
                            ->helperText('150-160 characters recommended'),
                    ])
                    ->columns(2)
                    ->columnSpan(12),

                // Stats - read only
                Section::make('Statistics')
                    ->schema([
                        TextInput::make('views')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        TextInput::make('likes')
                            ->numeric()
                            ->default(0)
                            ->disabled(),
                        TextInput::make('reading_time')
                            ->numeric()
                            ->suffix('min')
                            ->disabled(),
                    ])
                    ->columns(3)
                    ->columnSpan(12),
            ]);
    }
}
