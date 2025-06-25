<?php

namespace Novius\LaravelFilamentMenu\Templates;

use Novius\LaravelFilamentMenu\Concerns\IsMenuTemplate;
use Novius\LaravelFilamentMenu\Contracts\MenuTemplate;

class MenuTemplateWithoutTitle implements MenuTemplate
{
    use IsMenuTemplate;

    public function key(): string
    {
        return 'without-title';
    }

    public function name(): string
    {
        return trans('laravel-filament-menu::menu.template_without_title');
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
