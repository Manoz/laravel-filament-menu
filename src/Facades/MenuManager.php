<?php

namespace Novius\LaravelFilamentMenu\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use LaravelLang\Locales\Data\LocaleData;
use Novius\LaravelFilamentMenu\Contracts\MenuTemplate;
use Novius\LaravelFilamentMenu\Services\MenuManagerService;

/**
 * @method static Collection<string, MenuTemplate> templates()
 * @method static MenuTemplate|null template(string $templateKey)
 * @method static string getMenuModel()
 * @method static string getMenuResource()
 * @method static string getMenuItemModel()
 * @method static string getMenuItemResource()
 * @method static Collection<string, LocaleData> locales()
 *
 * @see MenuManagerService
 */
class MenuManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MenuManagerService::class;
    }
}
