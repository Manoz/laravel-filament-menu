<?php

namespace Novius\LaravelFilamentMenu;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelFilamentMenu\Services\MenuManagerService;

class LaravelFilamentMenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $packageDir = dirname(__DIR__);

        $this->publishes([$packageDir.'/config' => config_path()], 'config');

        $this->loadMigrationsFrom($packageDir.'/database/migrations');

        $this->loadViewsFrom($packageDir.'/resources/views', 'laravel-filament-menu');

        $this->loadTranslationsFrom($packageDir.'/lang', 'laravel-filament-menu');
        $this->publishes([$packageDir.'/lang' => lang_path('vendor/laravel-filament-menu')], 'lang');

        Blade::componentNamespace('Novius\\LaravelFilamentMenu\\View\\Components', 'laravel-filament-menu');

        Route::model('menu', config('laravel-filament-menu.models.menu', Menu::class));
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-filament-menu.php',
            'laravel-filament-menu'
        );

        $this->app->bind(MenuManagerService::class, static function () {
            return new MenuManagerService(config('laravel-filament-menu'));
        });
    }
}
