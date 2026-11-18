@props ([
    "group",
    "label",
    "options" => [],
    "selected" => null,
])

<div
    class="flex flex-wrap items-center gap-1 rounded-xl bg-ckb-secondary-container p-1"
    role="group"
    aria-label="{{ $label }}"
>
    @foreach ($options as $value => $optionLabel)
        <button
            type="button"
            data-filter-group="{{ $group }}"
            data-filter-value="{{ $value }}"
            aria-pressed="{{ $value === $selected ? 'true' : 'false' }}"
            class="min-h-8 cursor-pointer rounded-lg px-3 text-xs font-semibold focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent {{ $value === $selected ? 'bg-ckb-primary text-ckb-on-primary shadow-surface' : 'text-ckb-on-secondary-container transition hover:bg-ckb-surface/70 motion-reduce:transition-none' }}"
        >
            {{ $optionLabel }}
        </button>
    @endforeach
</div>
