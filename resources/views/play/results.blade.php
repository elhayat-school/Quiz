<x-app-layout>

    <div class="p-6">
        <div class="flex justify-end w-full ">

            <x-application-logo />

        </div>

        <div>

            <table class="text-white text-lg m-auto" dir="ltr">

                <tr>
                    <th class="border p-2"> Classement </th>
                    <th class="border p-2"> Nom </th>
                    {{-- <th class="border p-2"> Email </th> --}}
                    <th class="border p-2"> Reponses correct </th>
                    <th class="border p-2"> Durée pour repondre correctement </th>
                </tr>

                <tbody>

                    @foreach ($results as $i => $result)
                        @if ($i >= 10)
                            @if ($result->user_id !== auth()->user()->id)
                                @php
                                    continue;
                                @endphp
                            @elseif ($result->user_id === auth()->user()->id)
                                <tr class="h-8"></tr>
                            @endif
                        @endif
                        <tr class="{{ $result->user_id === auth()->user()->id ? 'bg-green-100 bg-opacity-20' : '' }}">
                            <td class="border p-2">{{ $i + 1 }}</td>
                            <td class="border p-2">{{ $result->user->name }}</td>
                            {{-- <td class="border p-2">{{ $result->user->email }}</td> --}}
                            <td class="border p-2">{{ $result->count_correct_answers }} </td>
                            <td class="border p-2">{{ $result->sum_elapsed_seconds }} seconds</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    </div>

</x-app-layout>
