<?php

namespace Novius\LaravelFilamentMenu\Filament\Resources\MenuItemResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Route;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelFilamentMenu\Models\Menu;

class CreateMenuItem extends CreateRecord
{
    public ?Menu $menu = null;

    public static function getResource(): string
    {
        return MenuManager::getMenuItemResource();
    }

    protected function afterFill(): void
    {
        $this->menu = Route::current()?->parameter('menu');
        if ($this->menu) {
            $this->data['menu_id'] = $this->menu->id;
        }
        $this->data['parent_id'] = Route::current()?->parameter('parent');
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [
            MenuManager::getMenuResource()::getUrl() => MenuManager::getMenuResource()::getBreadcrumb(),
            MenuManager::getMenuResource()::getUrl('view', ['record' => $this->menu]) => $this->menu->name,
            MenuManager::getMenuResource()::getUrl('edit', ['record' => $this->menu]) => MenuManager::getMenuItemResource()::getBreadcrumb(),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }
}
