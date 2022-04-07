<x-app-layout class="h-fit">

    <div class="p-6 ">

        <img class="w-24 lg:w-52 absolute right-0 top-0"
            src="https://media.giphy.com/media/TRub2cQyyLihIu0Ufa/giphy.gif" />
        {{-- <img class="w-24 absolute right-0 top-0" src="{{asset('assets/lamps3.gif')}}" /> --}}
        <div class="flex justify-end w-full ">

            <x-application-logo />

        </div>

        <div>

            <span class="text-amber-400  font-semibold text-xl" dir="ltr"> : نتائج الأوائل في الجولة الأخيرة</span>
            <table class="text-white text-lg overflow-scroll m-auto mt-5">

                <tr>
                    <th class="border p-2"> المرتبة </th>
                    <th class="border p-2"> المشارك </th>
                    <th class="border p-2"> عدد الإجابات الصحيحة </th>
                    <th class="border p-2"> المدة الزمنية
                    </th>
                </tr>

                <tbody>

                    @foreach ($results as $rank => $result)
                        @if ($rank >= 10)
                            @if ($result->user_id !== auth()->user()->id)
                                @php
                                    continue;
                                @endphp
                            @elseif ($result->user_id === auth()->user()->id)
                                <tr class="h-8"></tr>
                            @endif
                        @endif
                        <tr class="{{ $result->user_id === auth()->user()->id ? 'bg-green-100 bg-opacity-20' : '' }}">
                            <td class="border p-2 text-center">{{ $rank + 1 }}</td>
                            <td class="border p-2 text-center">{{ $result->user->name }}</td>
                            <td class="border p-2 text-center">{{ $result->count_correct_answers }} </td>
                            <td class="border p-2 text-center">{{ $result->sum_elapsed_seconds }} ثانية</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>
