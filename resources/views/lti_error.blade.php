<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LTI Error - {{ config('app.name', 'SmartyCards') }}</title>
  <style>
    body {
      font-family: sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
      background-color: #f7f7f7;
    }

    .error-container {
      background-color: #fff;
      padding: 2rem;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      max-width: 640px;
    }
  </style>
</head>

<body>
  <div class="error-container">
    <h1>Canvas Launch Error</h1>
    <p class="error-message">
      {{ $message }}
    </p>
    <p class="help-text">
      Contact <a href="mailto:help@umn.edu">help@umn.edu</a> if this problem persists.
    </p>
  </div>
</body>

</html>
