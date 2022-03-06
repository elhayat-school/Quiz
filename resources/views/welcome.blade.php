<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title> Quizz </title>
</head>

<body class="bg-gray-200 w-screen h-screen grid">

    <div id="mobile-frame" class="bg-white max-w-[450px] max-h-[1000px] place-self-center">
        <div class="w-[320px] h-[740px]">
            {{--  --}}
            <div id="app"></div>
            {{--  --}}
        </div>
    </div>

    <script src="{{ asset('js/manifest.js') }}"></script>
    <script src="{{ asset('js/vendor.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
