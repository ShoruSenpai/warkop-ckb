<div id="material-modal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">

    <div class="bg-ckb-background w-full max-w-lg rounded-2xl shadow-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-ckb-secondary flex justify-between items-center">
            <div>
                <h3 id="material-modal-title" class="text-lg font-bold text-ckb-on-primary">
                    Daftarkan Bahan Baku Baru
                </h3>
                <p class="text-xs text-gray-500">Isi informasi dasar bahan baku.</p>
            </div>

            <button type="button" onclick="closeMaterialModal()"
                    class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form
            id="material-form"
            onsubmit="submitMaterial(event)"
            class="p-6"
        >
            <input
                id="material-id"
                type="hidden"
            >
            <!-- Informasi dasar -->
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-ckb-secondary mb-1">
                        Nama Bahan Baku *
                    </label>
                    <input
                        id="rm_name"
                        type="text"
                        required
                        maxlength="100"
                        placeholder="cth: Susu UHT"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-secondary focus:outline-none bg-white"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ckb-secondary mb-1">
                        Satuan Dasar *
                    </label>
                    <select
                        id="rm_unit"
                        required
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-secondary focus:outline-none bg-white"
                    >
                        <option value="gram">gram</option>
                        <option value="ml">ml</option>
                        <option value="pcs">pcs</option>
                    </select>
                    <p class="text-[11px] text-gray-400 mt-1">
                        Satuan ini digunakan untuk menyimpan stok.
                    </p>
                </div>
            </div>

            <!-- Packaging -->
            <div class="mt-6">
                <div class="flex justify-between items-center mb-3">
                    <div>
                        <h4 class="text-sm font-bold text-ckb-secondary">
                            Kemasan Pembelian
                        </h4>
                        <p class="text-[11px] text-gray-400">
                            Tentukan satuan pembelian dan jumlah satuan dasar di dalamnya.
                        </p>
                    </div>

                    <button
                        type="button"
                        onclick="addMaterialPackagingRow()"
                        class="text-xs font-semibold text-ckb-secondary hover:text-ckb-accent"
                    >
                        + Tambah Kemasan
                    </button>
                </div>
                <div
                    id="material-packaging-list"
                    class="space-y-3"
                >
                    <!-- Packaging row dibuat via JS -->
                </div>
                <div
                    id="material-packaging-empty"
                    class="border border-dashed border-gray-300 rounded-lg p-4 text-center"
                >
                    <i class="fas fa-box-open text-gray-300 text-xl mb-2"></i>
                    <p class="text-xs text-gray-400">Belum ada kemasan pembelian.</p>

                    <button
                        type="button"
                        onclick="addMaterialPackagingRow()"
                        class="text-xs text-ckb-secondary font-semibold mt-1 hover:underline"
                    >Tambahkan kemasan</button>
                </div>
            </div>

            <!-- Action -->
            <div class="flex justify-end gap-3 mt-6">
                <button
                    type="button"
                    onclick="closeMaterialModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50"
                >Batal</button>

                <button
                    type="submit"
                    id="btn-save-material"
                    class="px-4 py-2 bg-ckb-secondary text-white rounded-lg text-sm font-medium hover:bg-ckb-accent flex items-center"
                >Simpan Bahan Baku</button>
            </div>
        </form>

        <template id="material-packaging-template">
            <div class="material-packaging-row border border-gray-200 rounded-lg p-3">
                <div class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-3 items-end">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-500 mb-1">
                            Satuan Beli *
                        </label>
                        <input
                            type="text"
                            class="material-packaging-unit w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-secondary focus:outline-none"
                            placeholder="cth: kardus"
                            maxlength="20"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-gray-500 mb-1">
                            Isi dalam Satuan Dasar *
                        </label>
                        <input
                            type="number"
                            class="material-packaging-conversion w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-secondary focus:outline-none"
                            min="0.001"
                            step="0.001"
                            placeholder="cth: 12000"
                            oninput="updateMaterialPackagingPreview(this)"
                            required
                        >
                    </div>

                    <button
                        type="button"
                        onclick="removeMaterialPackagingRow(this)"
                        class="h-[38px] w-[38px] rounded-lg bg-red-50 text-red-600 hover:bg-red-100"
                        title="Hapus kemasan"
                    >
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <p class="material-packaging-preview text-[11px] text-gray-400 mt-2">
                    Masukkan jumlah konversi.
                </p>
            </div>
        </template>

    </div>
</div>
