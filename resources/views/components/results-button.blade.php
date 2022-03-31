<button
    {{ $attributes->merge(['type' => 'submit','class' =>'  inline-flex items-center  py-2 mt-8 px-8 text-2xl rounded-full bg-amber-400 border border-transparent rounded-full font-semibold text-lg text-amber-900 hover:bg-amber-700  active:bg-amber-700 focus:outline-none focus:text-amber-300 focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
