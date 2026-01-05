<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LTI Error - {{ config('app.name', 'SmartyCards') }}</title>
  <style>
    body {
      font-family: system-ui, -apple-system, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
      background-color: #f3f4f6;
    }

    .error-container {
      background-color: #fff;
      padding: 2rem;
      border-radius: 0.5rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      max-width: 640px;
      border-top: 4px solid #ef4444;
    }

    h1 {
      color: #dc2626;
      font-size: 1.5rem;
      margin: 0 0 1rem;
      font-weight: 600;
    }

    .error-message {
      color: #374151;
      margin: 0 0 1.5rem;
      line-height: 1.6;
    }

    .help-text {
      color: #6b7280;
      font-size: 0.875rem;
      margin: 0;
    }

    a {
      color: #7c3aed;
      text-decoration: none;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <div class="error-container">
    <h1>LTI Launch Error</h1>
    <p class="error-message">
      {{ $message }}
    </p>
    <p class="help-text">
      Contact <a href="mailto:help@umn.edu">help@umn.edu</a> if this problem persists.
    </p>
  </div>
</body>

</html>
