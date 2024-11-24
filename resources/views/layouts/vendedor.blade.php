<!DOCTYPE html>
<html lang="en">
<head>
    @include('adminlte::partials.common.meta')
    <title>@yield('title', config('adminlte.title', ''))</title>
    @include('adminlte::partials.common.css')
</head>
<body class="@yield('classes_body')" @yield('body_data')>
    <div class="wrapper">
        {{-- Top Navbar --}}
        @if(empty($hideNavbar))
            @include('adminlte::partials.navbar.navbar')
        @endif

        {{-- Sidebar --}}
        @if(empty($hideSidebar))
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        <div class="content-wrapper">
            {{-- Main Content --}}
            <section class="content">
                @yield('content')
            </section>
        </div>

        {{-- Footer opcional --}}
        @include('adminlte::partials.footer.footer')
    </div>

    @include('adminlte::partials.common.js')
    @yield('adminlte_js')
</body>
</html>
