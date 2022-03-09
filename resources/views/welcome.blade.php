<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title> Quizz </title>
</head>

<body class="bg-center bg-cover w-screen h-screen grid" style="background-image: url('/assets/stars.svg')">

    <div id="mobile-frame" class="bg-white w-full h-full max-w-[450px] max-h-[920px] place-self-center">
        {{--  --}}
        <div id="root" class="flex h-full flex-col"></div>
        {{--  --}}
    </div>

    <script src="{{ asset('js/manifest.js') }}"></script>
    <script src="{{ asset('js/vendor.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
