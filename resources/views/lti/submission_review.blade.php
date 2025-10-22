<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Review Submission - {{ config('app.name', 'SmartyCards') }}</title>

  @vite(['resources/client/app.ts'])
</head>

<body class="font-sans antialiased">
  <div id="lti-submission-review"
       data-launch-id="{{ session('lti_launch_id') }}"
       data-for-user="{{ json_encode($for_user) }}">
  </div>

  <script>
    // Make launch data available to Vue app
    window.ltiSubmissionReview = {
      launchId: '{{ session('lti_launch_id') }}',
      forUser: @json($for_user)
    };
  </script>
</body>

</html>
