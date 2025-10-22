<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Returning to LMS...</title>
  <style>
    body {
      font-family: system-ui, -apple-system, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      margin: 0;
      background: #f3f4f6;
    }
    .loader {
      text-align: center;
    }
    .spinner {
      border: 3px solid #e5e7eb;
      border-top: 3px solid #7c3aed;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin: 0 auto 1rem;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    h2 {
      color: #374151;
      font-size: 1.25rem;
      font-weight: 500;
      margin: 0;
    }
    p {
      color: #6b7280;
      margin: 0.5rem 0 0;
    }
  </style>
</head>
<body>
  <div class="loader">
    <div class="spinner"></div>
    <h2>Returning to your LMS...</h2>
    <p>Please wait while we complete the setup.</p>
  </div>

  <form id="auto_submit_form" method="POST" action="{{ $return_url }}">
    <input type="hidden" name="JWT" value="{{ $jwt }}" />
  </form>

  <script>
    // Auto-submit the form
    document.getElementById('auto_submit_form').submit();
  </script>
</body>
</html>
