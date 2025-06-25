<?php

namespace Novius\LaravelFilamentMenu\Templates;

use Novius\LaravelFilamentMenu\Concerns\IsMenuTemplate;
use Novius\LaravelFilamentMenu\Contracts\MenuTemplate;

class MenuTemplateWithTitle implements MenuTemplate
{
    use IsMenuTemplate;

    public function key(): string
    {
        return 'with-title';
    }

    public function name(): string
    {
        return trans('laravel-filament-menu::menu.template_with_title');
    }

    public function hasTitle(): bool
    {
        return true;
    }

    public function view(): string
    {
        return 'laravel-filament-menu::menu';
    }

    public function viewItem(): string
    {
        return 'laravel-filament-menu::menu-item';
    }
}
