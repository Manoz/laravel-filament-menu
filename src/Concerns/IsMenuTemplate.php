<?php

namespace Novius\LaravelFilamentMenu\Concerns;

use Closure;
use Kalnoy\Nestedset\Collection;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelFilamentMenu\Models\MenuItem;
use Throwable;

trait IsMenuTemplate
{
    public function hasTitle(): bool
    {
        return false;
    }

    public function maxDepth(): int
    {
        return 1;
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

    /**
     * @throws Throwable
     */
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

    ): string {
        $containerClasses = (array) (is_callable($containerClasses) ? $containerClasses($menu) : $containerClasses);
        $titleClasses = (array) (is_callable($titleClasses) ? $titleClasses($menu) : $titleClasses);
        $containerItemsClassesCallback = static fn (?MenuItem $item = null) => (array) (is_callable($containerItemsClasses) ? $containerItemsClasses($item) : $containerItemsClasses);

        return view($this->view(), [
            'menu' => $menu,
            'items' => $items,
            'containerClasses' => $containerClasses,
            'titleClasses' => $titleClasses,
            'containerItemsClasses' => $containerItemsClassesCallback,
            'containerItemClasses' => $containerItemClasses,
            'itemClasses' => $itemClasses,
            'itemActiveClasses' => $itemActiveClasses,
            'itemContainsActiveClasses' => $itemContainsActiveClasses,
        ])->render();
    }

    abstract public function viewItem(): string;

    /**
     * @throws Throwable
     */
    public function renderItem(
        Menu $menu,
        MenuItem $item,
        Closure|array|string|null $containerItemsClasses = null,
        Closure|array|string|null $containerItemClasses = null,
        Closure|array|string|null $itemClasses = null,
        ?string $itemActiveClasses = null,
        ?string $itemContainsActiveClasses = null,
    ): string {
        $containerItemsClasses = (array) (is_callable($containerItemsClasses) ? $containerItemsClasses($item) : $containerItemsClasses);
        $containerItemClasses = (array) (is_callable($containerItemClasses) ? $containerItemClasses($item) : $containerItemClasses);
        $itemClasses = (array) (is_callable($itemClasses) ? $itemClasses($item) : $itemClasses);

        return view($this->viewItem(), [
            'menu' => $menu,
            'item' => $item,
            'containerItemsClasses' => $containerItemsClasses,
            'containerItemClasses' => $containerItemClasses,
            'itemClasses' => $itemClasses,
            'itemActiveClasses' => $itemActiveClasses,
            'itemContainsActiveClasses' => $itemContainsActiveClasses,
        ])->render();
    }
}
