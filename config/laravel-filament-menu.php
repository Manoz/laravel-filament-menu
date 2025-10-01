<?php

return [
    // The menu manager will load automaticaly templates from this directory
    'autoload_templates_in' => app_path('Menus/Templates'),

    // List of tempates, other than those automatically loaded by `autoload_templates_in`
    'templates' => [
        \Novius\LaravelFilamentMenu\Templates\MenuTemplateWithTitle::class,
        \Novius\LaravelFilamentMenu\Templates\MenuTemplateWithoutTitle::class,
    ],

    /*
     * Resources used to manage your menus.
     */
    'resources' => [
        'menu' => \Novius\LaravelFilamentMenu\Filament\Resources\Menus\MenuResource::class,
        'menu_item' => \Novius\LaravelFilamentMenu\Filament\Resources\MenuItems\MenuItemResource::class,
    ],

    /*
     * Models used to manage your posts.
     */
    'models' => [
        'menu' => \Novius\LaravelFilamentMenu\Models\Menu::class,
        'menu_item' => \Novius\LaravelFilamentMenu\Models\MenuItem::class,
    ],
];
