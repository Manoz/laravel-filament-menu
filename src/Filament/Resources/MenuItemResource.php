<?php

namespace Novius\LaravelFilamentMenu\Filament\Resources;

use CodeWithDennis\FilamentSelectTree\SelectTree;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Novius\LaravelFilamentMenu\Enums\LinkType;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuItemResource\Pages\CreateMenuItem;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuItemResource\Pages\EditMenuItem;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuItemResource\RelationManagers\MenuItemsRelationManager;
use Novius\LaravelFilamentMenu\Models\Menu;
use Novius\LaravelFilamentMenu\Models\MenuItem;
use Novius\LaravelLinkable\Filament\Forms\Components\Linkable;
use Wiebenieuwenhuis\FilamentCodeEditor\Components\CodeEditor;

class MenuItemResource extends Resource
{
    protected static ?string $slug = 'menu-items';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordRouteKeyName = 'id';

    protected static bool $shouldRegisterNavigation = false;

    public static function getModel(): string
    {
        return MenuManager::getMenuItemModel();
    }

    public static function getModelLabel(): string
    {
        return trans('laravel-filament-menu::menu.menu_item');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('laravel-filament-menu::menu.menu_items');
    }

    public static function form(Form $form): Form
    {
        $getMenu = static fn ($menu_id) => $menu_id ? Menu::find($menu_id) : null;

        return $form
            ->schema([
                Hidden::make('menu_id'),

                SelectTree::make('parent_id')
                    ->label(trans('laravel-filament-menu::menu.parent_item'))
                    ->enableBranchNode()
                    ->clearable(false)
                    ->searchable()
                    ->relationship(
                        relationship: 'parent',
                        titleAttribute: 'title',
                        parentAttribute: 'parent_id',
                        modifyQueryUsing: function (Builder|MenuItem $query, Get $get) {
                            $menu_id = $get('menu_id');
                            if ($menu_id) {
                                $query->where('menu_id', $menu_id);
                            }

                            return $query;
                        },
                        modifyChildQueryUsing: function (Builder|MenuItem $query, ?MenuItem $record) {
                            if ($record) {
                                $query->whereNot('id', $record->id);
                            }

                            return $query;
                        }
                    ),

                Select::make('menu_id')
                    ->label(trans('laravel-filament-menu::menu.menu_singular_label'))
                    ->relationship('menu', 'name')
                    ->disabled(fn (Get $get) => $get('menu_id')),

                TextInput::make('title')
                    ->label(trans('laravel-filament-menu::menu.title'))
                    ->rules(['required', 'max:191'])
                    ->required(),

                Select::make('link_type')
                    ->label(trans('laravel-filament-menu::menu.link_type'))
                    ->options(LinkType::class)
                    ->required()
                    ->live(),

                Linkable::make('internal_route')
                    ->label(trans('laravel-filament-menu::menu.internal_link'))
                    ->rules('required_if:link_type,'.LinkType::internal_link->value)
                    ->setLocale(function (Get $get) use ($getMenu) {
                        return $getMenu($get('menu_id'))?->locale;
                    })
                    ->hidden(fn (Get $get) => $get('link_type') !== LinkType::internal_link->value)
                    ->afterStateHydrated(function (Get $get, Linkable $component) {
                        $linkable_type = $get('linkable_type');
                        $linkable_id = $get('linkable_id');
                        if ($linkable_type && $linkable_id) {
                            $component->state($linkable_type.':'.$linkable_id);
                        }
                    })
                    ->afterStateUpdated(function ($state, Set $set) {
                        $infos = explode(':', $state);
                        if ($infos[0] !== 'route') {
                            /** @var class-string<Model> $class */
                            $class = $infos[0];
                            $set('linkable_type', (new $class)->getMorphClass());
                            $set('linkable_id', $infos[1]);
                            $set('internal_route', null);
                        } else {
                            $set('linkable_type', null);
                            $set('linkable_id', null);
                            $set('internal_route', $state);
                        }
                    }),

                Hidden::make('linkable_type'),
                Hidden::make('linkable_id'),

                TextInput::make('external_link')
                    ->label(trans('laravel-filament-menu::menu.external_link'))
                    ->rules([
                        'required_if:link_type,'.LinkType::external_link->value, 'max:191',
                    ])
                    ->hidden(fn (Get $get) => $get('link_type') !== LinkType::external_link->value)
                    ->helperText(trans('laravel-filament-menu::menu.must_start_with_http'))
                    ->url(),

                CodeEditor::make('html')
                    ->label(trans('laravel-filament-menu::menu.html'))
                    ->rules('required_if:link_type,'.LinkType::html->value)
                    ->helperText(trans('laravel-filament-menu::menu.help_code'))
                    ->hidden(fn (Get $get) => $get('link_type') !== LinkType::html->value),

                TextInput::make('html_classes')
                    ->label(trans('laravel-filament-menu::menu.html_classes'))
                    ->rules(['nullable', 'max:255', 'regex:/^[0-9a-z\- _]+$/i'])
                    ->helperText(trans('laravel-filament-menu::menu.html_classes_help')),

                Checkbox::make('target_blank')
                    ->label(trans('laravel-filament-menu::menu.target_blank'))
                    ->hidden(fn (Get $get) => $get('link_type') === LinkType::empty->value)
                    ->inline(false),

                Grid::make()
                    ->statePath('extras')
                    ->schema(function (Get $get) use ($getMenu) {
                        $menu = $getMenu($get('menu_id'));

                        if ($menu) {
                            return $menu->template->fields();
                        }

                        return [];
                    }),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder|MenuItem $query) {
                $query->withCount('ancestors');
            })
            ->paginated(fn (Table $table) => ! empty($table->getSortColumn()) && $table->getSortColumn() !== '_lft')
            ->defaultSort('_lft')
            ->columns([
                TextColumn::make('_lft')
                    ->label(trans('laravel-filament-menu::menu.column_tree'))
                    ->width(1)
                    ->html()
                    ->sortable()
                    ->state(function (MenuItem $record, Table $table) {
                        if (! empty($table->getSortColumn()) && $table->getSortColumn() !== '_lft') {
                            return '';
                        }

                        $level = $record->ancestors->count();
                        if ($level === 0) {
                            return '<code>□</code>';
                        }

                        $isLast = $record->isLeaf();
                        $hasNextSibling = $record->nextSiblings()->count() !== 0;

                        $prefix = '<code>'.str_repeat('┃&nbsp;&nbsp;', $level - 1); // Espaces pour l'indentation

                        if ($isLast && ! $hasNextSibling) {
                            return $prefix.'┗━━□'.'</code>';
                        }

                        return $prefix.'┣━━□'.'</code>';
                    }),

                TextColumn::make('title')
                    ->label(trans('laravel-filament-menu::menu.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('link_type')
                    ->label(trans('laravel-filament-menu::menu.link_type'))
                    ->badge(),

                TextColumn::make('url')
                    ->label(trans('laravel-filament-menu::menu.url'))
                    ->state(fn (MenuItem $record) => $record->href())
                    ->searchable()
                    ->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'create' => CreateMenuItem::route('/create/{menu:id}/{parent?}'),
            'edit' => EditMenuItem::route('/{record:id}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            MenuItemsRelationManager::class,
        ];
    }
}
