@php
    use Illuminate\Support\Facades\Route;
	$routeName = Route::current()->getName();
@endphp

<ul class="nav nav-sidebar" data-nav-type="accordion">
    <li class="nav-item-header"></li>
    <li class="nav-item">
        <a href='{{route('dashboard')}}' class="nav-link {{($routeName == 'dashboard') ? "active" : ''}}">
            <i class="ph-house"></i><span>{{st('menu.dashboard')}}</span>
        </a>
    </li>
    @foreach(config('subsystemMenu',[]) as $name => $info)
        @if($info)
            @if(!$info['child'])
                @if(Route::has($info['routeName']))
                    @php
                        $url = route($info['routeName'])
                    @endphp
                @else
                    @php
                        $url = route('dashboard')
                    @endphp
                @endif
                <li class="nav-item">
                    <a href="{{$url}}"
                       class="nav-link {{($info['routeName'] == $routeName || in_array($routeName,$info['active'] ?? [])) ? "active" : ''}}">
                        <i class="@isset($info['icon']){{$info['icon']}}@endisset"></i><span>{!!st("menu.$name")!!}</span>
                    </a>
                </li>
            @else
                <li class="nav-item nav-item-submenu">
                    <a class="nav-link {{(in_array($routeName,activeMenu($name))) ? "active" : ''}}">
                        <i class="@isset($info['icon']){{$info['icon']}}@endisset"></i><span>{!!st("menu.$name")!!}</span>
                    </a>
                    <ul class="nav-group-sub collapse" data-submenu-title="{!!$name!!}"
                        @if(in_array($routeName,activeMenu($name)))style="display: block;"@endif>
                        @foreach($info['child'] as $childName => $items )
                            @if($items)
                                @if(Route::has($items['routeName']))
                                    @php
                                        $url = route($items['routeName'])
                                    @endphp
                                @else
                                    @php
                                        $url = route('dashboard')
                                    @endphp
                                @endif
                                <li class="nav-item {{(in_array($routeName,activeSubMenu($name,$childName))) ? "active" : ''}}">
                                    <a href='{{$url}}'
                                       class="nav-link {{(in_array($routeName,activeSubMenu($name,$childName))) ? "active" : ''}}">
                                        <span> {!!st("menu.$childName")!!} </span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
            @endif
        @endif
    @endforeach
</ul>
