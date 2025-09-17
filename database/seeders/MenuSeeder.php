<?php

namespace Novius\LaravelFilamentMenu\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use LaravelLang\Locales\Data\LocaleData;
use LaravelLang\Locales\Facades\Locales;
use Mockery\Exception\RuntimeException;
use Novius\LaravelFilamentMenu\Contracts\MenuTemplate;
use Novius\LaravelFilamentMenu\Models\Menu;

abstract class MenuSeeder extends Seeder
{
    /** @var Collection<int, LocaleData>|null */
    protected ?Collection $locales = null;

    /**
     * Run the database seeds.
     *
     * @throws RuntimeException
     */
    public function run(): void
    {
        $menus_config = $this->menus();

        Menu::query()
            ->whereNotIn('slug', array_keys($menus_config))
            ->orWhereNotIn('locale', Locales::installed()->pluck('code'))
            ->each(function (Menu $menu) {
                $menu->items()->delete();
                $menu->delete();
            });

        $locales = Locales::installed();
        foreach ($menus_config as $slug => $config) {
            $menuParent = Menu::query()->firstWhere('slug', $slug);

            /** @var class-string<MenuTemplate> $template */
            $template = Arr::get($config, 'template');
            if (empty($template)) {
                throw new RuntimeException('template key for '.$slug.' does not exist');
            }
            if (! class_exists($template)) {
                throw new RuntimeException('Template class '.$template.' does not exist');
            }
            if (! in_array(MenuTemplate::class, class_implements($template), true)) {
                throw new RuntimeException('Template class '.$template.' does not implement '.MenuTemplate::class);
            }
            $name = Arr::get($config, 'name');
            if (empty($name)) {
                throw new RuntimeException('name key for '.$slug.' does not exist');
            }
            $title = Arr::get($config, 'title');

            foreach ($locales as $locale) {
                if ($menuParent && $menuParent->locale === $locale->code) {
                    $menuParent->name = $this->getLocalizedString($locale, $name);
                    $menuParent->title = $this->getLocalizedString($locale, $title);
                    $menuParent->slug = $slug;
                    $menuParent->template = new $template;
                    $menuParent->save();

                    $menu = $menuParent;
                } else {
                    $menu = Menu::withLocale($locale->code)->firstWhere('slug', $slug);
                    if ($menu === null) {
                        $menu = new Menu;
                    }
                    $menu->name = $this->getLocalizedString($locale, $name);
                    $menu->title = $this->getLocalizedString($locale, $title);
                    $menu->slug = $slug;
                    $menu->template = new $template;
                    $menu->locale_parent_id = $menuParent?->id;
                    $menu->save();
                }
                /** @var Menu $menuParent */
                $menuParent = $menuParent ?? $menu;

                /** @var Menu $menu */
                $menu->items()->delete();

                $this->postCreate($config, $locale, $menu);
            }
        }
    }

    protected function getLocales(): Collection
    {
        if ($this->locales === null) {
            $this->locales = Locales::installed();
        }

        return $this->locales;
    }

    protected function getLocalizedString(LocaleData $locale, ?string $string): ?string
    {
        if (empty($string)) {
            return null;
        }

        return $string.($this->getLocales()->count() > 1 ? ' '.$locale->code : '');
    }

    protected function postCreate(array $config, LocaleData $locale, Menu $menu): void {}

    /**
     * Example:
     *  [
     *      'slug' => [
     *          'name' => 'A name',
     *          'title' => 'A title', // optional
     *          'template' => AMenuTemplateClass::class,
     *      ],
     *  ]
     */
    abstract protected function menus(): array;
}
