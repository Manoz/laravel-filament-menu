<?php

namespace Novius\LaravelFilamentMenu\Concerns;

use Kalnoy\Nestedset\Collection;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelFilamentMenu\Models\MenuItem;

trait IsMenuTemplate
{
    public function hasTitle(): bool
    {
        return false;
    }

    public function isActiveItem(MenuItem $item): bool
    {
        return $item->href() === url()->current();
    }

    public function containtActiveItem(MenuItem $item): bool
    {
        foreach ($item->descendants as $descendant) {
            if ($this->isActiveItem($descendant)) {
                return true;
            }
        }

        return false;
    }

    public function fields(): array
    {
        return [];
    }

    public function casts(): array
    {
        return [];
    }

    abstract public function view(): string;

    public function render(Menu $menu, Collection $items): string
    {
        return view($this->view(), ['menu' => $menu, 'items' => $items]);
    }

    abstract public function viewItem(): string;

    public function renderItem(Menu $menu, MenuItem $item): string
    {
        return view($this->viewItem(), ['menu' => $menu, 'item' => $item]);
    }
}
