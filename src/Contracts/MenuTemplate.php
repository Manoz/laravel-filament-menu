<?php

namespace Novius\LaravelFilamentMenu\Contracts;

use Filament\Forms\Components\Component;
use Kalnoy\Nestedset\Collection;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelFilamentMenu\Models\MenuItem;

interface MenuTemplate
{
    public function key(): string;

    public function name(): string;

    public function hasTitle(): bool;

    public function maxDepth(): int;

    public function isActiveItem(MenuItem $item): bool;

    public function containtActiveItem(MenuItem $item): bool;

    /** @return array<Component> */
    public function fields(): array;

    public function casts(): array;

    public function view(): string;

    public function htmlClassesMenu(Menu $menu): array;

    public function htmlClassesMenuItem(Menu $menu, MenuItem $item): array;

    public function viewItem(): string;

    public function render(Menu $menu, Collection $items): string;

    public function renderItem(Menu $menu, MenuItem $item): string;
}
