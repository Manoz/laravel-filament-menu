<?php

namespace Novius\LaravelFilamentMenu\Filament\Resources\MenuItem\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Route;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelFilamentMenu\Models\MenuItem;

class CreateMenuItem extends CreateRecord
{
    public ?Menu $menu = null;

    public ?int $parent_id = null;

    public static function getResource(): string
    {
        return MenuManager::getMenuItemResource();
    }

    protected function afterFill(): void
    {
        $this->menu = $this->getMenu();
        if ($this->menu) {
            $this->data['menu_id'] = $this->menu->id;
        }
        $this->data['parent_id'] = $this->getParentId();
    }

    protected function afterCreate(): void
    {
        /** @var MenuItem $record */
        $record = $this->getRecord();
        $this->menu = $record->menu;
        $this->parent_id = $record->parent_id;
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [
            MenuManager::getMenuResource()::getUrl() => MenuManager::getMenuResource()::getBreadcrumb(),
            MenuManager::getMenuResource()::getUrl('view', ['record' => $this->getMenu()]) => $this->getMenu()->name,
            MenuManager::getMenuResource()::getUrl('edit', ['record' => $this->getMenu()]) => MenuManager::getMenuItemResource()::getBreadcrumb(),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    protected function getMenu(): ?Menu
    {
        if ($this->menu) {
            return $this->menu;
        }

        $this->menu = Route::current()?->parameter('menu');

        return $this->menu;
    }

    protected function getParentId(): ?int
    {
        if ($this->parent_id) {
            return $this->parent_id;
        }

        $parent_id = Route::current()?->parameter('parent');
        if ($parent_id) {
            $this->parent_id = (int) $parent_id;
        }

        return $this->parent_id;
    }
}
