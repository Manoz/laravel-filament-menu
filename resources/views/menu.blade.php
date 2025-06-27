@php
    use Novius\LaravelFilamentMenu\Models\Menu;

    /** @var Menu $menu */
@endphp

<nav role="navigation"
     aria-label="{{ $menu->aria_label ?? $menu->title ?? $menu->name }}"
     @class([$menu->template->htmlClassesMenu($menu)])
>
    @if ($menu->template->hasTitle())
        <div>
            {{ $menu->title ?? $menu->name }}
        </div>
    @endif
    <ul>
        @foreach($items as $item)
            {!! $menu->template->renderItem($menu, $item) !!}
        @endforeach
    </ul>
</nav>
