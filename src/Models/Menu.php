<?php

namespace Novius\LaravelFilamentMenu\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Kalnoy\Nestedset\Collection;
use Novius\LaravelFilamentMenu\Cast\AsTemplate;
use Novius\LaravelFilamentMenu\Contracts\MenuTemplate;
use Novius\LaravelFilamentMenu\Database\Factories\MenuFactory;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelTranslatable\Traits\Translatable;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property int $id
 * @property string $name
 * @property ?string $title
 * @property string $slug
 * @property string $locale
 * @property ?int $locale_parent_id
 * @property MenuTemplate $template
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Menu|null $parent
 * @property-read Collection<int, MenuItem> $items
 *
 * @method static MenuFactory factory($count = null, $state = [])
 * @method static Builder<Menu> newModelQuery()
 * @method static Builder<Menu> newQuery()
 * @method static Builder<Menu> query()
 *
 * @mixin Model
 */
class Menu extends Model
{
    use HasFactory;
    use HasSlug;
    use Translatable;

    protected $table = 'menus';

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'template' => AsTemplate::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MenuManager::getMenuItemModel(), 'menu_id');
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->extraScope(fn (Builder|Menu $query) => $query->where('locale', $this->locale))
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->preventOverwrite()
            ->doNotGenerateSlugsOnUpdate();
    }

    public function getCacheName(): string
    {
        return 'laravel-filament-menu.menu.'.$this->id;
    }

    protected static function newFactory(): MenuFactory
    {
        return MenuFactory::new();
    }
}
