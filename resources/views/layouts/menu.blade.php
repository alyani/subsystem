@php
    use Illuminate\Support\Facades\Route;
	$routeName = Route::current()->getName();
@endphp
<ul class="nav nav-sidebar" data-nav-type="accordion">
    <li class="nav-item-header"></li>

    <li class="nav-item">
        <a href="{{ route('dashboard') }}" class="nav-link {{ $routeName == 'dashboard' ? 'active' : '' }}">
            <i class="ph-house"></i>
            <span>{{ st('menu.dashboard') }}</span>
        </a>
    </li>

    @foreach(config('subsystemMenu', []) as $name => $info)

        {{-- بدون زیرمنو --}}
        @if(empty($info['child']) && !empty($info['routeName']))

            @continue(auth()->user()->cannot($info['permission'] ?? $info['routeName']))

            <li class="nav-item">
                <a href="{{ route($info['routeName']) }}"
                   class="nav-link {{ ($info['routeName'] == $routeName || in_array($routeName, $info['active'] ?? [])) ? 'active' : '' }}">
                    <i class="{{ $info['icon'] ?? '' }}"></i>
                    <span>{!! st("menu.$name") !!}</span>
                </a>
            </li>

        @elseif(!empty($info['child']))
            @php
                $children = collect($info['child'])->filter(function ($item) {
                    return empty($item['routeName']) || auth()->user()->can($item['permission'] ?? $item['routeName']);
                });
            @endphp

            @continue($children->isEmpty())

            <li class="nav-item nav-item-submenu">
                <a class="nav-link {{ in_array($routeName, activeMenu($name)) ? 'active' : '' }}">
                    <i class="{{ $info['icon'] ?? '' }}"></i>
                    <span>{!! st("menu.$name") !!}</span>
                </a>

                <ul class="nav-group-sub collapse"
                    data-submenu-title="{{ $name }}"
                    @if(in_array($routeName, activeMenu($name))) style="display:block" @endif>

                    @foreach($children as $childName => $items)
                        <li class="nav-item {{ in_array($routeName, activeSubMenu($name, $childName)) ? 'active' : '' }}">
                            <a href="{{ route($items['routeName']) }}"
                               class="nav-link {{ in_array($routeName, activeSubMenu($name, $childName)) ? 'active' : '' }}">
                                <span>{!! st("menu.$childName") !!}</span>
                            </a>
                        </li>
                    @endforeach

                </ul>
            </li>

        @endif

    @endforeach
</ul>
