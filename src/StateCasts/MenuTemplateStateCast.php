<?php

namespace Novius\LaravelFilamentMenu\StateCasts;

use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Novius\LaravelFilamentMenu\Contracts\MenuTemplate;
use Novius\LaravelFilamentMenu\Facades\MenuManager;

class MenuTemplateStateCast implements StateCast
{
    public function get(mixed $state): ?MenuTemplate
    {
        if (is_null($state)) {
            return null;
        }

        return MenuManager::template($state);
    }

    public function set(mixed $state): mixed
    {
        if ($state instanceof MenuTemplate) {
            return $state->key();
        }

        return $state;
    }
}
