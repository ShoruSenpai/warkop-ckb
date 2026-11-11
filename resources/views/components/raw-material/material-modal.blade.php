<div
    id="material-modal"
    class="fixed inset-0 bg-ckb-inverse-surface/50 hidden items-center justify-center z-50 p-4"
>
    <div
        class="bg-ckb-surface w-full max-w-lg rounded-2xl shadow-floating overflow-hidden"
    >
        <div
            class="px-6 py-4 border-b border-ckb-outline-variant/50 bg-ckb-primary flex justify-between items-center text-ckb-on-primary"
        >
            <div>
                <h3
                    id="material-modal-title"
                    class="text-lg font-bold text-ckb-on-primary"
                >
                    Daftarkan Bahan Baku Baru
                </h3>
                <p class="text-xs text-ckb-on-primary/75">Isi informasi dasar bahan baku.</p>
            </div>

            <button
                type="button"
                data-modal-close
                class="text-ckb-on-primary/70 hover:text-ckb-on-primary"
            >
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="material-form" class="p-6">
            <input id="material-id" type="hidden" />
            <!-- Informasi dasar -->
            <div class="space-y-4">
                <div>
                    <label
                        class="block text-xs font-semibold text-ckb-primary mb-1"
                    >
                        Nama Bahan Baku *
                    </label>
                    <input
                        id="rm_name"
                        type="text"
                        required
                        maxlength="50"
                        placeholder="cth: Susu UHT"
                        class="w-full border border-ckb-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-accent focus:outline-none bg-ckb-surface"
                    />
                </div>

                <div>
                    <label
                        class="block text-xs font-semibold text-ckb-primary mb-1"
                    >
                        Satuan Dasar *
                    </label>
                    <select
                        id="rm_unit"
                        required
                        class="w-full border border-ckb-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-accent focus:outline-none bg-ckb-surface"
                    >
                        <option value="gram">gram</option>
                        <option value="ml">ml</option>
                        <option value="pcs">pcs</option>
                    </select>
                    <p class="text-[11px] text-ckb-on-surface-variant mt-1">Satuan ini digunakan untuk menyimpan stok.</p>
                </div>
            </div>

            <div>
                <label
                    class="block text-xs font-semibold text-ckb-primary mb-1"
                >
                    Batas Stok Minimum *
                </label>

                <div class="relative">
                    <input
                        id="rm_minimum_stock"
                        type="number"
                        required
                        min="0"
                        max="999999"
                        placeholder="cth: 10000"
                    class="w-full border border-ckb-outline-variant rounded-lg px-3 py-2 pr-12 text-sm focus:ring-1 focus:ring-ckb-accent focus:outline-none bg-ckb-surface"
                    />

                    <span
                        id="rm-minimum-stock-unit"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-ckb-on-surface-variant"
                    >
                        gram
                    </span>
                </div>

                <p class="text-[11px] text-ckb-on-surface-variant mt-1">Sistem akan memberi peringatan saat stok mencapai atau berada di bawah batas ini.</p>
            </div>

            <!-- Packaging -->
            <div class="mt-6">
                <div class="flex justify-between items-center mb-3">
                    <div>
                        <h4 class="text-sm font-bold text-ckb-primary">
                            Kemasan Pembelian
                        </h4>

                        <p class="text-[11px] text-ckb-on-surface-variant">Tentukan satuan pembelian dan jumlah satuan dasar di dalamnya.</p>
                    </div>

                    <button
                        type="button"
                        data-action="add-packaging"
                        class="text-xs font-semibold text-ckb-primary hover:text-ckb-accent"
                    >
                        + Tambah Kemasan
                    </button>
                </div>

                <div id="material-packaging-list" class="space-y-3">
                    <!-- Packaging rows are created by JS -->
                </div>

                <div
                    id="material-packaging-empty"
                    class="border border-dashed border-ckb-outline-variant rounded-lg p-4 text-center"
                >
                    <i class="fas fa-box-open text-ckb-outline text-xl mb-2"></i>

                    <p class="text-xs text-ckb-on-surface-variant">Belum ada kemasan pembelian.</p>

                    <button
                        type="button"
                        data-action="add-packaging"
                        class="text-xs text-ckb-primary font-semibold mt-1 hover:underline"
                    >
                        Tambahkan kemasan
                    </button>
                </div>
            </div>

            <!-- Action -->
            <div class="flex justify-end gap-3 mt-6">
                <button
                    type="button"
                    data-modal-close
                    class="px-4 py-2 border border-ckb-outline-variant rounded-lg text-sm font-medium text-ckb-on-surface hover:bg-ckb-surface-container-low"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    id="btn-save-material"
                    class="px-4 py-2 bg-ckb-primary text-ckb-on-primary rounded-lg text-sm font-medium hover:bg-ckb-accent hover:text-ckb-on-accent flex items-center"
                >
                    Simpan Bahan Baku
                </button>
            </div>
        </form>

        <template id="material-packaging-template">
            <div
                class="material-packaging-row border border-ckb-outline-variant rounded-lg p-3"
            >
                <div
                    class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-3 items-end"
                >
                    <div>
                        <label
                            class="block text-[11px] font-semibold text-ckb-on-surface-variant mb-1"
                        >
                            Satuan Beli *
                        </label>

                        <input
                            type="text"
                            class="material-packaging-unit w-full border border-ckb-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-accent focus:outline-none"
                            placeholder="cth: kardus"
                            maxlength="20"
                            required
                        />
                    </div>

                    <div>
                        <label
                            class="block text-[11px] font-semibold text-ckb-on-surface-variant mb-1"
                        >
                            Isi dalam Satuan Dasar *
                        </label>

                        <input
                            type="number"
                            class="material-packaging-conversion w-full border border-ckb-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-accent focus:outline-none"
                            min="1"
                            max="999999"
                            placeholder="cth: 12000"
                            required
                        />
                    </div>

                    <button
                        type="button"
                        data-action="remove-packaging"
                        class="h-[38px] w-[38px] rounded-lg bg-ckb-error-container text-ckb-on-error-container hover:bg-ckb-error/15"
                        title="Hapus kemasan"
                    >
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <p
                    class="material-packaging-preview text-[11px] text-ckb-on-surface-variant mt-2"
                >Masukkan jumlah konversi.</p>
            </div>
        </template>
    </div>
</div>
