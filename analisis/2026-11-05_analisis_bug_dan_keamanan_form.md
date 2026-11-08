# LAPORAN AUDIT & ANALISIS KODE SISTEM WARKOP CAK KEBO
**Tanggal Analisis:** 5 November 2026  
**Cakupan Folder:** `app/` (Backend, Controllers, Models, Middleware) & `resources/` (Views Blade, Components, JavaScript, CSS)  
**Tujuan Dokumen:** Identifikasi bug sisi developer, potensi bug tersembunyi (latent bugs), kerentanan keamanan pada logic form, inkonsistensi data, serta panduan langkah implementasi filter karakter unik/simbol.

---

## DAFTAR ISI
1. [Ringkasan Eksekutif](#1-ringkasan-eksekutif)
2. [Bug Nyata Sisi Developer (Active Developer Bugs)](#2-bug-nyata-sisi-developer-active-developer-bugs)
3. [Potensi Bug Tersembunyi & Bom Waktu (Latent Bugs)](#3-potensi-bug-tersembunyi--bom-waktu-latent-bugs)
4. [Kesalahan pada Logic Keamanan Form (Security Logic Flaws)](#4-kesalahan-pada-logic-keamanan-form-security-logic-flaws)
5. [Kesalahan Logic pada Setiap Form & Inkonsistensi Data](#5-kesalahan-logic-pada-setiap-form--inkonsistensi-data)
6. [Rekomendasi & Solusi Perbaikan Fondasi Form](#6-rekomendasi--solusi-perbaikan-fondasi-form)
7. [Panduan Langkah Implementasi Filter Karakter Unik / Simbol](#7-panduan-langkah-implementasi-filter-karakter-unik--simbol)

---

## 1. RINGKASAN EKSEKUTIF

Berdasarkan audit mendalam terhadap seluruh alur data dari antarmuka Blade (`resources/views`), interaktivitas antarmuka JavaScript (`resources/js`), hingga pemrosesan controller, middleware, dan model (`app/`), ditemukan bahwa **form merupakan titik paling rentan dan krusial pada sistem ini**.

Meskipun sistem telah memiliki pemisahan modular yang cukup rapi (pemisahan state, utils, controller API vs Web), terdapat sejumlah **bug kritis yang langsung mematikan fungsi utama aplikasi saat digunakan**:
1. **Form Edit Produk tidak dapat menyimpan data (Gagal Total)** karena pengiriman `FormData` via HTTP `PUT` yang tidak didukung secara native oleh parser PHP.
2. **Tombol Hapus Baris Belanjaan pada Form Pembelian Supplier tidak berfungsi** karena atribut `data-action` hilang pada template Blade.
3. **Tombol Edit dan Hapus Satuan Kemasan Produk tidak merespons sama sekali** akibat penempatan event listener pada container yang keliru.
4. **Data produk dapat tersimpan kotor/korup saat validasi gagal** karena `return response()->json(...)` di dalam closure `DB::transaction()` tidak membatalkan transaksi database.
5. **Relasi antar model `Employee` dan `User` menghasilkan data tertukar** karena kesalahan pendefinisian foreign key.
6. **Data pembelian supplier bertipe `pending` terkunci selamanya** karena ketiadaan rute/controller untuk memperbarui status pembelian.
7. **Batas panjang karakter di database jauh lebih sempit daripada validasi controller**, yang berpotensi memicu crash `500 QueryException` di server.

---

## 2. BUG NYATA SISI DEVELOPER (ACTIVE DEVELOPER BUGS)

Berikut adalah bug yang sudah ada di dalam kode saat ini dan secara aktif menyebabkan kegagalan fungsional:

### BUG-DEV-01: Form Edit Produk Gagal Menyimpan Data (HTTP PUT Multipart Limitation di PHP)
- **Lokasi Berkas:**
  - `resources/js/product/form.js` (Fungsi `submitProduct`, baris 190–210)
  - `app/Http/Controllers/Api/ProductController.php` (Metode `update`)
- **Akar Masalah:**
  Pada mode edit, JavaScript menyusun payload menggunakan `new FormData()` (multipart/form-data) dan mengirimkannya menggunakan `method: "PUT"`.
  PHP secara native **hanya mem-parse body `multipart/form-data` pada request bertipe `POST`**. Pada request `PUT` multipart, global variable `$_POST` dan `$_FILES` akan **kosong sama sekali**.
  Ketika request masuk ke Laravel:
  ```php
  $validated = $request->validate([
      "category_id" => "required|integer|exists:categories,id",
      "name" => "required|string|max:100",
      ...
  ]);
  ```
  Laravel tidak menemukan satupun field di dalam request, sehingga request **selalu ditolak dengan status 422 Unprocessable Entity (Validation Error)**. Form edit produk tidak dapat diperbarui sama sekali.
- **Dampak:** Pengguna tidak dapat memperbarui produk yang sudah dibuat.
- **Saran Perbaikan:**
  Pada `resources/js/product/form.js`, jika payload berupa `FormData` untuk edit, kirimkan dengan metode `POST` dan tambahkan `formData.append('_method', 'PUT')`. Di `apiFetch`, biarkan request berjalan sebagai POST dengan header override Laravel.

---

### BUG-DEV-02: Event Listener Tombol Edit & Hapus Kemasan Produk Menempel di Container yang Salah
- **Lokasi Berkas:**
  - `resources/js/product/packaging.js` (Fungsi `initPackagingEvents`, baris 330–364)
  - `resources/views/components/product/packaging-manager.blade.php`
- **Akar Masalah:**
  Pada `initPackagingEvents()`, developer mendaftarkan event click untuk aksi `edit-packaging` dan `delete-packaging` ke element `$("packaging-modal")`:
  ```javascript
  $("packaging-modal")?.addEventListener("click", (event) => {
      const actionButton = event.target.closest("[data-action]");
      ...
      if (action === "edit-packaging") { ... }
      if (action === "delete-packaging") { ... }
  });
  ```
  Padahal, tombol dengan atribut `data-action="edit-packaging"` dan `data-action="delete-packaging"` dirender di dalam `$("packaging-list")` (yang berada di halaman form utama, di luar modal dialog `packaging-modal`).
- **Dampak:** Klik pada tombol Edit (ikon pensil) dan Hapus (ikon tempat sampah) pada tabel satuan pembelian produk **sama sekali tidak merespons** (tombol mati).
- **Saran Perbaikan:**
  Pindahkan event delegation click untuk aksi tersebut ke `$("packaging-list")` atau satukan di container section `$("packaging-section")`.

---

### BUG-DEV-03: Tombol Hapus Baris Belanja Supplier Tidak Berfungsi (Missing Atribut Data)
- **Lokasi Berkas:**
  - `resources/views/components/supplier/item-row.blade.php` (baris 66–73)
  - `resources/js/supplier/rows.js` (Fungsi `initRowEvents`, baris 140–148)
- **Akar Masalah:**
  Di `rows.js`, event delegation menangkap klik dengan selektor:
  ```javascript
  const button = event.target.closest('[data-action="remove-row"]');
  if (!button) return;
  await removeRow(button);
  ```
  Namun pada komponen Blade `item-row.blade.php`, tombol hapus ditulis tanpa atribut `data-action`:
  ```html
  <td class="py-2 text-center">
      <button type="button" class="text-red-400 hover:text-red-600" title="Hapus baris">
          <i class="fas fa-minus-square"></i>
      </button>
  </td>
  ```
- **Dampak:** Pengguna tidak dapat menghapus baris belanjaan pada tabel pembelian supplier. Tombol minus merah sama sekali tidak berfungsi.
- **Saran Perbaikan:**
  Tambahkan atribut `data-action="remove-row"` pada tag `<button>` di `item-row.blade.php`.

---

### BUG-DEV-04: Return Response di Dalam `DB::transaction()` Meng-commit Transaksi Saat Validasi Gagal
- **Lokasi Berkas:**
  - `app/Http/Controllers/Api/ProductController.php` (Method `store`, baris 122–134)
- **Akar Masalah:**
  Di dalam closure `DB::transaction(...)`:
  ```php
  if ($validated["stock_type"] !== "static" && !empty($validated["packagings"])) {
      return response()->json([
          "success" => false,
          "message" => "Packaging pembelian hanya dapat digunakan untuk produk static.",
      ], 422);
  }
  ```
  Di Laravel, me-return object `JsonResponse` dari dalam callback `DB::transaction` **bukanlah sebuah exception**. Transaksi database dianggap sukses dan di-commit (produk utama tetap tersimpan di database!). Selanjutnya pada baris 181, controller mengembalikan:
  ```php
  return response()->json([
      "success" => true,
      "message" => "Product created successfully.",
      "data" => $responseData, // Berisi JsonResponse 422 yang di-serialize!
  ], 201);
  ```
- **Dampak:** Terjadi inkonsistensi parah: data tersimpan ke database, tetapi response berisi pesan error yang bersarang, dan HTTP status code menjadi 201 Created padahal validasi logika bisnis gagal.
- **Saran Perbaikan:**
  Ganti return response tersebut dengan melempar exception:
  `throw \Illuminate\Validation\ValidationException::withMessages(['packagings' => 'Packaging pembelian hanya dapat digunakan untuk produk static.']);`

---

### BUG-DEV-05: Relasi Foreign Key Salah pada Model `Employee`
- **Lokasi Berkas:**
  - `app/Models/Employee.php` (Method `user()`, baris 26–29)
- **Akar Masalah:**
  Definisi relasi:
  ```php
  public function user()
  {
      return $this->belongsTo(User::class, "id");
  }
  ```
  Pada relasi `belongsTo(RelatedModel, foreignKey)`, argumen kedua adalah foreign key pada tabel lokal model ini (`employees`). Mengisinya dengan `"id"` membuat query yang dijalankan adalah `WHERE users.id = employees.id` (bukan `WHERE users.id = employees.user_id`).
- **Dampak:** Jika ID auto-increment tabel `users` dan `employees` tidak sama (misalnya ada user yang dihapus atau dibuat tanpa employee), pemanggilan `$employee->user` akan mengembalikan data akun user orang lain atau `null`!
- **Saran Perbaikan:**
  Ubah menjadi `return $this->belongsTo(User::class, "user_id");` atau biarkan default `return $this->belongsTo(User::class);`.

---

### BUG-DEV-06: Tag Meta CSRF & Login URL Hilang dari Layout Utama
- **Lokasi Berkas:**
  - `resources/views/components/layout.blade.php`
  - `resources/js/utils/api.js` (baris 1 & 12)
- **Akar Masalah:**
  `api.js` bergantung pada keberadaan dua tag meta:
  ```javascript
  const loginUrl = document.querySelector('meta[name="login-url"]')?.content;
  function getCsrfToken() {
      return document.querySelector('meta[name="csrf-token"]')?.content;
  }
  ```
  Namun pada `<head>` di `layout.blade.php`, kedua meta tag tersebut **tidak ada**. Tag `@csrf` hanya ada di form login dan form logout navbar.
- **Dampak:**
  - `getCsrfToken()` selalu bernilai `undefined`. (Aplikasi selamat sementara hanya karena cookie `XSRF-TOKEN` otomatis terbaca oleh browser pada domain yang sama).
  - Ketika terjadi error autentikasi (401 Unauthorized), `loginUrl` bernilai `undefined`, sehingga user **tidak diarahkan ke halaman login** dan aplikasi diam di tempat (stuck).
- **Saran Perbaikan:**
  Tambahkan di dalam `<head>` pada `layout.blade.php`:
  ```html
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="login-url" content="{{ route('login') }}">
  ```

---

### BUG-DEV-07: Parameter Tertukar pada Pemanggilan `showApiError` di `category.js`
- **Lokasi Berkas:**
  - `resources/js/product/category.js` (Fungsi `saveCategory`, baris 77–83)
  - `resources/js/utils/ui.js` (Fungsi `showApiError(data, title)`)
- **Akar Masalah:**
  Di `category.js`:
  ```javascript
  if (!response.ok) {
      await showApiError(
          response, // Parameter pertama dikirim object Response!
          data?.message || "Gagal menambahkan kategori.",
      );
      return;
  }
  ```
  Signature asli fungsi: `showApiError(data, title = "Terjadi Kesalahan")`. Di dalamnya terdapat `validationMessage(data)`. Karena dikirim `response`, maka `response.errors` dan `response.message` tidak ada.
- **Dampak:** Pesan error asli dari backend ("Nama kategori sudah ada.") malah dijadikan judul alert SweetAlert2, sedangkan body dialog selalu menampilkan teks default membingungkan: `"Data tidak dapat diproses."`.
- **Saran Perbaikan:**
  Ubah menjadi `await showApiError(data, "Gagal Menambahkan Kategori");`.

---

### BUG-DEV-08: Definisi Properti `$fillable` Ganda pada Model `Product`
- **Lokasi Berkas:**
  - `app/Models/Product.php` (baris 13–26)
- **Akar Masalah:**
  ```php
  protected $fillable = [
      "category_id",
      "name",
      "description",
      "base_price",
      "stock",
      "stock_type",
      "image_url",
      "image_path",
      "image_path",      // DUPLIKAT
      "is_recommended",
      "status",
      "is_recommended",  // DUPLIKAT
  ];
  ```
- **Dampak:** Terjadi pemborosan alokasi memori array Eloquent dan mengindikasikan ketiadaan automated testing/code linter.
- **Saran Perbaikan:** Hapus elemen duplikat pada array `$fillable`.

---

### BUG-DEV-09: Kolom `total_cost` Tidak Diikutsertakan pada Laporan Pembelian
- **Lokasi Berkas:**
  - `app/Http/Controllers/Api/PurchaseReportController.php` (Method `index`)
- **Akar Masalah:**
  Query API laporan:
  ```php
  $purchases = SupplierPurchase::query()
      ->select([
          "id",
          "invoice_number",
          "supplier_name",
          "purchase_date",
          "status",
      ])
  ...
  ```
  Kolom `total_cost` (total rupiah belanja) sama sekali tidak di-select.
- **Dampak:** Laporan transaksi pembelian tidak menyajikan data keuangan total pengeluaran belanja.
- **Saran Perbaikan:** Tambahkan `"total_cost"` ke dalam array select query.

---

## 3. POTENSI BUG TERSEMBUNYI & BOM WAKTU (LATENT BUGS)

Bug pada kategori ini mungkin belum meledak pada saat pengujian awal dengan data buatan pendek, namun **pasti akan meledak saat aplikasi digunakan dalam operasional nyata**.

### BUG-LAT-01: Ketidaksinkronan Panjang Karakter Antara Validasi Controller dan Skema Database (Database Check Violation)
Ini adalah masalah paling masif di seluruh sistem:
1. **Nama Bahan Baku (`raw_materials.name`):**
   - Skema Migrasi: `VARCHAR(50)` dengan `CHECK (char_length(btrim(name)) BETWEEN 1 AND 50)`.
   - Controller `RawMaterialController`: `"name" => "required|string|max:100"`.
   - *Bom Waktu:* Jika user memasukkan nama bahan baku sepanjang 55 karakter, validasi controller lolos, tetapi database PostgreSQL akan membatalkan query dengan fatal error `String data right truncation` atau `chk_raw_materials_name violation`.
2. **Nama Produk (`products.name`):**
   - Skema Migrasi: `VARCHAR(50)`.
   - Controller `ProductController`: `"name" => "required|string|max:100"`.
   - *Bom Waktu:* Input antara 51–100 karakter akan lolos validasi Laravel tapi gagal disimpan ke database.
3. **Satuan Kemasan Produk (`product_packagings.purchase_unit`):**
   - Skema Migrasi: `VARCHAR(20)`.
   - Controller `ProductController`: `"packagings.*.purchase_unit" => "required|string|max:50"`.
   - *Bom Waktu:* Input satuan > 20 karakter memicu fatal error 500.
4. **Nama Supplier & Nomor Invoice (`supplier_purchases`):**
   - Skema Migrasi: `invoice_number VARCHAR(20)`, `supplier_name VARCHAR(50)`.
   - Controller `SupplierPurchaseController`: Tidak ada batasan `max` (`"required|string"`).
   - *Bom Waktu:* Input invoice atau supplier panjang akan langsung melempar SQL error.
5. **Kuantitas Pembelian Supplier (`supplier_purchase_items.quantity`):**
   - Skema Migrasi: `CHECK (quantity BETWEEN 1 AND 999)`.
   - Controller `SupplierPurchaseController`: `"items.*.quantity" => "required|integer|min:1"` (tanpa `max:999`).
   - *Bom Waktu:* Kafe sering membeli sedotan (1.000 pcs), paper cup (1.000 pcs), atau tusuk gigi dalam jumlah ribuan. Pembelian di atas 999 unit akan crash di level database.

---

### BUG-LAT-02: Produk Resep / Produk Static Tidak Akan Pernah Bisa Dihapus (`destroy` Conflict)
- **Lokasi Berkas:**
  - `app/Http/Controllers/Api/ProductController.php` (Method `destroy`, baris 334–366)
- **Akar Masalah:**
  ```php
  $hasRecipes = DB::table("product_recipes")->where("product_id", $product->id)->exists();
  $hasPackagings = DB::table("product_packagings")->where("product_id", $product->id)->exists();
  if ($hasOrderHistory || $hasPurchaseHistory || $hasRecipes || $hasPackagings || ...) {
      return response()->json([
          "success" => false,
          "message" => "Produk tidak dapat dihapus karena sudah digunakan dalam data lain...",
      ], 409);
  }
  ```
- **Bom Waktu:**
  Untuk produk bertipe `recipe`, sistem **mewajibkan** pembuatan resep saat produk dibuat. Untuk produk `static`, user menyertakan satuan kemasan (`packagings`).
  Jika ada produk baru dibuat salah ketik atau salah input 1 detik yang lalu dan belum pernah ada transaksi sama sekali, **produk tersebut TIDAK AKAN BISA DIHAPUS SELAMANYA** karena `$hasRecipes` atau `$hasPackagings` bernilai `true`! Resep dan kemasan produk adalah relasi komposisi milik produk, bukan data transaksi historis.

---

### BUG-LAT-03: Status Pembelian "Pending" Terkunci Tanpa Alur Penyelesaian
- **Lokasi Berkas:**
  - `app/Http/Controllers/Api/SupplierPurchaseController.php`
  - `routes/api.php`
- **Bom Waktu:**
  Sistem mengizinkan status pembelian bernilai `"pending"` atau `"received"`. Jika status `"pending"`, stok tidak ditambah. Namun di seluruh backend:
  - Tidak ada rute API `PUT /api/supplier-purchases/{id}` untuk update status menjadi `received`.
  - Tidak ada rute API untuk membatalkan pembelian.
  - Tidak ada tombol atau antarmuka untuk menerima barang yang pending.
  Jika suatu saat kasir/admin mencatat invoice yang statusnya barang belum sampai (pending), data tersebut menjadi data mati dan stoknya tidak akan pernah bisa dimasukkan ke sistem.

---

### BUG-LAT-04: Transaksi Parsial / Ghost Record Saat Menambah Bahan Baku
- **Lokasi Berkas:**
  - `resources/js/raw-material/material.js` (Fungsi `submitMaterial`, baris 200–235)
- **Bom Waktu:**
  Penyimpanan bahan baku dilakukan dalam multiple HTTP request dari browser:
  1. Request 1: `POST /api/raw-materials` (membuat bahan baku).
  2. Request 2..N: Loop `POST /api/raw-materials/{id}/packagings` (membuat kemasan satu per satu).
  Jika pada kemasan ke-2 terjadi kegagalan (misalnya koneksi putus atau satuan kemasan duplikat), request berhenti. Namun bahan baku utama dan kemasan ke-1 sudah terlanjur tersimpan di database tanpa ada mekanisme rollback. Ini berbeda dengan produk yang menyatukan data dalam 1 request ber-`DB::transaction()`.

---

### BUG-LAT-05: Mengubah Tipe Stok Meninggalkan Data Kemasan Hantu (Ghost Packagings)
- **Lokasi Berkas:**
  - `app/Http/Controllers/Api/ProductController.php` (Method `update`)
- **Bom Waktu:**
  Jika produk awalnya berjenis `static` (sudah memiliki packaging), lalu di-update menjadi `recipe`, controller hanya menghapus tabel `recipes`:
  `$product->recipes()->delete();`
  Tabel `product_packagings` **tidak dibersihkan/dihapus**.
  Akibatnya, di dropdown Pembelian Supplier, produk tersebut tetap muncul dan bisa dibeli sebagai barang eceran, padahal jenis stoknya sudah berubah menjadi resep.

---

### BUG-LAT-06: Kerusakan Input Angka Desimal Akibat Skrip Global di `app.js`
- **Lokasi Berkas:**
  - `resources/js/app.js` (baris 23–58)
- **Akar Masalah:**
  Skrip global mendengarkan event input:
  ```javascript
  const digits = input.value.replace(/\D/g, "");
  ...
  input.value = digits;
  ```
- **Bom Waktu:**
  Jika di kemudian hari sistem membutuhkan input desimal pada form (misalnya jumlah gram: `1.5`, takaran cairan: `0.25`, atau diskon: `10.5%`), skrip ini akan **menghapus tanda titik desimal secara paksa**, mengubah `1.5` menjadi `15`! Selain itu, skrip ini hanya menghitung panjang karakter digit string untuk mengecek `input.max`, bukan nilai angka sebenarnya (misal `max="50"`, string digit 2 karakter mengizinkan nilai `99`).

---

## 4. KESALAHAN PADA LOGIC KEAMANAN FORM (SECURITY LOGIC FLAWS)

### SEC-01: Ketiadaan Proteksi Brute-Force (Rate Limiting) pada Form Login
- **Lokasi:** `routes/web.php` & `routes/api.php`
- **Masalah:** Rute `POST /login` tidak memiliki middleware throttle (misal `throttle:login` atau `throttle:5,1`). Penyerang dapat melakukan automated credential stuffing atau brute-force password tanpa batas percobaan.

### SEC-02: Fitur "Ingat Sesi di Perangkat Ini" (Remember Me) Tidak Berfungsi
- **Lokasi:** `resources/views/auth/login.blade.php` & `app/Http/Controllers/Web/AuthController.php`
- **Masalah:** Checkbox `name="remember"` ada di Blade, tetapi di controller:
  ```php
  if (Auth::attempt($credentials)) // Parameter kedua $request->boolean('remember') tidak dioper!
  ```
  Pengguna yang mencentang ingat sesi tetap akan ter-logout saat session lifetime berakhir. Ini adalah bug fungsional sekaligus inkonsistensi UX keamanan.

### SEC-03: Karyawan Nonaktif Masih Bisa Login ke Web Panel Admin
- **Lokasi:** `app/Http/Controllers/Web/AuthController.php` vs `app/Http/Controllers/Api/AuthController.php`
- **Masalah:**
  Pada `Api/AuthController`, terdapat proteksi akun:
  ```php
  if ($user->employee && !$user->employee->is_active) {
      return response()->json(["message" => "Account is not activated."], 403);
  }
  ```
  Namun pada `Web/AuthController`, **pengecekan `is_active` ini TIDAK ADA**. Karyawan yang telah dinonaktifkan (di-PHK/resign) masih bisa login ke Web Panel asalkan rolenya bukan employee biasa (atau jika rolenya admin/owner).

### SEC-04: Potensi Stored XSS pada Template Literal JavaScript
- **Lokasi:** Hampir seluruh berkas JS di `resources/js/product/`, `raw-material/`, `supplier/`
- **Masalah:**
  Meskipun developer telah menyediakan fungsi `escapeHtml()`, terdapat beberapa area rendering string di mana data dari database langsung dimasukkan ke `innerHTML` tanpa di-escape, atau atribut data HTML dirender langsung:
  Contoh di `resources/js/product/recipe.js`:
  ```javascript
  value="${row.amount_needed ?? ""}" // Tidak di-escape
  ```
  Jika suatu field tercemar script berbahaya (misal nama kategori atau invoice dari API pihak ketiga), script tersebut akan dieksekusi di browser admin.

### SEC-05: Kurangnya Validasi Format String (Injection & Control Characters)
- **Masalah:**
  Nama produk, kategori, invoice, dan kemasan hanya divalidasi dengan `required|string|max:x`. Tidak ada pencegahan terhadap karakter berbahaya seperti tag HTML (`<script>`), whitespace liar, null byte (`\0`), maupun karakter format kontrol lainnya.

---

## 5. KESALAHAN LOGIC PADA SETIAP FORM & INKONSISTENSI DATA

Berikut adalah telaah rinci pada setiap form yang ada di aplikasi:

### A. Form Login (`resources/views/auth/login.blade.php`)
1. **Status Online Hardcoded:** Menampilkan badge `Online - Cloud Sync Active` dan `POS • v2.4` secara statis, memberikan rasa aman palsu kepada kasir padahal tidak ada koneksi sinkronisasi aktif.
2. **Shift 1 Hardcoded:** Terdapat teks "Shift 1" yang tidak terhubung ke jam kerja dinamis atau tabel `cashier_shifts`.

### B. Form Tambah & Edit Produk (`resources/views/components/product/product-form.blade.php`)
1. **Mode Edit Rusak:** Mengirim `FormData` dengan method `PUT` yang tidak terbaca oleh PHP (lihat BUG-DEV-01).
2. **Tidak Bisa Ganti Foto Produk Saat Edit:** Controller `update` hanya menerima `"image_url" => "nullable|string|max:500"` dan tidak meng-inject `R2ImageService` untuk menangani file upload baru.
3. **Ghost Packagings Saat Switch Jenis Stok:** Jika pengguna di halaman create memilih jenis stok "Static", memasukkan 2 kemasan, lalu berubah pikiran mengklik "Recipe", array kemasan di JavaScript (`state.productPackagings`) **tidak dikosongkan**. Akibatnya saat submit, payload mengirim resep sekaligus kemasan, yang memicu bug transaksi di backend.
4. **Inkonsistensi Batas Panjang:**
   - Input HTML: `maxlength="50"`.
   - Controller: `max:100`.
   - Database: `VARCHAR(50)`.
   - Deskripsi HTML: `maxlength="500"`, Controller: `unlimited`, Database: `max 1000`.

### C. Form Resep Produk (`recipe-builder.blade.php`, `recipe.js`)
1. **Bahan Baku Duplikat:** Tidak ada filter di antarmuka untuk mencegah pengguna memilih bahan baku yang sama di dua baris berbeda.
2. **Re-render Destruktif:** Mengubah dropdown bahan baku pada satu baris memicu `renderRecipeRows()` yang me-render ulang seluruh DOM tabel resep, mereset fokus kursor dan input yang sedang diketik.
3. **Keterbatasan Satuan Desimal:** Validasi `amount_needed` mewajibkan integer `min:1`, sehingga tidak mendukung resep berbobot desimal (misal 1.5 gram bubuk kayu manis atau 0.5 buah lemon).

### D. Form Bahan Baku & Kemasan (`material-modal.blade.php`, `packaging-modal.blade.php`)
1. **Satuan `pcs` Hilang dari Pilihan:** Skema DB dan controller mengizinkan satuan dasar `gram, ml, pcs`. Namun pada elemen `<select id="rm_unit">` di modal tambah bahan baku, hanya ada opsi `gram` dan `ml`. Pengguna tidak bisa mendaftarkan bahan baku berbasis satuan buah/butir/lembar.
2. **Label Satuan Minimum Stock Salah:** Saat modal tambah dibuka pertama kali, dropdown default bernilai `gram`, tetapi label di sebelah kanan input batas minimum bertuliskan `ml`.
3. **Field `minimum_stock` Hilang Saat Edit:** Pada modal edit bahan baku, tidak ada field untuk mengubah batas stok minimum. Controller `update` juga tidak menerima field `minimum_stock`. Batas minimum stok yang sudah diisi di awal tidak akan pernah bisa diedit lagi.
4. **Pemisahan Transaksi Tidak Aman:** Bahan baku dan kemasannya disimpan dalam request HTTP terpisah tanpa transaksi atomic (lihat BUG-LAT-04).

### E. Form Pembelian Supplier (`suppliers/index.blade.php`, `purchase.js`, `item-row.blade.php`)
1. **Tombol Hapus Baris Mati:** Kehilangan atribut `data-action="remove-row"` (lihat BUG-DEV-03).
2. **Bisa Konfirmasi Tanpa Baris Belanja:** Jika user menghapus seluruh item, konfirmasi SweetAlert tetap muncul sebelum ditolak backend.
3. **Batas Kuantitas 999:** Pembelian barang dalam jumlah 1000 unit atau lebih akan crash di database.
4. **Validasi Invoice Number Terlalu Ketat:** Invoice divalidasi unik secara global di seluruh sistem (`unique:supplier_purchases,invoice_number`). Jika Toko A dan Toko B sama-sama memakai format nota `INV-001`, sistem menolak nota dari toko kedua. Seharusnya invoice unik per nama supplier.
5. **Kuantitas & Harga Desimal Crash di Backend:** JavaScript menggunakan `parseFloat()` pada qty dan harga, namun controller backend memvalidasi `"items.*.quantity" => "required|integer"` dan `"items.*.unit_price" => "required|integer"`. Memasukkan angka desimal (misal harga Rp 12.500,50) akan ditolak server.

---

## 6. REKOMENDASI & SOLUSI PERBAIKAN FONDASI FORM

Untuk menstabilkan pondasi form aplikasi, berikut langkah-langkah perbaikan yang direkomendasikan untuk diterapkan oleh tim developer:

### 1. Perbaikan Alur HTTP PUT & Upload pada Produk
- Pada `resources/js/product/form.js`: Jika `isEdit`, kirim HTTP request menggunakan metode `POST` dengan `formData.append('_method', 'PUT')`.
- Pada `ProductController.php@update`: Inject `R2ImageService` dan tambahkan validasi `"image" => ["nullable", "image", "max:5120"]`. Jika ada file baru di-upload, hapus gambar lama di R2 dan simpan URL baru.

### 2. Standarisasi Batas Karakter (DB vs Controller vs Frontend)
Samakan batas panjang karakter di ketiga lapisan agar konsisten:
- `products.name`: Tetapkan 100 karakter di migrasi (`$table->string('name', 100)`), di Controller (`max:100`), dan di HTML input (`maxlength="100"`).
- `raw_materials.name`: Tetapkan 50 karakter di migrasi, di Controller (`max:50`), dan di HTML input (`maxlength="50"`).
- `purchase_unit`: Tetapkan 20 karakter di migrasi, Controller (`max:20`), dan HTML (`maxlength="20"`).
- `supplier_name`: Berikan validasi `max:50` di controller sesuai skema DB.
- `invoice_number`: Berikan validasi `max:20` di controller sesuai skema DB.

### 3. Perbaikan Event Delegation & UI Form
- Di `resources/views/components/supplier/item-row.blade.php`: Tambahkan `data-action="remove-row"` pada tombol hapus.
- Di `resources/js/product/packaging.js`: Pindahkan listener klik untuk edit/delete packaging dari `packaging-modal` ke `packaging-list`.
- Di `resources/views/components/raw-material/material-modal.blade.php`: Tambahkan opsi `<option value="pcs">pcs</option>`. Sinkronkan label unit minimum stock secara otomatis saat modal dibuka.
- Di `resources/views/components/raw-material/packaging-modal.blade.php` dan `RawMaterialController@update`: Sertakan field `minimum_stock` agar dapat diperbarui sewaktu-waktu.

### 4. Perbaikan Transaksi Database & Integritas Relasi
- Di `ProductController@store`: Ganti `return response()->json(..., 422)` di dalam `DB::transaction` dengan melempar `ValidationException` agar transaksi di-rollback saat validasi gagal.
- Di `Employee.php`: Perbaiki relasi `belongsTo(User::class, 'user_id')`.
- Di `ProductController@destroy`: Izinkan penghapusan produk jika hanya memiliki resep atau packaging (hapus resep & packaging terkait dalam transaksi delete), dan hanya blokir penghapusan jika produk sudah pernah masuk ke `order_items` atau `supplier_purchase_items`.

---

## 7. PANDUAN LANGKAH IMPLEMENTASI FILTER KARAKTER UNIK / SIMBOL

Sesuai permintaan Anda untuk memfilter karakter unik seperti `!@#$%^&*()` dan karakter aneh lainnya pada form, **jangan hanya mengandalkan satu sisi saja**. Anda harus menerapkan **Defense in Depth (Pertahanan Berlapis)**.

Berikut adalah panduan langkah demi langkah yang harus dilakukan:

### Langkah 1: Tentukan Aturan Whitelist (Karakter yang Diizinkan) untuk Setiap Jenis Form
Hindari sistem blacklist (mencoba memblokir satu per satu simbol), karena penyerang selalu bisa menemukan karakter baru (seperti emoji, simbol matematika, null byte, unicode tersembunyi). Gunakan sistem **Whitelist** (hanya izinkan karakter yang sah):

| Jenis Form / Field | Karakter yang Diizinkan (Whitelist) | Simbol yang Dilarang |
|---|---|---|
| **Nomor Invoice** (`invoice_number`) | Huruf, Angka, Strip `-`, Garis Miring `/`, Underscore `_` | `!@#$%^&*()+=~`'":;?><` dll |
| **Nama Produk / Kategori / Bahan Baku** | Huruf, Angka, Spasi, Tanda Kurung `()`, Titik `.`, Strip `-` | `!@#$%^&*+=~`'":;?><` dll |
| **Satuan Beli / Kemasan** (`purchase_unit`) | Huruf dan Angka saja (alfanumerik) | Semua simbol dan spasi |
| **Nama Supplier** (`supplier_name`) | Huruf, Angka, Spasi, Titik `.`, Strip `-`, Koma `,`, Ampersand `&` | `!@#$%^*()+=~`'":;?><` dll |
| **Harga / Kuantitas / Konversi** | Hanya Angka (0–9) | Semua huruf dan simbol |

---

### Langkah 2: Implementasi Sanitasi & Filter Real-Time di Sisi Frontend (JavaScript)
Buat fungsi utilitas filter regex di `resources/js/utils/dom.js` (atau buat file utilitas baru `validator.js`):

```javascript
// Contoh pola Regex Whitelist:
export const PATTERNS = {
    // Hanya huruf, angka, spasi, strip, kurung, dan titik
    NAME: /^[a-zA-Z0-9\s\-\(\)\.]+$/,
    
    // Hanya huruf, angka, strip, slash, underscore (untuk invoice)
    INVOICE: /^[a-zA-Z0-9\-_/]+$/,
    
    // Hanya huruf dan angka (untuk satuan kemasan)
    UNIT: /^[a-zA-Z0-9]+$/,
    
    // Nama supplier (termasuk &, koma, titik)
    SUPPLIER: /^[a-zA-Z0-9\s\-\.,&]+$/
};

/**
 * Filter karakter input secara real-time saat pengguna mengetik
 */
export function attachInputFilter(inputElement, regexPattern) {
    if (!inputElement) return;

    inputElement.addEventListener("input", (e) => {
        const oldValue = inputElement.dataset.lastValidValue || "";
        const newValue = inputElement.value;

        // Jika kosong atau cocok dengan pola yang diizinkan, simpan nilai valid
        if (newValue === "" || regexPattern.test(newValue)) {
            inputElement.dataset.lastValidValue = newValue;
        } else {
            // Jika ada karakter ilegal, kembalikan ke nilai valid sebelumnya
            inputElement.value = oldValue;
            
            // Tampilkan notifikasi toast kecil
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: 'Karakter khusus tidak diizinkan',
                showConfirmButton: false,
                timer: 1500
            });
        }
    });
}
```

Terapkan fungsi ini pada inisialisasi form masing-masing:
- Pada `resources/js/product/form.js`: Filter field `#product-name` dengan `PATTERNS.NAME`.
- Pada `resources/js/product/category.js`: Filter field `#category-name` dengan `PATTERNS.NAME`.
- Pada `resources/js/product/packaging.js`: Filter field `#packaging-unit` dengan `PATTERNS.UNIT`.
- Pada `resources/js/supplier/purchase.js`: Filter `#invoice_number` dengan `PATTERNS.INVOICE`, dan `#supplier_name` dengan `PATTERNS.SUPPLIER`.

---

### Langkah 3: Implementasi Validasi Pre-Submit di Frontend
Sebelum data dikirim melalui `apiFetch`, lakukan pengecekan akhir:
```javascript
if (!PATTERNS.NAME.test(name)) {
    await Swal.fire({
        icon: 'error',
        title: 'Format Tidak Valid',
        text: 'Nama tidak boleh mengandung simbol seperti !@#$%^&*()+=',
    });
    return;
}
```

---

### Langkah 4: Implementasi Validasi Wajib di Sisi Backend (Laravel Controller)
Frontend dapat dibypass dengan mudah (menggunakan Postman, cURL, atau inspect element). Oleh karena itu, **validasi di controller Laravel adalah benteng pertahanan mutlak**.

Gunakan aturan `regex` pada validator Laravel di masing-masing controller:

1. **Pada `app/Http/Controllers/Api/CategoryController.php`:**
   ```php
   $validated = $request->validate([
       "name" => [
           "required",
           "string",
           "max:50",
           "regex:/^[a-zA-Z0-9\s\-\(\)\.]+$/",
           "unique:categories,name",
       ],
   ], [
       "name.regex" => "Nama kategori hanya boleh memuat huruf, angka, spasi, titik, strip, dan tanda kurung.",
   ]);
   ```

2. **Pada `app/Http/Controllers/Api/ProductController.php`:**
   ```php
   $validated = $request->validate([
       "name" => [
           "required",
           "string",
           "max:100",
           "regex:/^[a-zA-Z0-9\s\-\(\)\.]+$/",
       ],
       "packagings.*.purchase_unit" => [
           "required",
           "string",
           "max:20",
           "regex:/^[a-zA-Z0-9]+$/", // Satuan beli tidak boleh mengandung simbol
       ],
       ...
   ]);
   ```

3. **Pada `app/Http/Controllers/Api/RawMaterialController.php`:**
   ```php
   $validated = $request->validate([
       "name" => [
           "required",
           "string",
           "max:50",
           "regex:/^[a-zA-Z0-9\s\-\(\)\.]+$/",
       ],
       ...
   ]);
   ```

4. **Pada `app/Http/Controllers/Api/SupplierPurchaseController.php`:**
   ```php
   $validated = $request->validate([
       "invoice_number" => [
           "required",
           "string",
           "max:20",
           "regex:/^[a-zA-Z0-9\-_/]+$/", // Hanya alfanumerik, -, _, /
           "unique:supplier_purchases,invoice_number",
       ],
       "supplier_name" => [
           "required",
           "string",
           "max:50",
           "regex:/^[a-zA-Z0-9\s\-\.,&]+$/",
       ],
       ...
   ]);
   ```

---

### Langkah 5: Sanitasi Pembersihan Whitespace (Sanitization Sanitizer)
Karakter spasi ganda dan spasi di awal/akhir string sering menyebabkan kebingungan pencarian dan duplikasi data yang tidak terdeteksi.
Sebelum menyimpan ke database:
1. Lakukan `trim()` pada setiap string.
2. Gunakan `preg_replace('/\s+/', ' ', $value)` untuk mereduksi spasi berlebih di tengah kata menjadi satu spasi tunggal.
3. Bersihkan tag HTML menggunakan `strip_tags($value)`.

---

## KESIMPULAN

Sistem Warkop Cak Kebo memiliki pondasi UI dan alur bisnis yang sangat baik, namun memiliki beberapa cacat implementasi kode yang menghambat fungsi form (terutama form edit produk, event packaging, dan tombol baris belanja supplier).

Dengan menyelaraskan batasan panjang data antara migrasi dan validasi controller, memperbaiki event delegation di frontend, mengoreksi cara pengiriman payload PUT, serta menambahkan filter regex whitelist pada form-form utama, sistem akan menjadi jauh lebih tangguh, aman, dan konsisten dalam menghadapi operasional harian.

