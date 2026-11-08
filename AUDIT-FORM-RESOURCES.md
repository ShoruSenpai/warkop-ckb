# Audit `resources` dan aturan form

Tanggal pemeriksaan: 8 November 2026  
Cakupan: `resources/views`, `resources/js`, `resources/css`, serta endpoint/controller terkait untuk memeriksa kontrak form. Bagian ringkasan di bawah mendokumentasikan kondisi awal; status perbaikannya dicatat pada pembaruan implementasi.

## Pembaruan implementasi

Status pembaruan: selesai untuk perubahan yang dapat dilakukan dari `resources`.

| Berkas | Perubahan |
| --- | --- |
| `resources/views/components/supplier/item-row.blade.php` | Menambahkan `data-action="remove-row"` pada tombol hapus agar listener pembelian supplier dapat menghapus baris. |
| `resources/js/product/form.js` | Mengirim perubahan produk memakai `POST` dan `_method=PUT`, sehingga `FormData` terbaca Laravel; menambahkan validasi resep; kemasan hanya dikirim untuk produk `static`; menghapus log payload; membatasi unggahan gambar baru sampai 5 MB. |
| `resources/views/components/product/product-form.blade.php` | Menyamakan panjang nama produk menjadi 100 karakter, harga menjadi integer, dan menyembunyikan input gambar pada mode edit karena endpoint edit belum menerima berkas gambar. |
| `resources/js/product/packaging.js` dan `resources/views/components/product/packaging-manager.blade.php` | Menampilkan satuan dasar produk static sebagai `pcs`, mencegah kemasan draft duplikat, menangani tombol Enter pada modal, dan menyamakan batas satuan pembelian menjadi 50 karakter. |
| `resources/js/product/category.js` dan `resources/views/components/product/category-modal.blade.php` | Menampilkan error validasi kategori dari respons API yang benar, menghindari dialog ganda saat sesi berakhir, menyamakan batas nama kategori menjadi 100 karakter, dan menandainya wajib pada HTML. |
| `resources/views/auth/login.blade.php` | Menyamakan label dengan kontrak email serta menghapus checkbox “Ingat sesi” dan badge shift template. |
| `resources/views/products/index.blade.php` dan `resources/js/product/list.js` | Menahan navigasi tombol “Tambah Produk” saat daftar kategori kosong dan menampilkan pesan agar kategori ditambahkan terlebih dahulu. |
| `resources/js/raw-material/packaging.js` | Menampilkan satuan dasar pada kedua tempat di modal edit kemasan bahan baku. |
| `resources/views/components/raw-material/material-modal.blade.php`, `resources/views/components/raw-material/packaging-modal.blade.php`, dan `resources/js/raw-material/table.js` | Menyamakan batas nama bahan menjadi 100 karakter, menambah opsi `pcs`, memperbaiki satuan awal minimum stok menjadi `gram`, serta memperbaiki `colspan` tabel kosong menjadi tujuh kolom. |
| `resources/views/components/layout.blade.php` dan `resources/js/utils/api.js` | Menambahkan meta CSRF serta URL login yang digunakan helper API, mengganti kelas latar yang tidak didefinisikan, dan membuat HTTP error diproses sekali oleh pemanggil agar dialog error tidak berulang. |

## Temuan lanjutan dan perubahan di luar `resources`

Berikut pekerjaan yang tidak diubah karena berada di luar folder `resources` dan perlu diputuskan sebelum implementasi.

1. `ProductController::update()` belum memvalidasi atau menyimpan field file `image`. Input gambar pada halaman edit disembunyikan sampai endpoint tersebut mendukung unggahan.
2. Pembuatan bahan baku dan kemasannya belum atomik di backend. Bila penyimpanan kemasan gagal, bahan baku dapat tetap tersimpan tanpa kemasan. Perbaikannya memerlukan endpoint/transaksi database di `app/Http`.
3. Validasi backend masih perlu diselaraskan: `minimum_stock` harus memiliki `min:0`; nomor invoice dan nama supplier perlu batas panjang; tanggal pembelian perlu dicegah dari masa depan pada server.
4. Instalasi Laravel Boost telah menambahkan dependensi, tetapi `php artisan boost:install` tidak tersedia pada paket yang terpasang. `AGENTS.md` belum diganti oleh panduan baru.
5. Kompilasi Vite berhasil, tetapi memberi peringatan bahwa `vite.config.js` masih memakai `__dirname` dan bundle JavaScript utama melebihi 500 kB. Keduanya berada di luar `resources`.

Perubahan `AuthController::processLogin()` untuk remember tidak diperlukan lagi karena kontrol remember telah dihapus dari form login dan aplikasi tidak menawarkan fitur tersebut.

## Temuan baru di `resources`

1. Layout admin sebelumnya tidak memiliki meta `csrf-token` dan `login-url`, walaupun `resources/js/utils/api.js` membacanya. Keduanya telah ditambahkan.
2. Kelas `bg-ckb-bg` dipakai pada layout, tetapi token CSS yang tersedia adalah `bg-ckb-background`. Kelas telah diperbaiki.
3. Satuan awal di form bahan baku menampilkan `ml` saat pilihan awalnya `gram`; tampilan telah disamakan.

## Verifikasi

`npm.cmd run build` berhasil dijalankan. Build mengonfirmasi seluruh modul JavaScript dan Blade yang diubah dapat diproses oleh Vite. Tidak ada pengujian request HTTP atau database karena cakupan perbaikan dibatasi pada `resources`.

## Ringkasan temuan

### Masalah yang berisiko membuat form gagal atau berperilaku keliru

1. **Baris pembelian supplier tidak bisa dihapus.** Tombol hapus pada `resources/views/components/supplier/item-row.blade.php` tidak memiliki `data-action="remove-row"`, sementara `resources/js/supplier/rows.js` hanya mendengarkan aksi tersebut. Tombol terlihat aktif tetapi tidak melakukan apa pun.
2. **Edit produk dengan gambar tidak didukung oleh kontrak saat ini.** `resources/js/product/form.js` mengirim `FormData` menggunakan HTTP `PUT` dan menambahkan `_method=PUT`. Upload multipart dengan `PUT` tidak diproses seperti multipart `POST` pada PHP/Laravel. Selain itu, `ProductController::update()` hanya menerima `image_url`, bukan field file `image`; akibatnya gambar yang dipilih pada halaman edit tidak tersimpan.
3. **Aturan kategori tidak konsisten.** Input kategori membatasi 50 karakter, JS juga memeriksa 100 karakter, dan API menerima sampai 100. Pengguna tidak dapat memasukkan nilai yang diizinkan backend di atas 50 karakter.
4. **Kemasan produk dibawa saat jenis stok bukan `static`.** Di mode tambah, payload tetap mengirim kemasan draft walau pengguna sudah mengganti jenis stok ke `recipe`/`untracked`; API melarang `packagings` pada jenis stok tersebut. Sembunyinya bagian kemasan tidak menghapus state/payload.
5. **Validasi resep belum tuntas di sisi browser.** Tombol simpan mengirim baris resep kosong atau tanpa bahan terpilih; baru API yang menolak karena resep memerlukan sedikitnya satu bahan dan jumlah positif. Pengguna mendapatkan kesalahan umum setelah submit.
6. **Pembuatan bahan baku dan kemasannya adalah dua rangkaian request.** Bahan baku dibuat lebih dulu, lalu setiap kemasan disimpan satu per satu. Jika request kemasan gagal, bahan sudah tersimpan sebagian; menekan simpan lagi berpotensi membuat bahan duplikat. Tidak ada rollback/penanda pemulihan di sisi UI.
7. **Checkbox “Ingat sesi” pada login tidak dipakai.** Form mengirim `remember`, tetapi `AuthController::processLogin()` memanggil `Auth::attempt($credentials)` tanpa meneruskan nilai remember.
8. **Kategori salah menangani kegagalan API.** Pada respons 422, `category.js` memanggil `showApiError(response, ...)`, padahal argumen pertama seharusnya objek data JSON. Pesan validasi API jadi generik. Untuk 401/403/404/500, `apiFetch()` melempar error lebih dulu; blok `if (!response.ok)` terkait tidak berjalan.
9. **Unit dasar pada form kemasan bahan baku tidak diperbarui di tempat yang ditampilkan.** `packaging.js` mengisi `#packaging-base-unit`, sedangkan form konversi menampilkan `#packaging-base-unit-display`. Tampilan unit dasar di samping input konversi tetap `-`.
10. **Kontrak validasi backend lebih longgar/berbeda dari UI pada beberapa field.** Contoh: `minimum_stock` API hanya `numeric` (tanpa `min:0`), sedangkan UI melarang nilai negatif; API mengizinkan satuan `pcs`, tetapi form tambah hanya menawarkan `gram` dan `ml`; API nama produk maksimal 100, form maksimal 50; API kemasan produk maksimal 50, input create maksimal 20. Validasi UI tidak boleh menjadi satu-satunya pelindung.
11. **Kesalahan jaringan dapat memunculkan dua dialog.** `apiFetch()` sudah menampilkan SweetAlert lalu melempar `NETWORK_ERROR`; handler form yang menangkapnya biasanya menampilkan SweetAlert kedua dengan pesan generik. Tentukan satu lapisan sebagai pemilik notifikasi agar satu kegagalan hanya menampilkan satu pesan.

### Masalah pemisahan dan konsistensi

- Pemecahan Blade menjadi halaman, layout, dan komponen produk/supplier/bahan baku cukup jelas. Namun alur bahan baku menyimpan bahan dan kemasan dalam dua transaksi jaringan terpisah, sedangkan UI menyajikannya sebagai satu operasi simpan.
- `product/form.js` mengatur pengambilan data, pembentukan payload, pengiriman, status tombol, dan preview gambar. `product/packaging.js` serta `recipe.js` mengubah singleton `state` yang sama. Ketergantungan implisit ini membuat perubahan jenis stok atau mode edit mudah meninggalkan state lama.
- Nama `index.js` digunakan untuk entry point produk, bahan baku, supplier, dan laporan. Ini normal untuk entry point per halaman, tetapi perlu dipastikan hanya entry point halaman yang dimuat agar listener tidak terpasang ganda. Saat ini pemuatan halaman dilakukan melalui slot script masing-masing.
- Gaya import alias `@/utils/...` dan path relatif bercampur. Alias sudah didefinisikan di `vite.config.js`, jadi tidak otomatis menjadi error; konsistensi akan membantu pemindahan file dan pembacaan dependensi.
- `packaging-manager.blade.php` mendefinisikan `#packaging-form` sebagai `div`, dan `product/packaging.js` menyimpan hanya melalui klik tombol. Enter dari input tidak punya jalur submit form standar.
- Tombol hapus baris supplier adalah contoh ketidaksamaan markup dan listener. Untuk kontrol berbasis delegasi, nilai `data-action` pada Blade harus sama persis dengan nilai yang ditangani JS.
- JS produk mencatat semua pasangan payload ke `console.log` sebelum menyimpan. Ini membuat data form dan metadata berkas tampil di console produksi; hapus log saat perbaikan.
- Pada tabel bahan baku, keadaan kosong memakai `colspan="6"`, sementara tabel berisi tujuh kolom. Bukan kegagalan submit, tetapi tata letak tabel kosong tidak selaras.
- `navbar` dan `sidebar` sama-sama menampilkan form logout di setiap halaman admin. Keduanya mengirim POST dan CSRF dengan benar, tetapi ada aksi logout duplikat.

## Aturan setiap form

Aturan berikut adalah kontrak yang sebaiknya dipertahankan saat form diperbaiki. Validasi frontend membantu pengguna; validasi backend tetap wajib dan menjadi sumber kebenaran.

| Form / lokasi | Aturan data dan perilaku | Status yang perlu diingat |
| --- | --- | --- |
| Login — `views/auth/login.blade.php` | `email` dan `password` wajib; autentikasi memakai email. Pesan error harus tetap tampil setelah redirect. | Checkbox remember telah dihapus sesuai keputusan pengguna. Label email telah disamakan; tautan lupa sandi dan bantuan memakai `href="#"` sehingga belum berfungsi. |
| Logout — `components/navbar.blade.php`, `components/sidebar.blade.php` | POST ke route logout dengan `@csrf`; logout menginvalidasi sesi dan meregenerasi token CSRF. | Dua form identik muncul di setiap halaman admin. |
| Tambah kategori — `components/product/category-modal.blade.php`, `js/product/category.js` | Nama wajib setelah trim, unik, string, maksimal 100 karakter menurut API; setelah sukses, muat ulang daftar/filter lalu tutup modal. Cegah submit ganda saat request berlangsung. | Batas HTML sekarang 50 sedangkan JS/API 100. Tampilkan `data` JSON saat 422, bukan objek `Response`. Catch 401 semestinya mengikuti perilaku umum `apiFetch`. |
| Tambah produk — `components/product/product-form.blade.php`, `js/product/form.js` | Kategori, nama, harga, dan jenis stok wajib. Harga integer ≥ 0. `stock_type` hanya `static`, `recipe`, atau `untracked`. `recipe` wajib memiliki ≥1 bahan yang berbeda, masing-masing jumlah integer ≥1. Kemasan hanya berlaku untuk `static`, dengan satuan dan faktor konversi integer ≥1. Rekomendasi dikirim sebagai boolean. Gambar JPG/PNG/WebP maksimal sesuai validasi server 5 MB. | Validasi resep perlu dilakukan sebelum request. Kirim `packagings` hanya untuk `static`. Samakan batas nama/harga/unit UI dengan API. |
| Edit produk — `products/edit.blade.php`, `js/product/form.js`, `ProductController::update()` | Aturan informasi produk sama dengan tambah. Perubahan resep harus menyinkronkan daftar resep. Status lama dipertahankan bila pengguna tidak mengubahnya. | Untuk upload file, gunakan metode request yang didukung Laravel untuk multipart method spoofing dan tambahkan validasi/penyimpanan file pada endpoint update; sekarang endpoint hanya menerima `image_url`. Jangan menyatakan gambar tersimpan sebelum kontrak itu diperbaiki. |
| Tambah/edit satuan produk — `components/product/packaging-manager.blade.php`, `js/product/packaging.js` | Hanya produk `static`; satuan wajib dan unik per produk; faktor konversi integer ≥1; hapus memerlukan konfirmasi dan API menolak kemasan yang sudah dipakai transaksi. Produk baru menyimpan draft bersama produk, produk tersimpan memakai endpoint packaging. | Form mini saat ini bukan elemen `<form>` dan simpan hanya terhubung ke klik; input menyebut max 20 tetapi API menerima max 50. Saat jenis stok berubah, draft kemasan harus dibuang atau tidak ikut payload. |
| Tambah bahan baku — `components/raw-material/material-modal.blade.php`, `js/raw-material/material.js` | Nama wajib; satuan dasar hanya pilihan yang disediakan; minimum stock ≥0; harus ada minimal satu kemasan; nama satuan beli wajib dan unik (case-insensitive di UI), maksimal 20; konversi integer ≥1. | API saat ini menerima `pcs` tambahan dan tidak menetapkan minimum 0 pada server. Simpan bahan + seluruh kemasan secara atomik atau sediakan pemulihan yang jelas setelah kegagalan parsial. |
| Edit bahan baku — `components/raw-material/packaging-modal.blade.php`, `js/raw-material/packaging.js` | Nama bahan wajib; satuan dasar ditampilkan read-only; update mengirim nama dan unit yang ada. Tutup modal mereset state. | Form edit tidak memberi kontrol untuk mengubah minimum stock. Pastikan ini memang keputusan produk; kalau tidak, field dan validasi update belum ada. |
| Tambah/edit kemasan bahan baku — `components/raw-material/packaging-modal.blade.php`, `js/raw-material/packaging.js` | Satuan beli wajib dan unik per bahan, maksimal 20; konversi integer ≥1; edit memperbarui kemasan yang dipilih; kemasan yang sudah dipakai transaksi tidak dapat dihapus. | Set unit dasar pada elemen tampilan yang benar. API mengembalikan 422 untuk validasi dan harus menampilkan pesan tersebut. |
| Pembelian supplier — `suppliers/index.blade.php`, `components/supplier/*`, `js/supplier/*` | Nomor invoice wajib dan unik; supplier wajib; tanggal wajib dan tidak boleh di masa depan; minimal satu baris. Setiap baris wajib memilih tepat satu barang (bahan baku atau produk static), kemasan aktif milik barang itu, jumlah integer ≥1, dan harga integer ≥0. Minta konfirmasi sebelum menyimpan; sukses memperbarui stok dan reload. | Tombol hapus baris belum terhubung ke listener. Samakan batas invoice 20/supplier 50 dengan validasi backend, yang saat ini belum menerapkan batas tersebut. Backend perlu tetap memvalidasi tanggal masa depan dan hubungan ID barang-kemasan. |

## Urutan penanganan yang disarankan

1. Hubungkan tombol hapus baris pembelian dengan aksi delegasi yang sudah ada.
2. Sepakati kontrak upload gambar edit produk: backend menerima file, lalu frontend memakai multipart POST dengan method spoofing Laravel; atau hilangkan input gambar dari mode edit sampai didukung.
3. Samakan batas dan validasi frontend/backend untuk kategori, produk, satuan, tanggal pembelian, dan stok minimum.
4. Validasi resep serta kombinasi jenis stok/kemasan sebelum mengirim payload.
5. Jadikan pembuatan bahan baku dan kemasan satu transaksi backend atau tangani kegagalan parsial agar pengguna tidak menggandakan data.
6. Unit dasar dan error kategori sudah diperbaiki. Tautan bantuan/lupa sandi masih belum memiliki tujuan fungsional.
7. Setelah kontrak stabil, rapikan tanggung jawab modul JS/state dan hapus logging payload.

## Batas pemeriksaan

Audit ini membaca file dan mencocokkan alur JS/Blade dengan route/controller terkait. Tidak menjalankan aplikasi, tidak mengirim request ke database, dan tidak mengubah kode. Laravel Boost tidak terpasang; panduan `AGENTS.md` meminta instalasi yang akan mengubah dependency/konfigurasi, sehingga instalasi tersebut dilewati demi batasan tidak mengubah kode yang diminta pengguna.
