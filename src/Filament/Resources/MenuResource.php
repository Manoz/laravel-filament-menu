<?php

namespace Novius\LaravelFilamentMenu\Filament\Resources;

use Exception;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;
use Novius\LaravelFilamentMenu\Contracts\MenuTemplate;
use Novius\LaravelFilamentMenu\Facades\MenuManager;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuResource\Pages\CreateMenu;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuResource\Pages\EditMenu;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuResource\Pages\ListMenu;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuResource\Pages\ViewMenu;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuResource\RelationManagers\MenuItemsRelationManager;
use Novius\LaravelFilamentMenu\Filament\Resources\MenuResource\RelationManagers\MenuItemsTreeRelationManager;
use Novius\LaravelFilamentSlug\Filament\Forms\Components\Slug;
use Novius\LaravelFilamentTranslatable\Filament\Forms\Components\Locale;
use Novius\LaravelFilamentTranslatable\Filament\Tables\Columns\LocaleColumn;
use Novius\LaravelFilamentTranslatable\Filament\Tables\Columns\TranslationsColumn;
use Novius\LaravelFilamentTranslatable\Filament\Tables\Filters\LocaleFilter;

class MenuResource extends Resource
{
    protected static ?string $slug = 'menus';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-bars-3-bottom-right';

    protected static ?string $recordRouteKeyName = 'id';

    public static function getModel(): string
    {
        return MenuManager::getMenuModel();
    }

    public static function getModelLabel(): string
    {
        return trans('laravel-filament-menu::menu.menu_singular_label');
    }

    public static function getPluralModelLabel(): string
    {
        return trans('laravel-filament-menu::menu.menus_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                $title = TextInput::make('name')
                    ->label(trans('laravel-filament-menu::menu.name'))
                    ->required(),

                Slug::make('slug')
                    ->label(trans('laravel-filament-menu::menu.slug'))
                    ->fromField($title)
                    ->required()
                    ->string()
                    ->regex('/^[a-zA-Z0-9-_]+$/')
                    ->unique(
                        MenuManager::getMenuModel(),
                        'slug',
                        ignoreRecord: true,
                        modifyRuleUsing: function (Unique $rule, Get $get) {
                            return $rule->where('locale', $get('locale'));
                        }
                    ),

                Select::make('template')
                    ->afterStateHydrated(function (Select $component, ?MenuTemplate $state) {
                        $component->state($state?->key());
                    })
                    ->label(trans('laravel-filament-menu::menu.template'))
                    ->options(MenuManager::templates()->mapWithKeys(fn (MenuTemplate $template) => [$template->key() => $template->name()]))
                    ->required()
                    ->live(),

                Locale::make('locale')
                    ->required(),

                TextInput::make('title')
                    ->label(trans('laravel-filament-menu::menu.title'))
                    ->required()
                    ->hidden(function (Get $get) {
                        $template = $get('template');
                        if ($template !== null) {
                            $template = MenuManager::template($template);

                            return ! $template->hasTitle();
                        }

                        return true;
                    }),

                TextInput::make('aria_label')
                    ->label(trans('laravel-filament-menu::menu.aria_label')),

                Hidden::make('locale_parent_id'),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(trans('laravel-filament-menu::menu.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label(trans('laravel-filament-menu::menu.slug'))
                    ->searchable()
                    ->sortable(),

                LocaleColumn::make('locale')
                    ->sortable(),
                TranslationsColumn::make('translations'),

                TextColumn::make('template')
                    ->formatStateUsing(fn (MenuTemplate $state) => $state->name())
                    ->label(trans('laravel-filament-menu::menu.template'))
                    ->sortable()
                    ->badge()
                    ->toggleable(),
            ])
            ->filters([
                LocaleFilter::make('locale'),
                SelectFilter::make('template')
                    ->label(trans('laravel-filament-menu::menu.template'))
                    ->options(fn () => MenuManager::templates()->mapWithKeys(fn (MenuTemplate $template) => [$template->key() => $template->name()])),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                ActionGroup::make([
                    ViewAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenu::route('/'),
            'create' => CreateMenu::route('/create'),
            'view' => ViewMenu::route('/{record:id}'),
            'edit' => EditMenu::route('/{record:id}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            MenuItemsRelationManager::class,
            MenuItemsTreeRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'title'];
    }
}
