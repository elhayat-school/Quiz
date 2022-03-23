<button {{ $attributes->merge(['type' => 'submit', 'class' => '  inline-flex items-center px-4 py-2 bg-green-400 border border-transparent rounded-full font-semibold text-lg text-green-900 hover:bg-green-700 hover:text-green-200 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
