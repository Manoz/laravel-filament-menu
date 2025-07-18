@php
    use Novius\LaravelFilamentMenu\Models\Menu;

    /** @var Menu $menu */
@endphp

<nav role="navigation"
     aria-label="{{ $menu->aria_label ?? $menu->title ?? $menu->name }}"
     @class($containerClasses)
>
    @if ($menu->template->hasTitle())
        <span @class($titleClasses)>
            {{ $menu->title ?? $menu->name }}
        </span>
    @endif
    <ul @class($containerItemsClasses())>
        @foreach($items as $item)
            {!! $menu->template->renderItem(
                $menu,
                $item,
                $containerItemsClasses,
                $containerItemClasses,
                $itemClasses,
                $itemActiveClasses,
                $itemContainsActiveClasses
            ) !!}
        @endforeach
    </ul>
</nav>
