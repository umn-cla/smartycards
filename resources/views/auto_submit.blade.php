<!DOCTYPE html>
<html>

<head>
  <title>Submitting...</title>
</head>

<body onload="document.getElementById('form').submit()">
  <form id="form" action="{{ $return_url }}" method="post">
    <input type="hidden" name="JWT" value="{{ $jwt }}">
    <input type="submit" value="Click here if you are not automatically redirected">
  </form>
</body>

</html>
