<div
    id="packaging-modal"
    class="fixed inset-0 bg-ckb-inverse-surface/50 hidden items-center justify-center z-50 p-4"
>
    <div
        class="bg-ckb-surface w-full max-w-3xl rounded-2xl shadow-floating overflow-hidden"
    >
        <div
            class="px-6 py-4 border-b border-ckb-outline-variant/50 bg-ckb-surface flex justify-between items-start"
        >
            <div>
                <h3
                    id="packaging-modal-title"
                    class="text-lg font-bold text-ckb-primary"
                >
                    Edit Bahan Baku
                </h3>

                <p
                    id="packaging-modal-description"
                    class="text-xs text-ckb-on-surface-variant mt-1"
                >Ubah informasi bahan dan kelola kemasan pembelian.</p>
            </div>

            <button
                type="button"
                data-modal-close
                class="text-ckb-on-surface-variant hover:text-ckb-on-surface"
            >
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6">
            {{-- Material info --}}
            <form
                id="material-edit-form"
                class="bg-ckb-surface-container-lowest border border-ckb-outline-variant rounded-xl p-4 mb-5"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-xs font-semibold text-ckb-primary mb-1"
                        >
                            Nama Bahan Baku
                        </label>

                        <input
                            id="edit-rm-name"
                            type="text"
                            maxlength="50"
                            required
                            class="w-full border border-ckb-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-accent focus:outline-none"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold text-ckb-primary mb-1"
                        >
                            Satuan Dasar
                        </label>

                        <div
                            id="packaging-base-unit"
                            class="w-full bg-ckb-surface-container-low border border-ckb-outline-variant rounded-lg px-3 py-2 text-sm text-ckb-on-surface-variant"
                        >
                            -
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button
                        type="submit"
                        id="btn-save-material-edit"
                        class="px-4 py-2 bg-ckb-primary text-ckb-on-primary rounded-lg text-sm font-medium hover:bg-ckb-accent hover:text-ckb-on-accent"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>

            {{-- Packaging form --}}
            <form
                id="packaging-form"
                class="bg-ckb-surface-container-lowest border border-ckb-outline-variant rounded-xl p-4 mb-5"
            >
                <input id="packaging-id" type="hidden" />
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label
                            class="block text-xs font-semibold text-ckb-primary mb-1"
                        >
                            Satuan Beli *
                        </label>

                        <input
                            id="packaging-unit"
                            type="text"
                            maxlength="20"
                            required
                            placeholder="cth: kardus"
                            class="w-full border border-ckb-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-accent focus:outline-none"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold text-ckb-primary mb-1"
                        >
                            Konversi *
                        </label>

                        <input
                            id="packaging-conversion"
                            type="number"
                            min="1"
                            max="999999"
                            required
                            placeholder="cth: 12000"
                            class="w-full border border-ckb-outline-variant rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-accent focus:outline-none"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-xs font-semibold text-ckb-primary mb-1"
                        >
                            Satuan Dasar
                        </label>

                        <div
                            id="packaging-base-unit-display"
                            class="w-full bg-ckb-surface-container-low border border-ckb-outline-variant rounded-lg px-3 py-2 text-sm text-ckb-on-surface-variant"
                        >
                            -
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button
                        type="button"
                        id="btn-cancel-packaging-edit"
                        class="hidden px-3 py-2 border border-ckb-outline-variant rounded-lg text-sm text-ckb-on-surface hover:bg-ckb-surface-container-low"
                    >
                        Batal Edit
                    </button>
                    <button
                        type="submit"
                        id="btn-save-packaging"
                        class="px-4 py-2 bg-ckb-primary text-ckb-on-primary rounded-lg text-sm font-medium hover:bg-ckb-accent hover:text-ckb-on-accent"
                    >
                        + Tambah Kemasan
                    </button>
                </div>
            </form>

            {{-- Packaging list --}}
            <div
                class="bg-ckb-surface border border-ckb-outline-variant rounded-xl overflow-hidden"
            >
                <div class="px-4 py-3 border-b border-ckb-outline-variant/50">
                    <h4 class="font-semibold text-sm text-ckb-primary">
                        Daftar Kemasan
                    </h4>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="text-[10px] text-ckb-on-surface-variant uppercase tracking-wider border-b border-ckb-outline-variant/50"
                            >
                                <th class="px-4 py-3">Satuan Beli</th>
                                <th class="px-4 py-3">Konversi</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="packaging-list">
                            <tr>
                                <td
                                    colspan="4"
                                    class="py-6 text-center text-ckb-on-surface-variant"
                                >
                                    Memuat kemasan...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
