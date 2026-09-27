<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,
initial-scale=1.0">
<title>@yield('title', 'Daily Crumb')</title>
@vite(['resources/js/app.js'])
</head>
<body>
@hasSection('hideNav')
@else
@include('includes.nav')
@endif
@yield('content')
</body>
</html>
