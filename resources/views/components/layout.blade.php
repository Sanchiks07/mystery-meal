<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mystery Meal</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <script src="{{ asset('script.js') }}" defer></script>
</head>
<body>
    <x-navigation />
    
    <div class="main-container">
        {{ $slot }}
    </div>
</body>
</html>