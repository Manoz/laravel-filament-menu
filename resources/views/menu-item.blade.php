@php
    use Novius\LaravelFilamentMenu\Enums\LinkType;use Novius\LaravelFilamentMenu\Models\Menu;
    use Novius\LaravelFilamentMenu\Models\MenuItem;

    /** @var Menu $menu */
    /** @var MenuItem $item */
@endphp
<li>
    @if ($item->link_type === LinkType::html)
        {!! $item->html !!}
    @else
        <a href="{{ $item->href() }}" @class([$item->htmlClasses]) {{ $item->target_blank ? 'target="_blank"' : '' }}>
            {{ $item->title }}
        </a>
    @endif

    @if ($item->children->isNotEmpty())
        <ul>
            @foreach($item->children as $item)
                {!! $menu->template->renderItem($menu, $item) !!}
            @endforeach
        </ul>
    @endif
</li>
