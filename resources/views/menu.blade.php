@php
    use Novius\LaravelFilamentMenu\Models\Menu;

    /** @var Menu $menu */
@endphp

<nav role="navigation"
    id="menu-{{ $menu->slug }}"
    aria-label="{{ $menu->aria_label ?? $menu->title ?? $menu->name }}"
    @class(['lfm-'.$menu->slug])>
    @if ($menu->template->hasTitle())
        <span>
            {{ $menu->title ?? $menu->name }}
        </span>
    @endif
    <ul>
        {{-- @foreach($items as $item)
            {!! $menu->template->renderItem(
                $menu,
                $item,
                $itemEmptyTag
            ) !!}
        @endforeach --}}
    </ul>
</nav>
