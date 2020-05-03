<!DOCTYPE HTML>
<html>
  <meta name="viewport" content="width=device-width">
  <meta name="viewport" content="initial-scale=1.0">

  <head>
    <link type="text/css" rel="stylesheet" href="{{ mix('assets/css/app.css') }}">
    {{-- styles --}}
    @stack('styles')
  </head>

  <body>
@if (App::environment('development'))
    <div style='width: 100%;background-color: red;color: white;font-weight: bolder;font-size: 20px;text-align: center;'>
      DEVELOPMENT MODE
    </div>
@endif
    <a href='/home'>HOME</a>
    @yield('content')
    <script src="{{ asset('assets/js/app.js') }}"></script>
    {{-- scripts --}}
    @stack('scripts')
  </body>
</html>