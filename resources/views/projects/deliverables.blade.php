<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Deliverables
        </h2>
    </x-slot>

    <livewire:pages.project-deliverables :project="$project" />
</x-app-layout>
