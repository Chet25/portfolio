<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

use App\Filament\Forms\Components\EditorJs;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(12)
            ->components([
                Section::make('Core Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(8)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => 
                                $set('slug', Str::slug($state))
                            ),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->columnSpan(4),
                        Textarea::make('description')
                            ->required()
                            ->rows(3)
                            ->placeholder('Short summary for the project card...')
                            ->columnSpanFull(),
                    ])
                    ->columns(12)
                    ->columnSpanFull(),

                Section::make('Details')
                    ->schema([
                        EditorJs::make('content')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),

                Section::make('Links & Media')
                    ->schema([
                        TextInput::make('url_live')
                            ->url()
                            ->label('Live URL'),
                        TextInput::make('url_repo')
                            ->url()
                            ->label('Repository URL'),
                        SpatieMediaLibraryFileUpload::make('thumbnail')
                            ->collection('thumbnail')
                            ->image()
                            ->imageEditor()
                            ->label('Project Thumbnail'),
                    ])
                    ->columnSpanFull(),

                Section::make('Settings')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'archived' => 'Archived',
                            ])
                            ->default('completed')
                            ->required(),
                        Toggle::make('is_featured')
                            ->label('Featured Project'),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        DatePicker::make('started_at'),
                        DatePicker::make('completed_at'),
                        Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Tech Stack'),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->default(auth()->id())
                            ->label('Owner'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}