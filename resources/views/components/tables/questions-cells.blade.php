@props(['questions'])

@foreach ($questions as $question)
    <td class="p-2 border border-gray-500" dir="rtl">

        <div class="mb-4 font-bold">
            {{ $question->content }}
        </div>

        <ol class="list-decimal">
            @foreach ($question->choices as $choice)
                <li class="mr-8">
                    {{ $choice->content }}
                    {{ $choice->is_correct ? '✔️' : '❌' }}
                </li>
            @endforeach
        </ol>

    </td>
@endforeach
