@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-xl text-amber-400', 'dir' => 'rtl']) }}>
    {{ $value ?? $slot }}
</label>
