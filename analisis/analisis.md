# Analisis Arsitektur dan Logika Sistem Warkop CKB

Dokumen ini berisi analisis lengkap dari sisi *routing*, logika backend, *frontend*, serta rangkuman perbaikan yang telah diterapkan, beserta saran untuk pengembangan ke depan.

## 1. Analisis Routing & Logika Backend
Aplikasi Warkop CKB dibangun menggunakan *framework* Laravel (sepertinya versi terbaru dengan fitur Sanctum & Vite).
- **Struktur API**: Semua *endpoint* krusial yang mengolah data bisnis diletakkan pada `routes/api.php` dan dilindungi menggunakan middleware `auth:sanctum` serta custom middleware `ExtendTokenActivity`. Hal ini menandakan sistem menggunakan skema autentikasi berbasis *token* atau SPA *stateful authentication*, bukan sekadar *session* biasa.
- **Controller**: Controller dipisah antara urusan merender tampilan (`Web\AuthController` dan *closure* di `web.php`) dan urusan manipulasi data (`Api\ProductController`, `Api\RawMaterialController`, dsb). Pendekatan ini sangat baik karena memisahkan antara *presentation layer* dan *data layer*, sehingga di kemudian hari *backend* ini siap dikonsumsi oleh *platform* lain (seperti aplikasi *mobile*).

## 2. Analisis Logika Frontend
- **Arsitektur Rendering**: Frontend menggunakan gabungan dari *server-side rendering* menggunakan Blade untuk struktur *layout* dan UI, dikombinasikan dengan pendekatan SPA (*Single Page Application*) secara *client-side* menggunakan *vanilla* JavaScript (`fetch` API).
- **State Management & Interaktivitas**: Sistem tidak bergantung pada form submission tradisional (reload halaman), melainkan mencegat form (`event.preventDefault()`) lalu mengirim *payload* menggunakan AJAX/Fetch. 
- **Kelebihan**: Pendekatan ini memberikan pengalaman pengguna (UX) yang lebih responsif karena tidak ada transisi *loading* putih pada layar.
- **Kekurangan**: Karena tidak menggunakan *framework* reaktif seperti Vue, React, atau Livewire, manipulasi DOM masih dilakukan secara manual menggunakan sintaks `.innerHTML` dan `document.getElementById()`. Pendekatan ini rentan terhadap *bug* sinkronisasi data dengan tampilan, dan membuat struktur file `*.blade.php` menjadi sangat besar (banyak script JS di dalam file view).

## 3. Ringkasan Perbaikan yang Dilakukan

1. **Auto-refresh & Tombol Refresh pada Card Penampil**
   - **Bahan Baku**: Ditambahkan tombol *refresh* manual (`fetchRawMaterials()`) pada card "Inventori Bahan Baku". Untuk *auto-refresh*, fungsi `fetchRawMaterials()` sudah otomatis dipanggil pada akhir *block* `.then` dari proses CRUD (Add/Edit/Delete).
   - **Pembelian Supplier**: Ditambahkan fungsi `refreshPurchaseData()` yang membungkus pemanggilan *fetch* ke inventory dan supplier list. Tombol *refresh* ditambahkan berjejeran dengan tombol "+ Tambah Baris" pada form "Daftar Item Belanja".

2. **Perbaikan Button CRUD pada Halaman Raw Material & Purchase**
   - Dibuat *wrapper* dan kelas tambahan yang lebih estetik (`gap-2`, `transition`, dan ring focus indicator). *Alignment* juga telah dipastikan konsisten di area modal dan tabel.

3. **Perbaikan Card Purchase (Komponen Item Row)**
   - Form input di dalam `item-row.blade.php` kini memiliki efek fokus yang seragam (`focus:ring-1`, `focus:ring-ckb-primary`). Hal ini memperbaiki *feedback visual* bagi kasir/admin ketika menginputkan barang sehingga terkesan lebih profesional.

4. **Validasi Tanggal Invoice Info**
   - Mengubah komponen `invoice-info.blade.php` pada *field* `purchase_date` agar memiliki atribut `max="{{ date('Y-m-d') }}"`. Hal ini mencegah admin/pengguna tidak sengaja memilih tanggal belanja *future date* (besok, lusa) yang secara akuntansi stok tidak masuk akal.

## 4. Saran Pengembangan Ke Depan

Berdasarkan arsitektur *hybrid* yang ada saat ini, berikut adalah saran untuk iterasi pengembangan Warkop CKB ke depan:

1. **Transisi ke Livewire / Inertia.js**
   - Mengingat banyaknya skrip *vanilla JavaScript* untuk manipulasi DOM (seperti di `raw-materials/index.blade.php` dan `suppliers/index.blade.php`), ke depannya kode akan semakin rumit untuk dikelola (spaghetti code).
   - **Saran**: Jika *developer* lebih familiar dengan PHP, gunakan **Laravel Livewire**. Jika ingin mengadopsi ekosistem modern, gunakan **Inertia.js** dengan React atau Vue. Keduanya akan menghapus kebutuhan menulis kode `document.getElementById(...).innerHTML` secara manual.

2. **Pemisahan File JavaScript**
   - Saat ini, logika AJAX/API digabung langsung di dalam tag `<script>` pada file *Blade*. Untuk menjaga kebersihan kode, logika JS harusnya di-*extract* ke dalam file tersendiri di dalam `resources/js/` (contoh: `resources/js/rawMaterial.js`), lalu dibundling melalui **Vite** yang sudah terkonfigurasi pada `package.json`.

3. **Global API Helper & Error Handling**
   - Fungsi `apiFetch()` dan fungsi alert Swal berulang kali ditulis ulang atau rentan repetisi di setiap halaman. Sebaiknya fungsi ini diletakkan pada satu file utilitas global (misal `app.js` atau `api.js`) yang dapat di-*import* atau dipanggil di semua halaman, agar perubahan *handling* token kedaluwarsa hanya dilakukan di satu tempat.

4. **Validasi Backend yang Lebih Kuat**
   - Pastikan backend tidak hanya mengandalkan form validasi HTML (`max="..."`), tetapi controller juga memvalidasi input *date* di API: `['purchase_date' => 'required|date|before_or_equal:today']`.
