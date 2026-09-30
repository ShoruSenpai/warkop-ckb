<x-layout title="Settings & Profil">
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h2 class="text-2xl font-bold text-ckb-primary">Settings & Profil</h2>
            <p class="mt-1 text-sm text-ckb-on-surface-variant">Pengaturan profil toko Warkop Cak Kebo, jam shift kasir, dan keamanan akun.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Left column: Store info & profile --}}
            <div class="space-y-6 md:col-span-2">
                {{-- User Profile Form --}}
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                    <h3 class="text-base font-bold text-ckb-primary border-b border-gray-100 pb-2">Profil Akun Saya</h3>
                    
                    <form id="form-profile-settings" class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Username</label>
                            <input type="text" id="setting-username" readonly class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-500" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                            <input type="text" id="setting-full-name" maxlength="100" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-ckb-primary" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Email</label>
                            <input type="email" id="setting-email" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-ckb-primary" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Password Baru (opsional)</label>
                            <input type="password" id="setting-password" placeholder="Kosongkan jika tidak ingin mengubah password" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm outline-none focus:border-ckb-primary" />
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" id="btn-save-profile" class="px-5 py-2.5 bg-ckb-primary text-white rounded-lg text-sm font-semibold hover:bg-ckb-accent transition">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan Profil
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Warkop Store Info --}}
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                    <h3 class="text-base font-bold text-ckb-primary border-b border-gray-100 pb-2">Informasi Toko Warkop</h3>
                    
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-xs font-semibold text-gray-500">Nama Warkop</span>
                            <p id="store-name-display" class="font-bold text-gray-800">Warkop Cak Kebo</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-gray-500">Alamat Operasional</span>
                            <p id="store-address-display" class="text-gray-700">Jl. Raya Surabaya No. 123</p>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-gray-500">Nomor Telepon</span>
                            <p id="store-phone-display" class="text-gray-700">081234567890</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right column: Shift info & system summary --}}
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                    <h3 class="text-base font-bold text-ckb-primary border-b border-gray-100 pb-2">Jadwal Shift Kasir</h3>

                    <div class="space-y-3 text-xs">
                        <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-100 text-emerald-800">
                            <p class="font-bold">Shift Pagi</p>
                            <p class="mt-1">06:00 - 14:00 WIB</p>
                        </div>
                        <div class="p-3 rounded-lg bg-blue-50 border border-blue-100 text-blue-800">
                            <p class="font-bold">Shift Sore / Malam</p>
                            <p class="mt-1">14:00 - 22:00 WIB</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-3 text-xs text-gray-600">
                    <h3 class="text-base font-bold text-ckb-primary border-b border-gray-100 pb-2">Informasi Sistem</h3>
                    <div class="flex justify-between">
                        <span>Framework:</span>
                        <span class="font-semibold text-gray-800">Laravel 11</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Database:</span>
                        <span class="font-semibold text-gray-800">MySQL (PDO)</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Integrasi API:</span>
                        <span class="font-semibold text-emerald-600">Web & Mobile Ready</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        @vite("resources/js/settings/index.js")
    </x-slot:script>
</x-layout>
