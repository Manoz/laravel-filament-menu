<?php

namespace Novius\LaravelFilamentMenu\Filament\Resources\MenuItem\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelFilamentMenu\Filament\Resources\Menu\MenuResource;
use Novius\LaravelFilamentMenu\Models\MenuItem;

class EditMenuItem extends EditRecord
{
    public static function getResource(): string
    {
        return MenuManager::getMenuItemResource();
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function configureDeleteAction(DeleteAction $action): void
    {
        /** @var MenuItem $record */
        $record = $this->getRecord();

        $action
            ->authorize(MenuManager::getMenuItemResource()::canDelete($record))
            ->successRedirectUrl(MenuResource::getUrl('edit', ['record' => $record->menu]));
    }

    public function getBreadcrumbs(): array
    {
        /** @var MenuItem $record */
        $record = $this->getRecord();

        $breadcrumbs = [
            MenuManager::getMenuResource()::getUrl() => MenuManager::getMenuResource()::getBreadcrumb(),
            MenuManager::getMenuResource()::getUrl('view', ['record' => $record->menu]) => $record->menu->name,
            MenuManager::getMenuResource()::getUrl('edit', ['record' => $record->menu]) => MenuManager::getMenuItemResource()::getBreadcrumb(),
        ];

        if ($record->exists) {
            $breadcrumbs[MenuManager::getMenuItemResource()::getUrl('edit', ['record' => $record])] = $this->getRecordTitle();
        }

        $breadcrumbs[] = $this->getBreadcrumb();

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }
}
