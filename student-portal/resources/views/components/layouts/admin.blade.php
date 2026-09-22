@props(['title' => null])

<x-layouts.dashboard :title="$title">
    <x-slot:header>
        <x-admin.header />
    </x-slot:header>

    <x-slot:sidebar>
        <x-admin.sidebar />
    </x-slot:sidebar>

    <x-slot:footer>
        <x-admin.footer />
    </x-slot:footer>

    {{ $slot }}
</x-layouts.dashboard>
