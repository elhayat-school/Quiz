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

    <table class="m-4">
        <tr>
            <th></th>
            <th></th>
            <th class="p-2 border border-gray-500"> Question 1 </th>
            <th class="p-2 border border-gray-500"> Question 2 </th>
            <th class="p-2 border border-gray-500"> Question 3 </th>
            <th class="p-2 border border-gray-500"> Question 4 </th>
            <th class="p-2 border border-gray-500"> Participation stats </th>
        </tr>

        @foreach ($quizzes as $quiz)
            <tr>

                <td class="p-1 border border-gray-500 text-center">
                    QUIZ n°{{ $quiz->id }} <br>

                    @if (!$quiz->participation_stats)
                        <form action="{{ route('quizzes.done_state', ['quiz' => $quiz->id]) }}" method="post">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="new_state" value="{{ !$quiz->done ? 'done' : 'not-done' }}">

                            <button type="submit"
                                class="m-1 p-2 font-bold rounded-full border {{ !$quiz->done? 'bg-orange-700 hover:bg-orange-600 border-orange-700': 'bg-blue-700 hover:bg-blue-600 border-blue-700' }}">
                                {{ !$quiz->done ? 'Mark as done' : 'Mark as not done' }}
                            </button>

                        </form>
                    @elseif ($quiz->done && $quiz->participation_stats)
                        DONE
                    @endif
                </td>

                <td class="p-1 border border-gray-500 text-center">
                    <div class="">
                        {{ $quiz->start_at }}
                    </div>
                    ----
                    <div class="">
                        {{ gmdate('i:s', $quiz->duration) }}
                    </div>
                </td>

                <x-tables.questions-cells :questions="$quiz->questions" />

                <td class="p-1 border border-gray-500 text-center">
                    @if ($quiz->done)
                        @if (!$quiz->participation_stats)
                            <form action="{{ route('quizzes.cache_participation_stats', ['quiz' => $quiz->id]) }}"
                                method="post">
                                @csrf
                                @method('PUT')

                                <button type="submit"
                                    class="bg-blue-700 hover:bg-blue-600 m-1 p-2 font-bold rounded-full border border-blue-700">
                                    Caluclate stats
                                </button>
                            </form>
                        @elseif ($quiz->participation_stats)
                            <ul>
                                @foreach (explode('-', $quiz->participation_stats) as $stat)
                                    {{-- USE ACCESSOR --}}
                                    <li>
                                        {{ $stat }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @endif
                </td>
            </tr>
        @endforeach

    </table>

</body>

</html>
