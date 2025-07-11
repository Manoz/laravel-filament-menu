# Laravel Filament Menu manager

[![Novius CI](https://github.com/novius/laravel-filament-menu/actions/workflows/main.yml/badge.svg?branch=main)](https://github.com/novius/laravel-filament-menu/actions/workflows/main.yml)
[![Packagist Release](https://img.shields.io/packagist/v/novius/laravel-filament-menu.svg?maxAge=1800&style=flat-square)](https://packagist.org/packages/novius/laravel-filament-menu)
[![License: AGPL v3](https://img.shields.io/badge/License-AGPL%20v3-blue.svg)](http://www.gnu.org/licenses/agpl-3.0)

## Introduction

This [Laravel Filament](https://filamentphp.com/) package allows you to manage menus in your Laravel Filament admin panel.

## Requirements

* PHP >= 8.2
* Laravel Filament >= 3.3
* Laravel Framework >= 11.0 

## Installation

```sh
composer require novius/laravel-filament-menu
```

Publish Filament assets

```sh
php artisan filament:assets
```

Then, launch migrations 

```sh
php artisan migrate
```

In your `AdminFilamentPanelProvider` add the `MenuManagerPlugin` :

```php
use Novius\LaravelFilamentMenu\Filament\MenuManagerPlugin;

class AdminFilamentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->plugins([
                MenuManagerPlugin::make(),
            ])
            // ...
            ;
    }
}
```

### Configuration

Some options that you can override are available.

```sh
php artisan vendor:publish --provider="Novius\LaravelFilamentMenu\LaravelFilamentMenuServiceProvider" --tag="config"
```

## Usage

### Blade directive

You can display menu with : 

```bladehtml
<x-laravel-filament-menu::menu 
    menu-slug="slug-of-menu" 
    locale="fr" {{-- optional, will use the current locale by default --}}
    container-classes="p6" {{-- optional, null by default. Css classes for the menu container (<nav>), can be a string, an array or a Closure taking the menu as single paramater --}
    title-classes="font-bold" {{-- optional, null by default. Css classes for the menu title (<div>), can be a string, an array or a Closure taking the menu as single paramater --}
    container-items-classes="flex flex-col gap-x-6" {{-- optional, null by default. Css classes for the menu container of a list of items (<ul>), can be a string, an array or a Closure taking the item menu as single paramater --}
    container-item-classes="p6" {{-- optional, null by default. Css classes for the item menu container (<li>), can be a string, an array or a Closure taking the item menu as single paramater --}
    item-classes="p6" {{-- optional, null by default. Css classes for the item menu (<a> or <div>), can be a string, an array or a Closure taking the item menu as single paramater --}
    item-active-classes="active" {{-- optional, 'active' by default. Css classes for the active item menu (<a>), must be a string --}
    item-contains-active-classes="open" {{-- optional, 'open' by default. Css classes for item menu containers containing the active item (<a>), must be a string --}
/>
```

### Write your owned template

#### Template class

```php
namespace App\Menus\Templates;

use Novius\LaravelFilamentMenu\Concerns\IsMenuTemplate;
use Novius\LaravelFilamentMenu\Contracts\MenuTemplate;

class MyMenuTemplate implements MenuTemplate // Must implement MenuTemplate interface
{
    use IsMenuTemplate; // This trait will defined required method with default implementation

    public function key(): string
    {
        return 'my-template';
    }

    public function name(): string
    {
        return 'My template';
    }

    public function hasTitle(): bool
    {
        return true; // Define if the menu need a title displaying in front. False by default if you don't implement this method
    }

    public function maxDepth(): int
    {
        return 1; //Define the max depth of the menu
    }

    public function fields(): array
    {
        return [
            \Filament\Forms\Components\DatePicker::make('extras.date'), // You can add additionals fields on items. Prefix field name by `extras.` to save them in the extras field
        ];
    }

    public function casts(): array
    {
        return [
            'date' => 'date:Y-m-d', // If you add additionals fields on items, you can define their casts
        ];
    }

    public function view(): string
    {
        return 'menus.my-template'; // Define the view used to display this the menu
    }

    public function viewItem(): string
    {
        return 'menus.my-template-item'; // Define the view used to display an item of the menu
    }
}
```
#### Template views

First the view to display the menu :

```bladehtml
@php
    use Novius\LaravelFilamentMenu\Models\Menu;
@endphp
<nav role="navigation"
     aria-label="{{ $menu->aria_label ?? $menu->title ?? $menu->name }}"
     @class($containerClasses)
>
    @if ($menu->template->hasTitle())
        <div @class($titleClasses)>
            {{ $menu->title ?? $menu->name }}
        </div>
    @endif
    <ul @class($containerItemsClasses)>
        @foreach($items as $item)
            {!! $menu->template->renderItem($menu, $item, $containerItemsClasses, $containerItemClasses, $itemClasses) !!}
        @endforeach
    </ul>
</nav>
```

The the view to display an item of the menu

```bladehtml
@php
    use Novius\LaravelFilamentMenu\Enums\LinkType;use Novius\LaravelFilamentMenu\Models\Menu;
    use Novius\LaravelFilamentMenu\Models\MenuItem;
@endphp
<li @class($containerItemClasses)>
    @if ($item->link_type === LinkType::html)
        {!! $item->html !!}
    @elseif ($item->link_type !== LinkType::empty)
        <a href="{{ $item->href() }}"
           {{ $menu->template->isActiveItem($item) ? 'data-active="true"' : ''}}
            @class([
                ...$itemClasses($item),
                $item->htmlClasses
            ])
            {{ $item->target_blank ? 'target="_blank"' : '' }}
        >
                {{ $item->title }}
        </a>
    @else
        <div @class([
             ...$itemClasses($item),
             $item->htmlClasses
        ])>
            {{ $item->title }}
        </div>
    @endif

    @if ($item->children->isNotEmpty())
        <ul
            @class($containerItemsClasses)
            {{ $menu->template->containtActiveItem($item) ? 'data-open="true"' : ''}}
        >
            @foreach($item->children as $item)
                {!! $menu->template->renderItem($menu, $item) !!}
            @endforeach
        </ul>
    @endif
</li>
```

### Manage internal link possibilities

Laravel Filament Menu uses [Laravel Linkable](https://github.com/novius/laravel-linkable) to manage linkable routes and models. Please read the documentation.

## Lint

Run php-cs with:

```sh
composer run-script lint
```

## Contributing

Contributions are welcome!
Leave an issue on Github, or create a Pull Request.


## Licence

This package is under [GNU Affero General Public License v3](http://www.gnu.org/licenses/agpl-3.0.html) or (at your option) any later version.
