<?php

namespace Novius\LaravelFilamentMenu\Filament\Resources\Menu\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Novius\LaravelFilamentMenu\Facades\MenuManager;

class ListMenu extends ListRecords
{
    public static function getResource(): string
    {
        return MenuManager::getMenuResource();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
