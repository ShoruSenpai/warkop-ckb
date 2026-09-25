<div id="packaging-modal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">

    <div class="bg-ckb-background w-full max-w-3xl rounded-2xl shadow-xl overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-200 bg-white flex justify-between items-start">
            <div>
                <h3
                    id="packaging-modal-title"
                    class="text-lg font-bold text-ckb-secondary"
                >
                    Edit Bahan Baku
                </h3>

                <p
                    id="packaging-modal-description"
                    class="text-xs text-gray-500 mt-1"
                >
                    Ubah informasi bahan dan kelola kemasan pembelian.
                </p>
            </div>

            <button
                type="button"
                data-modal-close
                class="text-gray-400 hover:text-gray-600"
            >
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>


        <div class="p-6">

            {{-- Material info --}}
            <form
                id="material-edit-form"
                class="bg-white border border-gray-200 rounded-xl p-4 mb-5"
            >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-xs font-semibold text-ckb-secondary mb-1">
                            Nama Bahan Baku
                        </label>

                        <input
                            id="edit-rm-name"
                            type="text"
                            maxlength="100"
                            required
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-secondary focus:outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ckb-secondary mb-1">
                            Satuan Dasar
                        </label>

                        <div
                            id="packaging-base-unit"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-500"
                        >
                            -
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button
                        type="submit"
                        id="btn-save-material-edit"
                        class="px-4 py-2 bg-ckb-secondary text-white rounded-lg text-sm font-medium hover:bg-ckb-accent"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>


            {{-- Packaging form --}}
            <form
                id="packaging-form"
                class="bg-white border border-gray-200 rounded-xl p-4 mb-5"
            >
                <input
                    id="packaging-id"
                    type="hidden"
                >
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-ckb-secondary mb-1">
                            Satuan Beli *
                        </label>

                        <input
                            id="packaging-unit"
                            type="text"
                            maxlength="20"
                            required
                            placeholder="cth: kardus"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-secondary focus:outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ckb-secondary mb-1">
                            Konversi *
                        </label>

                        <input
                            id="packaging-conversion"
                            type="number"
                            min="0.001"
                            step="0.001"
                            required
                            placeholder="cth: 12000"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-ckb-secondary focus:outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-ckb-secondary mb-1">
                            Satuan Dasar
                        </label>

                        <div
                            id="packaging-base-unit-display"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-500"
                        >
                            -
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <button
                        type="button"
                        id="btn-cancel-packaging-edit"
                        class="hidden px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50"
                    >
                        Batal Edit
                    </button>
                    <button
                        type="submit"
                        id="btn-save-packaging"
                        class="px-4 py-2 bg-ckb-secondary text-white rounded-lg text-sm font-medium hover:bg-ckb-accent"
                    >
                        + Tambah Kemasan
                    </button>
                </div>
            </form>


            {{-- Packaging list --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

                <div class="px-4 py-3 border-b border-gray-100">
                    <h4 class="font-semibold text-sm text-ckb-secondary">
                        Daftar Kemasan
                    </h4>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                        <tr class="text-[10px] text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="px-4 py-3">
                                Satuan Beli
                            </th>
                            <th class="px-4 py-3">
                                Konversi
                            </th>
                            <th class="px-4 py-3">
                                Status
                            </th>
                            <th class="px-4 py-3 text-right">
                                Aksi
                            </th>
                        </tr>
                        </thead>
                        <tbody id="packaging-list">
                        <tr>
                            <td
                                colspan="4"
                                class="py-6 text-center text-gray-400"
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
