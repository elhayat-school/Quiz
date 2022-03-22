<x-app-layout>

    <div class="p-6">
        <div class="flex justify-end w-full ">

            <x-application-logo />

        </div>


        <div class="w-full flex flex-col items-center space-y-10 mt-20">

            <p class="text-white text-2xl">
                أنت هنا مبكرًا
            </p>
            <p class="text-white text-5xl animate-bounce">
                ⌛
            </p>

            <div id="quiz-countdown" data-quiz-delay="{{ $seconds_to_wait }}"
                class="bg-white border-2 border-amber-400 rounded-full text-2xl font-semibold px-2 w-44 text-center"
                dir="ltr">
                <span id="quiz-countdown-hours">--</span>
                :
                <span id="quiz-countdown-minutes">--</span>
                :
                <span id="quiz-countdown-seconds">--</span>
            </div>
        </div>


    </div>


    <script src="{{ asset('js/countdown.js') }}"></script>

</x-app-layout>
