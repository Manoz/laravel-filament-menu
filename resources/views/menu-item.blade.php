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
                $item->htmlClasses
            ])
            {{ $item->target_blank ? 'target="_blank"' : '' }}
        >
            {{ $item->title }}
        </a>
    @else
        <div @class([
            ...$itemClasses,
            $item->htmlClasses
        ])>
            {{ $item->title }}
        </div>
    @endif

    @if ($item->children->isNotEmpty())
        <ul
            @class([
                ...$containerItemsClasses($item),
                $menu->template->containtActiveItem($item) ? ($itemContainsActiveClasses ?? 'open') : '',
            ])
        >
            @foreach($item->children as $item)
                {!! $menu->template->renderItem($menu, $item) !!}
            @endforeach
        </ul>
    @endif
</li>
