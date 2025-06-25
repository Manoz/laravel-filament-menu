<?php

namespace Novius\LaravelFilamentMenu\Filament\Resources\MenuItemResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuItemResource;
use Novius\LaravelFilamentMenu\Models\MenuItem;

class MenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'children';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('laravel-filament-menu::menu.sub_menus');
    }

    public function table(Table $table): Table
    {
        return MenuManager::getMenuItemResource()::table($table)
            ->modifyQueryUsing(function (Builder|MenuItem $query) {
                $query->whereHas('parent', fn (Builder|MenuItem $query) => $query->where('id', $this->ownerRecord->id));
            })
            ->pluralModelLabel(MenuItemResource::getPluralModelLabel())
            ->recordTitleAttribute('title')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->url(MenuItemResource::getUrl('create', ['menu' => $this->ownerRecord->menu, 'parent' => $this->ownerRecord])),
            ])
            ->actions([
                EditAction::make()
                    ->url(fn (MenuItem $record) => MenuItemResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make()
                    ->successRedirectUrl(MenuItemResource::getUrl('edit', ['record' => $this->ownerRecord])),
            ]);
    }

    public function isReadOnly(): bool
    {
        return false;
    }
}
