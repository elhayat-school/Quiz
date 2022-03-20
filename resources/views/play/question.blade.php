<form action="{{ route('anwswer.store') }}" method="POST">
    @csrf
    <input type="hidden" name="question_id" value="{{ $question->id }}" />

    <h2 dir="rtl" className="bg-gray-100 px-4 py-6 rounded-b-xl text-xl">
        {{ $question->content }}
    </h2>

    {{-- COUNTDOWN --}}

    @foreach ($question->choices as $choice)
        <div className="bg-gray-50 m-2 px-2 py-4 rounded-md shadow-sm flex items-center flex-row-reverse">
            <input name="choice_number" type="radio" value="{{ $choice->choice_number }}" className="h-6 w-6 ml-2"
                required></input>

            <label dir="rtl" className="flex-1">
                {{ $choice->content }}
            </label>
        </div>
    @endforeach

    <button className="bg-emerald-600 text-gray-50 mx-2 px-4 py-2 rounded-full font-bold">
        Répondre
    </button>
</form>
