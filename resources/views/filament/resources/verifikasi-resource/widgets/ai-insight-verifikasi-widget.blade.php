<x-filament-widgets::widget>
    <x-filament::section>
        <div class="prose max-w-none dark:prose-invert">
            {!! Str::markdown($insight ?? 'Memuat insight...') !!}
        </div>
    </x-filament::section>
</x-filament-widgets::widget>