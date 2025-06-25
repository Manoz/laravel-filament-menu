<?php

namespace Novius\LaravelFilamentMenu\Filament\Resources\MenuResource\Pages;

use Illuminate\Database\Eloquent\Model;
use LaravelLang\Locales\Facades\Locales;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelFilamentTranslatable\Filament\Resources\Pages\CreateRecord;

class CreateMenu extends CreateRecord
{
    public static function getResource(): string
    {
        return MenuManager::getMenuResource();
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

    /**
     * @param  Menu  $parent
     */
    protected function getDataFromTranslate(Model $parent, string $locale): array
    {
        $data = $parent->attributesToArray();

        $data['name'] = $parent->name.' '.Locales::get($locale)->native;

        return $data;
    }
}
