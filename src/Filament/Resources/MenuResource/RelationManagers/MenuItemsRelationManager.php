<?php

namespace Novius\LaravelFilamentMenu\Filament\Resources\MenuResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuItemResource;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuResource;
use Novius\LaravelFilamentMenu\Models\MenuItem;

class MenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return MenuItemResource::getPluralModelLabel();
    }

    public function table(Table $table): Table
    {
        return MenuManager::getMenuItemResource()::table($table)
            ->pluralModelLabel(MenuItemResource::getPluralModelLabel())
            ->recordTitleAttribute('title')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->url(MenuItemResource::getUrl('create', ['menu' => $this->ownerRecord])),
            ])
            ->actions([
                EditAction::make()
                    ->url(fn (MenuItem $record) => MenuItemResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make()
                    ->successRedirectUrl(MenuResource::getUrl('edit', ['record' => $this->ownerRecord])),
            ], ActionsPosition::BeforeColumns);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
