<tr
    class="border-b border-ckb-outline-variant/30 text-xs last:border-0 hover:bg-ckb-surface-container-low sm:text-sm"
>
    <td
        data-field="invoice_number"
        class="whitespace-nowrap px-3 py-2.5 font-semibold text-ckb-primary"
    ></td>
    <td
        data-field="supplier_name"
        class="min-w-48 px-3 py-2.5 text-ckb-on-surface"
    ></td>
    <td
        data-field="purchase_date"
        class="whitespace-nowrap px-3 py-2.5 text-ckb-on-surface-variant"
    ></td>
    <td class="whitespace-nowrap px-3 py-2.5">
        <span data-field="status" class="inline-flex min-h-7 items-center rounded-md bg-ckb-surface-container-low px-2.5 text-[11px] font-semibold text-ckb-on-surface-variant"></span>
    </td>
    <td
        data-field="items_count"
        class="whitespace-nowrap px-3 py-2.5 text-ckb-on-surface-variant"
    ></td>
    <td
        data-field="total_cost"
        class="whitespace-nowrap px-3 py-2.5 text-right font-semibold text-ckb-primary"
    ></td>
    <td class="whitespace-nowrap px-3 py-2.5 text-right">
        <a
            data-invoice-link
            href="{{ route('reports.purchases.index') }}"
            class="inline-flex min-h-11 cursor-pointer items-center gap-2 rounded-lg bg-ckb-primary px-3 text-xs font-semibold text-ckb-on-primary transition hover:bg-ckb-accent active:translate-y-px focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent motion-reduce:transition-none"
        >
            Detail
            <i class="fas fa-chevron-right text-[10px]" aria-hidden="true"></i>
        </a>
        <button
            type="button"
            data-delete-invoice
            aria-label="Hapus invoice pembelian"
            class="ml-1 inline-flex min-h-11 min-w-11 cursor-pointer items-center justify-center rounded-lg border border-ckb-error/30 px-3 text-xs font-semibold text-ckb-error transition hover:bg-ckb-error-container focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent motion-reduce:transition-none"
            title="Hapus invoice menunggu"
        >
            Hapus
        </button>
    </td>
</tr>
