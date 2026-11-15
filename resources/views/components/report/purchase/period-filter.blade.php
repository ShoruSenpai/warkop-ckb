@props ([
    "name",
    "label",
    "options" => [],
    "selected" => null,
])

<div
    class="inline-flex items-center gap-1 rounded-xl bg-ckb-secondary-container p-1"
    role="group"
    aria-label="{{ $label }}"
>
    @foreach ($options as $value => $optionLabel)
        <button
            type="button"
            data-period-control="{{ $name }}"
            data-period="{{ $value }}"
            aria-pressed="{{ $value === $selected ? 'true' : 'false' }}"
            class="min-h-11 cursor-pointer rounded-lg px-3 text-xs font-semibold transition hover:bg-ckb-surface/70 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent motion-reduce:transition-none {{ $value === $selected ? 'bg-ckb-primary text-ckb-on-primary shadow-surface' : 'text-ckb-on-secondary-container' }}"
        >
            {{ $optionLabel }}
        </button>
    @endforeach
</div>
