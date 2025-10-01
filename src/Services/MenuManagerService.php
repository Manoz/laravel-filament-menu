<?php

namespace Novius\LaravelFilamentMenu\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelLang\Locales\Data\LocaleData;
use LaravelLang\Locales\Facades\Locales;
use Novius\LaravelFilamentMenu\Contracts\MenuTemplate;
use Novius\LaravelFilamentMenu\Filament\Resources\Menus\MenuResource;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuItems\MenuItemResource;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelFilamentMenu\Models\MenuItem;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class MenuManagerService
{
    public function __construct(protected array $config = []) {}

    /**
     * @return Collection<string, MenuTemplate>
     */
    public function templates(): Collection
    {
        $templates = [];
        $potentialTemplates = array_merge(
            Arr::get($this->config, 'templates', []),
            $this->autoloadIn('autoload_templates_in')
        );
        foreach ($potentialTemplates as $templateClass) {
            if (! class_exists($templateClass)) {
                continue;
            }

            if (! in_array(MenuTemplate::class, class_implements($templateClass), true) ||
                (new ReflectionClass($templateClass))->isAbstract()
            ) {
                continue;
            }
            /** @var MenuTemplate $template */
            $template = new $templateClass;
            $templates[$template->key()] = $template;
        }

        return collect($templates);
    }

    public function template(string $templateKey): ?MenuTemplate
    {
        $template = $this->templates()->get($templateKey);
        if (empty($template)) {
            return null;
        }

        return $template;
    }

    /**
     * @return Collection<string, LocaleData>
     */
    public function locales(): Collection
    {
        $locales = Arr::get($this->config, 'locales', []);

        return Locales::installed()
            ->when(! empty($locales), fn (Collection $collection) => $collection->filter(fn (LocaleData $localeData) => in_array($localeData->code, $locales, true)));
    }

    /** @return class-string<Menu> */
    public function getMenuModel(): string
    {
        return Arr::get($this->config, 'models.menu', Menu::class);
    }

    /** @return class-string<MenuResource> */
    public function getMenuResource(): string
    {
        return Arr::get($this->config, 'resources.post', MenuResource::class);
    }

    /** @return class-string<MenuItem> */
    public function getMenuItemModel(): string
    {
        return Arr::get($this->config, 'models.menu_item', MenuItem::class);
    }

    /** @return class-string<MenuItemResource> */
    public function getMenuItemResource(): string
    {
        return Arr::get($this->config, 'resources.menu_item', MenuItemResource::class);
    }

    protected function autoloadIn($config_key): array
    {
        $namespace = app()->getNamespace();

        $resources = [];
        $autoload_templates_in = Arr::get($this->config, $config_key);
        if (empty($autoload_templates_in) || ! is_dir($autoload_templates_in)) {
            return $resources;
        }

        foreach ((new Finder)->in($this->config[$config_key])->files() as $resource) {
            $resource = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($resource->getPathname(), app_path().DIRECTORY_SEPARATOR)
            );

            $resources[] = $resource;
        }

        return $resources;
    }
}
