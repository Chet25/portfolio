<?php

namespace App\Filament\Resources\Blogs\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BlogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('featured_image')
                    ->collection('featured_image')
                    ->conversion('thumbnail')
                    ->circular()
                    ->size(40)
                    ->label(''),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->title)
                    ->weight('medium'),

                TextColumn::make('author.name')
                    ->label('Author')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        'scheduled' => 'info',
                        'archived' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('review_status')
                    ->label('Review')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending_review' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending_review' => 'Pending',
                        default => ucfirst($state),
                    }),

                ToggleColumn::make('is_featured')
                    ->label('Featured')
                    ->sortable(),

                TextColumn::make('views')
                    ->numeric()
                    ->sortable()
                    ->label('Views')
                    ->alignEnd(),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->date('M j, Y')
                    ->sortable()
                    ->placeholder('—')
                    ->color('gray'),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'scheduled' => 'Scheduled',
                        'archived' => 'Archived',
                    ]),
                SelectFilter::make('review_status')
                    ->label('Review Status')
                    ->options([
                        'pending_review' => 'Pending Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('is_featured')
                    ->label('Featured')
                    ->options([
                        '1' => 'Featured',
                        '0' => 'Not Featured',
                    ]),
            ])
            ->recordActions([
                Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-arrow-up-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'published')
                    ->requiresConfirmation()
                    ->modalHeading('Publish Blog')
                    ->modalDescription('Are you sure you want to publish this blog?')
                    ->action(function ($record) {
                        $record->status = 'published';
                        $record->published_at = $record->published_at ?? now();
                        $record->save();
                    }),

                Action::make('unpublish')
                    ->label('Unpublish')
                    ->icon('heroicon-o-arrow-down-circle')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'published')
                    ->requiresConfirmation()
                    ->modalHeading('Unpublish Blog')
                    ->modalDescription('Are you sure you want to unpublish this blog? It will be saved as a draft.')
                    ->action(function ($record) {
                        $record->status = 'draft';
                        $record->save();
                    }),

                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->paginated([10, 25, 50]);
    }
}
