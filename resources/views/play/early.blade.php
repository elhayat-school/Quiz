<x-app-layout>

    <div class="p-6 h-screen ">
        <div class="flex justify-end w-full ">
            <img class="w-24 lg:w-52 absolute right-0 top-0"
                src="https://media.giphy.com/media/TRub2cQyyLihIu0Ufa/giphy.gif" />
            {{-- <img class="w-24 absolute right-0 top-0" src="{{asset('assets/lamps3.gif')}}" /> --}}
            <x-application-logo />

        </div>

        <div class="w-full flex flex-col items-center space-y-10 mt-20">

            <p class="text-white text-2xl text-center">
                لم تبدأ المسابقة بعد، يرجى الإنتظار أو العودة في :
            </p>

            <img src="https://media.giphy.com/media/jV0fRmUyDAGRalG0T7/giphy.gif" class="w-32" />

            <div CountDown data-countdown-duration="{{ $seconds_to_wait }}" data-countdown-format="HH:mm:ss"
                data-countdown-step="1s"
                class="bg-white border-2 border-amber-400 rounded-full text-2xl font-semibold px-2 w-44 text-center"
                dir="ltr">
            </div>

        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
        integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('js/countdown.js') }}"></script>

</x-app-layout>
