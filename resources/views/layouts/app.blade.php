<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @yield('css_scripts')

    <!-- JQuery -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google-map.api_key') }}&libraries=places">
    </script>

</head>

<body>
    <div id="app">
        @yield('content')
    </div>

    <!-- Scripts -->

    @yield('scripts')

    <script src="{{ mix('js/app.js') }}?ver=5"></script>

</body>

</html>
