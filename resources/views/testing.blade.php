<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Web Testing API Warkop CKB</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        .card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        input, select, button { padding: 10px; margin: 5px 0; width: 100%; box-sizing: border-box; }
        button { background: #0284c7; color: white; border: none; cursor: pointer; }
        pre { background: #1e293b; color: #10b981; padding: 10px; overflow-x: auto; border-radius: 4px; }
    </style>
</head>
<body>

<h2>Testing API Warkop CKB</h2>

<!-- INFO TOKEN -->
<div class="card">
    <h3>1. Status Autentikasi</h3>
    <p>Token Tersimpan: <span id="token-status" style="color: red;">Belum ada token</span></p>
    <button onclick="clearToken()" style="background: #dc2626;">Hapus Token (Logout Lokal)</button>
</div>

<!-- FORM INPUT PRODUK -->
<div class="card">
    <h3>2. Input Master Produk Baru</h3>
    <input type="text" id="cat_name" placeholder="Nama Kategori (Misal: Kopi, Snack)" required>
    <input type="text" id="prod_name" placeholder="Nama Produk (Misal: Kopi Latte)" required>
    <input type="number" id="base_price" placeholder="Harga Dasar (Misal: 25000)" required>

    <select id="stock_type">
        <option value="static">Static (Barang Kemasan / Eceran)</option>
        <option value="recipe">Recipe (Minuman/Makanan Racikan)</option>
        <option value="untracked">Untracked (Tidak Dilacak)</option>
    </select>

    <button onclick="submitProduct()">Simpan Produk ke Database</button>
    <h4>Respons Server:</h4>
    <pre id="response-box">Menunggu aksi...</pre>
</div>

<script>
    // Cek apakah ada token Sanctum dari hasil login sebelumnya di Postman
    // Untuk testing ini, kamu bisa paste token dari Postman langsung ke localStorage browser via inspect element,
    // ATAU kita bisa tambahkan form login sekalian di sini nanti.
    const token = localStorage.getItem('api_token');

    if(token) {
        document.getElementById('token-status').innerText = "Aktif (" + token.substring(0, 15) + "...)";
        document.getElementById('token-status').style.color = "green";
    } else {
        // Untuk percobaan cepat, paste manual tokenmu di sini dan hapus komentar:
        // localStorage.setItem('api_token', '1|token_panjang_kamu_di_sini');
        // location.reload();
    }

    async function submitProduct() {
        const apiToken = localStorage.getItem('api_token');
        if(!apiToken) {
            alert("Harap masukkan token Sanctum ke dalam localStorage terlebih dahulu!");
            return;
        }

        const payload = {
            category_name: document.getElementById('cat_name').value,
            name: document.getElementById('prod_name').value,
            base_price: document.getElementById('base_price').value,
            stock_type: document.getElementById('stock_type').value
        };

        document.getElementById('response-box').innerText = "Loading...";

        try {
            // Menembak endpoint API Laravel seperti yang akan dilakukan Vue/Flutter[cite: 7]
            const response = await fetch('/api/products', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${apiToken}` // Mengirim token Sanctum[cite: 7]
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();
            document.getElementById('response-box').innerText = JSON.stringify(data, null, 2);
        } catch (error) {
            document.getElementById('response-box').innerText = "Terjadi Kesalahan Jaringan: " + error;
        }
    }

    function clearToken() {
        localStorage.removeItem('api_token');
        location.reload();
    }
</script>
</body>
</html>
