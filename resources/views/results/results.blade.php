<x-app-layout class="h-fit">

    <div class="p-6 ">

        <div class="flex justify-end w-full ">
            <x-application-logo />
        </div>

        @if (auth()->user()->role === 'admin')
            <div class="text-center">
                <a href="{{ route('ranking.global') }}">
                    <x-results-button>
                        الترتيب العام
                    </x-results-button>
                </a>
            </div>
        @endif

        <div>
            <span class="text-amber-400 font-semibold text-xl" dir="ltr"> : نتائج الأوائل في الجولة الأخيرة</span>
        </div>

        <x-tables.ranking :results="$results" />

    </div>

</x-app-layout>
