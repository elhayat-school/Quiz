<x-app-layout class="h-fit">

    <div class="p-6 ">

        <div class="flex justify-end w-full ">
            <x-application-logo />
        </div>

        <div>
            <span class="text-amber-400  font-semibold text-xl" dir="ltr"> :النتائج العامة لشهر رمضان </span>
        </div>

        <x-tables.ranking :results="$results" />

    </div>

</x-app-layout>
