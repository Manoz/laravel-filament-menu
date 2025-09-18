<?php

namespace Novius\LaravelFilamentMenu\Filament\Resources\MenuResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Novius\FilamentRelationNested\Filament\Resources\RelationManagers\TreeRelationManager;
use Novius\FilamentRelationNested\Filament\Tables\Actions\FixTreeAction;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuItemResource;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuResource;
use Novius\LaravelFilamentMenu\Models\MenuItem;

class MenuItemsTreeRelationManager extends TreeRelationManager
{
    protected static string $relationship = 'items';

    public function table(Table $table): Table
    {
        return MenuManager::getMenuItemResource()::table($table)
            ->columns([
                TextColumn::make('title'),
            ])
            ->pluralModelLabel(MenuItemResource::getPluralModelLabel())
            ->recordTitleAttribute('title')
            ->headerActions([
                CreateAction::make()
                    ->url(MenuItemResource::getUrl('create', ['menu' => $this->ownerRecord])),
                FixTreeAction::make(),
            ])
            ->actions([
                EditAction::make()
                    ->url(fn (MenuItem $record) => MenuItemResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make()
                    ->successRedirectUrl(MenuResource::getUrl('edit', ['record' => $this->ownerRecord])),
            ]);
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('laravel-filament-menu::menu.tree');
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
