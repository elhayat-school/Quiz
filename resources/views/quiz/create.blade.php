<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Document</title>
</head>

<body class="bg-slate-900 text-gray-200">

    <form action='{{ route('quiz.store') }}' method="POST">

        <div class="w-max m-auto">

            @csrf
            {{ _p_field() }}

            <input type="datetime-local" name="start_at" required
                class="bg-slate-700 text-white p-2 m-3 rounded-md border border-slate-500">
            <input type="number" min="0" name="duration" required
                class="bg-slate-700 text-white w-16 p-2 m-3 rounded-md border border-slate-500">

            <hr>

            @for ($i = 1; $i < 5; $i++)
                <div class="w-max p-1 flex items-baseline">
                    <div class="flex flex-col">
                        <label for="questions[{{ $i }}][content]" class="my-1 mx-2 text-2xl font-bold">
                            Question {{ $i }}
                        </label>

                        <textarea name="questions[{{ $i }}][content]" required
                            class="bg-slate-700 text-gray-100 h-32 w-56 p-2 mx-3 rounded-md border border-slate-500"
                            dir="rtl"></textarea>
                    </div>

                    @for ($j = 1; $j < 5; $j++)
                        <div class="ml-2 flex flex-col">
                            <label for="questions[{{ $i }}][choices][{{ $j }}]"
                                class="block my-1 mx-2 text-lg font-bold">
                                Choix {{ $j }}
                            </label>


                            <div>
                                <label for="questions[{{ $i }}][is_correct]" class="my-1 ml-4">
                                    Correct ?
                                </label>
                                <input type="radio" name="questions[{{ $i }}][is_correct]"
                                    value="{{ $j }}" required>
                            </div>

                            <textarea name="questions[{{ $i }}][choices][{{ $j }}]"
                                class="bg-slate-700 text-gray-100 h-24 w-56 p-2 mx-3 rounded-md border border-slate-500"
                                dir="rtl"></textarea>
                        </div>
                    @endfor

                </div>

            @endfor

            <button type="submit"
                class="bg-slate-200 text-slate-800 hover:bg-slate-300 hover:text-slate-900 m-1 mx-auto py-2 px-6 block rounded-full font-bold">
                Ajouter
            </button>

        </div>
    </form>
</body>
