<x-app-layout>
    <div class="p-6 ">

        <div class="flex justify-between w-full items-center ">

            <div id="question-countdown" data-question-duration="{{ $question->duration }}"
                class="bg-white border-2 border-amber-400 rounded-full text-2xl font-semibold px-2 w-32 text-center"
                dir="ltr">
                <span id="question-countdown-minutes">--</span>
                :
                <span id="question-countdown-seconds">--</span>
            </div>

            <x-application-logo />
        </div>
        <form action="{{ route('anwswer.store') }}" method="POST">
            @csrf
            <input type="hidden" name="question_id" value="{{ $question->id }}" />

            <h2 dir="rtl" class="px-4 py-6 rounded-b-xl text-2xl text-amber-400">
                {{ $question->content }}
            </h2>


            @foreach ($question->choices->shuffle() as $choice)
                <div class="py-4 rounded-md shadow-sm flex items-center flex-row text-white text-xl">
                    <input name="choice_number" type="radio" value="{{ $choice->choice_number }}"
                        class="h-6 w-6 ml-2 " required />

                    <label dir="rtl" class="flex-1">
                        {{ $choice->content }}
                    </label>
                </div>
            @endforeach


            <x-button class="mt-8 px-8 text-2xl rounded-full">
                تأكيد الإجابة
            </x-button>

        </form>

    </div>


    <script src="{{ asset('js/countdown.js') }}"></script>

</x-app-layout>
