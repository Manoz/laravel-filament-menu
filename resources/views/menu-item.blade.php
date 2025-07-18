@php
    use Novius\LaravelFilamentMenu\Enums\LinkType;use Novius\LaravelFilamentMenu\Models\Menu;
    use Novius\LaravelFilamentMenu\Models\MenuItem;

    /** @var Menu $menu */
    /** @var MenuItem $item */
@endphp
<li @class($containerItemClasses)>
    @if ($item->link_type === LinkType::html)
        {!! $item->html !!}
    @elseif ($item->link_type !== LinkType::empty)
        <a href="{{ $item->href() }}"
            @class([
                ...$itemClasses,
                $menu->template->isActiveItem($item) ? ($itemActiveClasses ?? 'active') : '',
                $item->html_classes
            ])
            {{ $item->target_blank ? 'target="_blank"' : '' }}
        >
            {{ $item->title }}
        </a>
    @else
        <span @class([
            ...$itemClasses,
            $item->html_classes
        ])>
            {{ $item->title }}
        </span>
    @endif

    @if ($item->children->isNotEmpty())
        <ul
            @class([
                ...$containerItemsClasses,
                $menu->template->containtActiveItem($item) ? ($itemContainsActiveClasses ?? 'open') : '',
            ])
        >
            @foreach($item->children as $item)
                {!! $menu->template->renderItem($menu, $item) !!}
            @endforeach
        </ul>
    @endif
</li>
