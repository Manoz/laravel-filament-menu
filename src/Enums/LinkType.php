<?php

namespace Novius\LaravelFilamentMenu\Enums;

use Filament\Support\Contracts\HasLabel;

enum LinkType: string implements HasLabel
{
    case internal_link = 'internal_link';

    case external_link = 'external_link';

    case html = 'html';

    case empty = 'empty';

    public function getLabel(): string
    {
        return match ($this) {
            self::internal_link => trans('laravel-filament-menu::menu.internal_link'),
            self::external_link => trans('laravel-filament-menu::menu.external_link'),
            self::html => trans('laravel-filament-menu::menu.html'),
            self::empty => trans('laravel-filament-menu::menu.empty_link'),
        };
    }
}
