import 'package:flutter/material.dart';

class PesananBerhasilPage extends StatelessWidget {
  const PesananBerhasilPage({Key? key}) : super(key: key);

  // Tema warna disesuaikan dengan Tailwind Config dari HTML
  static const Color primaryColor = Color(0xFF006194);
  static const Color primaryContainer = Color(0xFF007BB9);
  static const Color secondaryContainer = Color(0xFFBAE6FD);
  static const Color surfaceLow = Color(0xFFEFF4FF);
  static const Color onSurface = Color(0xFF0B1C30);
  static const Color onSurfaceVariant = Color(0xFF3F4850);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF8F9FF),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 1,
        automaticallyImplyLeading: false,
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: primaryColor,
                borderRadius: BorderRadius.circular(10),
              ),
              child: const Icon(Icons.local_cafe, color: Colors.white, size: 20),
            ),
            const SizedBox(width: 10),
            const Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'Cak Kebo',
                  style: TextStyle(
                    color: onSurface,
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                Text(
                  'POS TERMINAL',
                  style: TextStyle(
                    color: primaryColor,
                    fontSize: 10,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
            const Spacer(),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: secondaryContainer.withOpacity(0.5),
                borderRadius: BorderRadius.circular(20),
              ),
              child: const Row(
                children: [
                  Icon(Icons.table_restaurant, color: primaryColor, size: 16),
                  SizedBox(width: 6),
                  Text(
                    'Meja #04',
                    style: TextStyle(
                      color: primaryColor,
                      fontWeight: FontWeight.bold,
                      fontSize: 12,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(20.0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Breadcrumb & Status Cloud
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Row(
                    children: [
                      Icon(Icons.point_of_sale, color: primaryColor, size: 18),
                      SizedBox(width: 4),
                      Text(
                        'Kasir POS / Meja #04 / ',
                        style: TextStyle(color: onSurfaceVariant, fontSize: 13),
                      ),
                      Text(
                        'Konfirmasi Pembayaran',
                        style: TextStyle(
                          color: onSurface,
                          fontWeight: FontWeight.bold,
                          fontSize: 13,
                        ),
                      ),
                    ],
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 10,
                      vertical: 4,
                    ),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Row(
                      children: [
                        Icon(Icons.cloud_done, color: primaryColor, size: 14),
                        SizedBox(width: 4),
                        Text(
                          'Ref: TXN-99841',
                          style: TextStyle(
                            color: primaryColor,
                            fontWeight: FontWeight.bold,
                            fontSize: 11,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),

              // Layout Responsif: Tampilan Grid/Row untuk layar lebar, Column untuk layar sempit
              LayoutBuilder(
                builder: (context, constraints) {
                  if (constraints.maxWidth > 800) {
                    return Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Expanded(flex: 7, child: _buildLeftColumn(context)),
                        const SizedBox(width: 20),
                        Expanded(flex: 5, child: _buildRightColumn(context)),
                      ],
                    );
                  } else {
                    return Column(
                      children: [
                        _buildLeftColumn(context),
                        const SizedBox(height: 20),
                        _buildRightColumn(context),
                      ],
                    );
                  }
                },
              ),
            ],
          ),
        ),
      ),
    );
  }

  // Kolom Kiri: Status Berhasil & Tombol Aksi Cepat
  Widget _buildLeftColumn(BuildContext context) {
    return Column(
      children: [
        // Hero Status Card
        Container(
          width: double.infinity,
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.04),
                blurRadius: 15,
                offset: const Offset(0, 5),
              ),
            ],
          ),
          child: Column(
            children: [
              // Lingkaran Centang
              Container(
                width: 70,
                height: 70,
                decoration: BoxDecoration(
                  color: secondaryContainer.withOpacity(0.6),
                  shape: BoxShape.circle,
                ),
                child: Center(
                  child: Container(
                    width: 50,
                    height: 50,
                    decoration: const BoxDecoration(
                      color: primaryColor,
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(
                      Icons.check,
                      color: Colors.white,
                      size: 32,
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 12,
                  vertical: 4,
                ),
                decoration: BoxDecoration(
                  color: secondaryContainer.withOpacity(0.5),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Text(
                  'Pembayaran Lunas & Terverifikasi',
                  style: TextStyle(
                    color: primaryColor,
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
              const SizedBox(height: 12),
              const Text(
                'Pesanan Berhasil Dibuat!',
                style: TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.bold,
                  color: onSurface,
                ),
              ),
              const SizedBox(height: 4),
              const Text(
                'Nomor Pesanan #ORD-204 • Meja 04 (Dimas Arya) • QRIS Dinamis',
                textAlign: TextAlign.center,
                style: TextStyle(color: onSurfaceVariant, fontSize: 13),
              ),
              const SizedBox(height: 20),

              // Total Transaksi Pill
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: surfaceLow,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Column(
                  children: [
                    const Text(
                      'TOTAL TRANSAKSI DITERIMA',
                      style: TextStyle(
                        color: onSurfaceVariant,
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 4),
                    const Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      crossAxisAlignment: CrossAxisAlignment.baseline,
                      textBaseline: TextBaseline.alphabetic,
                      children: [
                        Text(
                          'Rp ',
                          style: TextStyle(
                            color: primaryColor,
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        Text(
                          '83.600',
                          style: TextStyle(
                            color: primaryColor,
                            fontSize: 36,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: const [
                        Icon(Icons.schedule, size: 14, color: primaryColor),
                        SizedBox(width: 4),
                        Text(
                          '24 Okt 2024, 10:43 WIB',
                          style: TextStyle(
                            fontSize: 11,
                            color: onSurfaceVariant,
                          ),
                        ),
                        SizedBox(width: 10),
                        Text(
                          '•',
                          style: TextStyle(color: onSurfaceVariant),
                        ),
                        SizedBox(width: 10),
                        Icon(Icons.qr_code_2, size: 14, color: primaryColor),
                        SizedBox(width: 4),
                        Text(
                          'NMID: ID10200389218',
                          style: TextStyle(
                            fontSize: 11,
                            color: onSurfaceVariant,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 12),

              // Status Dapur
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: surfaceLow,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: primaryContainer,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Icon(
                        Icons.restaurant,
                        color: Colors.white,
                        size: 20,
                      ),
                    ),
                    const SizedBox(width: 12),
                    const Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Tiket Otomatis Terkirim ke Bar & Dapur',
                            style: TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 12,
                              color: onSurface,
                            ),
                          ),
                          Text(
                            '2 Antrean Minuman • 1 Makanan',
                            style: TextStyle(
                              fontSize: 11,
                              color: onSurfaceVariant,
                            ),
                          ),
                        ],
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Text(
                        'Cetak KOT OK',
                        style: TextStyle(
                          color: primaryColor,
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 16),

        // Panel Aksi Cepat
        Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'AKSI CEPAT TERMINAL',
                style: TextStyle(
                  color: onSurfaceVariant,
                  fontSize: 11,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 12),
              Row(
                children: [
                  // Button Pesanan Baru
                  Expanded(
                    flex: 2,
                    child: ElevatedButton.icon(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: primaryColor,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                      ),
                      onPressed: () {
                        // Kembali ke halaman Kasir / Home
                        Navigator.popUntil(
                          context,
                          ModalRoute.withName('/home'),
                        );
                      },
                      icon: const Icon(
                        Icons.add_circle,
                        color: Colors.white,
                        size: 20,
                      ),
                      label: const Text(
                        'Pesanan Baru',
                        style: TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),

                  // Button Cetak Struk
                  Expanded(
                    child: OutlinedButton(
                      style: OutlinedButton.styleFrom(
                        backgroundColor: surfaceLow,
                        side: BorderSide.none,
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                      ),
                      onPressed: () {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(
                            content: Text('Mencetak struk thermal...'),
                          ),
                        );
                      },
                      child: const Column(
                        children: [
                          Icon(Icons.print, color: primaryColor, size: 20),
                          SizedBox(height: 2),
                          Text(
                            'Cetak Struk',
                            style: TextStyle(
                              color: onSurface,
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),

                  // Button Kirim WA
                  Expanded(
                    child: OutlinedButton(
                      style: OutlinedButton.styleFrom(
                        backgroundColor: surfaceLow,
                        side: BorderSide.none,
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                      ),
                      onPressed: () {
                        ScaffoldMessenger.of(context).showSnackBar(
                          const SnackBar(
                            content: Text('Struk terkirim ke WhatsApp!'),
                          ),
                        );
                      },
                      child: const Column(
                        children: [
                          Icon(
                            Icons.send_to_mobile,
                            color: primaryColor,
                            size: 20,
                          ),
                          SizedBox(height: 2),
                          Text(
                            'Kirim WA',
                            style: TextStyle(
                              color: onSurface,
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ],
    );
  }

  // Kolom Kanan: Detail Nota & Rincian Item
  Widget _buildRightColumn(BuildContext context) {
    return Column(
      children: [
        Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.04),
                blurRadius: 15,
                offset: const Offset(0, 5),
              ),
            ],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header Nota
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Row(
                    children: [
                      Icon(Icons.receipt_long, color: primaryColor, size: 20),
                      SizedBox(width: 8),
                      Text(
                        'Rincian Nota',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                          color: onSurface,
                        ),
                      ),
                    ],
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 10,
                      vertical: 4,
                    ),
                    decoration: BoxDecoration(
                      color: secondaryContainer,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Text(
                      'PAID #204',
                      style: TextStyle(
                        color: primaryColor,
                        fontWeight: FontWeight.bold,
                        fontSize: 11,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),

              // Metadata Transaksi
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: surfaceLow,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Column(
                  children: [
                    _MetaRow(label: 'Pelanggan', value: 'Dimas Arya'),
                    SizedBox(height: 6),
                    _MetaRow(label: 'Meja', value: 'Meja #04 (Dine In)'),
                    SizedBox(height: 6),
                    _MetaRow(label: 'Kasir', value: 'Alisa Yasmin'),
                  ],
                ),
              ),
              const SizedBox(height: 16),

              const Text(
                'ITEM DIPESAN',
                style: TextStyle(
                  color: onSurfaceVariant,
                  fontSize: 11,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 8),

              // Daftar Item
              const _ItemTile(
                qty: '1x',
                title: 'Kopi Susu Cak Kebo',
                variant: 'Normal Ice, Less Sugar 70%',
                price: 'Rp 18.000',
              ),
              const SizedBox(height: 8),
              const _ItemTile(
                qty: '2x',
                title: 'Pisang Goreng Wijen',
                variant: 'Topping Cokelat Keju Parut',
                price: 'Rp 32.000',
              ),
              const SizedBox(height: 8),
              const _ItemTile(
                qty: '1x',
                title: 'Matcha Latte',
                variant: 'Substitusi Oatmilk (Oatside)',
                price: 'Rp 26.000',
              ),
              const SizedBox(height: 16),

              // Perhitungan Total
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: surfaceLow,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: const Column(
                  children: [
                    _PriceRow(
                      label: 'Subtotal Penjualan',
                      value: 'Rp 76.000',
                    ),
                    SizedBox(height: 6),
                    _PriceRow(
                      label: 'PB1 / Pajak Resto (10%)',
                      value: 'Rp 7.600',
                    ),
                    SizedBox(height: 6),
                    _PriceRow(
                      label: 'Diskon Promosi',
                      value: '- Rp 0',
                      isDiscount: true,
                    ),
                    Divider(height: 16),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          'Total Pembayaran',
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 14,
                            color: onSurface,
                          ),
                        ),
                        Text(
                          'Rp 83.600',
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 16,
                            color: primaryColor,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}

// Widget Pembantu untuk Baris Metadata
class _MetaRow extends StatelessWidget {
  final String label;
  final String value;
  const _MetaRow({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: const TextStyle(color: Color(0xFF3F4850), fontSize: 12),
        ),
        Text(
          value,
          style: const TextStyle(
            color: Color(0xFF0B1C30),
            fontWeight: FontWeight.bold,
            fontSize: 12,
          ),
        ),
      ],
    );
  }
}

// Widget Pembantu untuk Item Pesanan
class _ItemTile extends StatelessWidget {
  final String qty;
  final String title;
  final String variant;
  final String price;

  const _ItemTile({
    required this.qty,
    required this.title,
    required this.variant,
    required this.price,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(10),
      decoration: BoxDecoration(
        color: const Color(0xFFEFF4FF).withOpacity(0.7),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
            decoration: BoxDecoration(
              color: const Color(0xFFBAE6FD),
              borderRadius: BorderRadius.circular(6),
            ),
            child: Text(
              qty,
              style: const TextStyle(
                color: Color(0xFF006194),
                fontWeight: FontWeight.bold,
                fontSize: 12,
              ),
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: const TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 12,
                    color: Color(0xFF0B1C30),
                  ),
                ),
                Text(
                  variant,
                  style: const TextStyle(
                    fontSize: 11,
                    color: Color(0xFF3F4850),
                  ),
                ),
              ],
            ),
          ),
          Text(
            price,
            style: const TextStyle(
              fontWeight: FontWeight.bold,
              fontSize: 12,
              color: Color(0xFF0B1C30),
            ),
          ),
        ],
      ),
    );
  }
}

// Widget Pembantu untuk Baris Harga
class _PriceRow extends StatelessWidget {
  final String label;
  final String value;
  final bool isDiscount;

  const _PriceRow({
    required this.label,
    required this.value,
    this.isDiscount = false,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: const TextStyle(color: Color(0xFF3F4850), fontSize: 12),
        ),
        Text(
          value,
          style: TextStyle(
            color: isDiscount ? const Color(0xFF006194) : const Color(0xFF0B1C30),
            fontWeight: FontWeight.w500,
            fontSize: 12,
          ),
        ),
      ],
    );
  }
}