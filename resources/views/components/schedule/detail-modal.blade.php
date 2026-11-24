@props (["isOwner" => false])

<div
    id="schedule-detail-modal"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-labelledby="schedule-detail-title"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-ckb-inverse-surface/50 p-4"
>
    <div
        class="w-full max-w-lg overflow-hidden rounded-2xl bg-ckb-surface shadow-floating"
    >
        <div
            class="flex items-center justify-between border-b border-ckb-outline-variant/50 px-5 py-4 sm:px-6"
        >
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-ckb-on-surface-variant">Detail Penugasan</p>
                <h2
                    id="schedule-detail-title"
                    class="mt-1 text-lg font-bold text-ckb-primary"
                >
                    Jadwal Karyawan
                </h2>
            </div>
            <button
                type="button"
                data-modal-close
                aria-label="Tutup detail jadwal"
                class="flex size-11 items-center justify-center rounded-lg text-ckb-on-surface-variant transition hover:bg-ckb-surface-container-low hover:text-ckb-primary"
            >
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>

        <div id="schedule-detail-content" class="space-y-4 p-5 sm:p-6"></div>

        @if ($isOwner)
            <div
                id="schedule-detail-actions"
                class="flex flex-col-reverse gap-2 border-t border-ckb-outline-variant/50 px-5 py-4 sm:flex-row sm:justify-end sm:px-6"
            >
                <button
                    type="button"
                    id="btn-delete-schedule"
                    class="inline-flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded-lg border border-ckb-error/30 bg-ckb-surface px-4 py-2 text-xs font-semibold text-ckb-error transition hover:bg-ckb-error-container sm:text-sm"
                >
                    <i class="fas fa-trash" aria-hidden="true"></i>
                    Hapus
                </button>
                <button
                    type="button"
                    id="btn-edit-schedule"
                    class="inline-flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded-lg bg-ckb-primary px-4 py-2 text-xs font-semibold text-ckb-on-primary transition hover:bg-ckb-accent hover:text-ckb-on-accent sm:text-sm"
                >
                    <i class="fas fa-pen" aria-hidden="true"></i>
                    Edit Jadwal
                </button>
            </div>
        @endif
    </div>
</div>
