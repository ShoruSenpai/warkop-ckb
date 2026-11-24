<div
    id="schedule-form-modal"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-labelledby="schedule-form-title"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-ckb-inverse-surface/50 p-4"
>
    <div
        class="max-h-[calc(100dvh-2rem)] w-full max-w-xl overflow-y-auto rounded-2xl bg-ckb-surface shadow-floating"
    >
        <div
            class="flex items-center justify-between border-b border-ckb-outline-variant/50 bg-ckb-primary px-5 py-4 text-ckb-on-primary sm:px-6"
        >
            <div>
                <h2 id="schedule-form-title" class="text-lg font-bold">
                    Tambah Jadwal
                </h2>
                <p class="mt-1 text-xs text-ckb-on-primary/75">Pilih karyawan dan isi waktu penugasannya.</p>
            </div>
            <button
                type="button"
                data-modal-close
                aria-label="Tutup form jadwal"
                class="flex size-11 items-center justify-center rounded-lg text-ckb-on-primary/80 transition hover:bg-ckb-on-primary/10 hover:text-ckb-on-primary"
            >
                <i class="fas fa-times" aria-hidden="true"></i>
            </button>
        </div>

        <form id="schedule-form" novalidate class="space-y-4 p-5 sm:p-6">
            <input type="hidden" name="id" />
            <div>
                <label
                    for="schedule-employee"
                    class="mb-1 block text-xs font-semibold text-ckb-primary"
                    >Karyawan <span aria-hidden="true">*</span></label
                >
                <select
                    id="schedule-employee"
                    name="employee_id"
                    required
                    class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-sm text-ckb-on-surface outline-none focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent"
                >
                    <option value="">Pilih karyawan aktif</option>
                </select>
                <p id="schedule-error-employee_id" class="mt-1 hidden text-xs text-ckb-error" aria-live="polite"></p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label
                        for="schedule-position"
                        class="mb-1 block text-xs font-semibold text-ckb-primary"
                        >Posisi</label
                    >
                    <input
                        id="schedule-position"
                        type="text"
                        readonly
                        class="min-h-11 w-full rounded-lg border border-ckb-outline-variant/70 bg-ckb-surface-container-low px-3 py-2 text-sm text-ckb-on-surface-variant"
                    />
                    <p class="mt-1 text-[10px] text-ckb-on-surface-variant">Mengikuti data karyawan.</p>
                </div>
                <div>
                    <label
                        for="schedule-shift-name"
                        class="mb-1 block text-xs font-semibold text-ckb-primary"
                        >Nama Shift <span aria-hidden="true">*</span></label
                    >
                    <input
                        id="schedule-shift-name"
                        name="shift_name"
                        type="text"
                        maxlength="50"
                        required
                        placeholder="Contoh: Pagi"
                        class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-sm text-ckb-on-surface outline-none focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent"
                    />
                    <p id="schedule-error-shift_name" class="mt-1 hidden text-xs text-ckb-error" aria-live="polite"></p>
                </div>
            </div>

            <div>
                <label
                    for="schedule-date"
                    class="mb-1 block text-xs font-semibold text-ckb-primary"
                    >Tanggal <span aria-hidden="true">*</span></label
                >
                <input
                    id="schedule-date"
                    name="schedule_date"
                    type="date"
                    required
                    class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-sm text-ckb-on-surface outline-none focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent"
                />
                <p id="schedule-error-schedule_date" class="mt-1 hidden text-xs text-ckb-error" aria-live="polite"></p>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label
                        for="schedule-start-time"
                        class="mb-1 block text-xs font-semibold text-ckb-primary"
                        >Jam Mulai <span aria-hidden="true">*</span></label
                    >
                    <input
                        id="schedule-start-time"
                        name="start_time"
                        type="time"
                        required
                        class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-sm text-ckb-on-surface outline-none focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent"
                    />
                    <p id="schedule-error-start_time" class="mt-1 hidden text-xs text-ckb-error" aria-live="polite"></p>
                </div>
                <div>
                    <label
                        for="schedule-end-time"
                        class="mb-1 block text-xs font-semibold text-ckb-primary"
                        >Jam Selesai <span aria-hidden="true">*</span></label
                    >
                    <input
                        id="schedule-end-time"
                        name="end_time"
                        type="time"
                        required
                        class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-sm text-ckb-on-surface outline-none focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent"
                    />
                    <p id="schedule-error-end_time" class="mt-1 hidden text-xs text-ckb-error" aria-live="polite"></p>
                </div>
            </div>

            <p id="schedule-form-error" class="hidden rounded-lg border border-ckb-error/30 bg-ckb-error-container px-3 py-2 text-xs text-ckb-on-error-container" role="alert"></p>
            <div
                class="flex flex-col-reverse gap-2 border-t border-ckb-outline-variant/50 pt-4 sm:flex-row sm:justify-end"
            >
                <button
                    type="button"
                    data-modal-close
                    class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg border border-ckb-outline-variant bg-ckb-surface px-4 py-2 text-xs font-semibold text-ckb-on-surface transition hover:bg-ckb-surface-container-low sm:text-sm"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    id="btn-save-schedule"
                    class="inline-flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded-lg bg-ckb-primary px-4 py-2 text-xs font-semibold text-ckb-on-primary transition hover:bg-ckb-accent hover:text-ckb-on-accent sm:text-sm"
                >
                    <i class="fas fa-save" aria-hidden="true"></i>
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>
