<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('backpack.base.html_direction') }}">
<head>
    @include(backpack_view('inc.head'))
</head>
<body class="{{ config('backpack.base.body_class') }}">
  @include(backpack_view('inc.main_header'))
  <div class="app-body">
    <main class="pt-4 pl-3" style="width:100%;">

      <div class="px-3">
        @yield('content')
      </div>
    </main>
  </div>
 
  @yield('before_scripts')
  @stack('before_scripts')

  @include(backpack_view('inc.scripts'))

  @yield('after_scripts')
  @stack('after_scripts')

</body>
</html>
