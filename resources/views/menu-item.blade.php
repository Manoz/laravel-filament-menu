@php
    use Novius\LaravelFilamentMenu\Enums\LinkType;
    use Novius\LaravelFilamentMenu\Models\Menu;
    use Novius\LaravelFilamentMenu\Models\MenuItem;

    /** @var Menu $menu */
    /** @var MenuItem $item */
@endphp
<li @class([
    'lfm-item-li',
    $item->children->isNotEmpty() ? 'lfm--has-children' : '',
])>
    @if ($item->link_type === LinkType::html)
        {!! $item->html !!}
    @elseif ($item->link_type !== LinkType::empty)
        <a href="{{ $item->href() }}"
            @class([
                'lfm-item',
                'lfm-item--active' => $menu->template->isActiveItem($item),
                $item->html_classes
            ])
            {{ $item->target_blank ? 'target="_blank"' : '' }}
        >
            {{ $item->title }}
        </a>
    @else
        <{{$itemEmptyTag}} @class(['lfm-item lfm-item--is-empty', $item->html_classes])>
            {{ $item->title }}
        </{{$itemEmptyTag}}>
    @endif

    @if ($item->children->isNotEmpty())
        <ul @class([
            'lfm-items-container',
            $menu->template->containsActiveItem($item) ? 'lfm--has-active-item' : '',
        ]) data-depth="{{ $item->depth + 1 }}">
            @foreach($item->children as $item)
                {!! $menu->template->renderItem($menu, $item, $itemEmptyTag) !!}
            @endforeach
        </ul>
    @endif
</li>
