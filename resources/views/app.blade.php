<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="theme-color" content="#2E091E">
  <link rel="icon" href="favicon.svg">
  <link rel="mask-icon" href="mask-icon.svg" color="#2E091E">
  <link rel="apple-touch-icon" href="apple-touch-icon.png">
  <link rel="manifest" href="/manifest.json">

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'SmartyCards') }}</title>
  <meta name="description" content="Create and share digital flashcards and study games">
  <meta name="keywords" content="digital flashcards, college, studying">
  <meta name="google-site-verification" content="nSRuzVbBSDeCpknMIUeBWlOg4h9jqUXNj2J6vpRrNW4" />
  <meta property="og:title" content="SmartyCards">
  <meta name="twitter:title" content="SmartyCards">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="en_US">
  <meta property="og:site_name" content="SmartyCards">
  <meta name="twitter:card" content="summary_large_image">
  <meta property="og:image" content="https://smartycards.cla.umn.edu/img/og-image.png">
  <meta name="twitter:image" content="https://smartycards.cla.umn.edu/img/og-image.png">
  <meta property="og:image:alt" content="SmartyCards - Digital Flashcards Platform">
  <meta name="twitter:image:alt" content="SmartyCards - Digital Flashcards Platform">
  <meta property="og:description" content="Create and share digital flashcards and study games.">
  <meta name="twitter:description" content="Create and share digital flashcards and study games.">

  <!-- Scripts -->
  @vite(['resources/client/app.ts'])
</head>

<body class="font-sans antialiased">
  <div id="app"></div>
</body>

</html>
