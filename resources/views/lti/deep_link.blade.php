<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Select Deck - {{ config('app.name', 'SmartyCards') }}</title>

  @vite(['resources/client/app.ts'])
</head>

<body class="font-sans antialiased">
  <div id="app"></div>

  <script>
    // Make launch data available to Vue app
    window.SmartyCards = window.SmartyCards || {};
    window.SmartyCards.ltiDeepLink = {
      launchId: '{{ $launch_id }}',
      settings: @json($settings)
    };
  </script>
</body>

</html>
