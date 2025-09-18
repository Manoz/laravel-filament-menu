<?php

namespace Novius\LaravelFilamentMenu\Contracts;

use Closure;
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

    /** @return array<\Filament\Schemas\Components\Component> */
    public function fields(): array;

    public function casts(): array;

    public function view(): string;

    public function viewItem(): string;

    public function render(
        Menu $menu,
        Collection $items,
        Closure|array|string|null $containerClasses = null,
        Closure|array|string|null $titleClasses = null,
        Closure|array|string|null $containerItemsClasses = null,
        Closure|array|string|null $containerItemClasses = null,
        Closure|array|string|null $itemClasses = null,
        ?string $itemActiveClasses = null,
        ?string $itemContainsActiveClasses = null,
    ): string;

    public function renderItem(
        Menu $menu,
        MenuItem $item,
        Closure|array|string|null $containerItemsClasses = null,
        Closure|array|string|null $containerItemClasses = null,
        Closure|array|string|null $itemClasses = null,
        ?string $itemActiveClasses = null,
        ?string $itemContainsActiveClasses = null,
    ): string;
}
