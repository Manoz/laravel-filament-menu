<?php

namespace Novius\LaravelFilamentMenu\Filament\Resources\Menus\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;
use Novius\LaravelFilamentMenu\Facades\MenuManager;

class ViewMenu extends ViewRecord
{
    public static function getResource(): string
    {
        return MenuManager::getMenuResource();
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
