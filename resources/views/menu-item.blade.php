@php
    use Novius\LaravelFilamentMenu\Enums\LinkType;use Novius\LaravelFilamentMenu\Models\Menu;
    use Novius\LaravelFilamentMenu\Models\MenuItem;

    /** @var Menu $menu */
    /** @var MenuItem $item */
@endphp
<li>
    @if ($item->link_type === LinkType::html)
        {!! $item->html !!}
    @elseif ($item->link_type !== LinkType::empty)
        <a href="{{ $item->href() }}"
            {{ $menu->template->isActiveItem($item) ? 'data-active="true"' : ''}}
            @class([
                ...$menu->template->htmlClassesMenuItem($menu, $item),
                $item->htmlClasses
            ])
            {{ $item->target_blank ? 'target="_blank"' : '' }}
        >
            {{ $item->title }}
        </a>
    @else
        <div @class([
            ...$menu->template->htmlClassesMenuItem($menu, $item),
            $item->htmlClasses
        ])>
            {{ $item->title }}
        </div>
    @endif

    @if ($item->children->isNotEmpty())
        <ul
            {{ $menu->template->containtActiveItem($item) ? 'data-open="true"' : ''}}
        >
            @foreach($item->children as $item)
                {!! $menu->template->renderItem($menu, $item) !!}
            @endforeach
        </ul>
    @endif
</li>
