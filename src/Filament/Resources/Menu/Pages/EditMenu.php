<?php

namespace Novius\LaravelFilamentMenu\Filament\Resources\Menu\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Novius\LaravelFilamentMenu\Facades\MenuManager;

class EditMenu extends EditRecord
{
    public static function getResource(): string
    {
        return MenuManager::getMenuResource();
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
