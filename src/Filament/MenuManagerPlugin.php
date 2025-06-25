<?php

namespace Novius\LaravelFilamentMenu\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Resources\Resource;
use InvalidArgumentException;
use Novius\LaravelFilamentMenu\Facades\MenuManager;

class MenuManagerPlugin implements Plugin
{
    public function __construct()
    {
        if (! is_subclass_of(MenuManager::getMenuResource(), Resource::class)) {
            throw new InvalidArgumentException('The menu resource must be a subclass of '.Resource::class);
        }
        if (! is_subclass_of(MenuManager::getMenuItemResource(), Resource::class)) {
            throw new InvalidArgumentException('The menu item resource must be a subclass of '.Resource::class);
        }
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'laravel-filament-menu';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            MenuManager::getMenuResource(),
            MenuManager::getMenuItemResource(),
        ]);
    }

    public function boot(Panel $panel): void {}
}
