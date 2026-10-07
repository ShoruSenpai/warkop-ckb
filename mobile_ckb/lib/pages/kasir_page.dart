import 'package:flutter/material.dart';
import 'dart:async';
import 'package:intl/intl.dart';

class KasirScreen extends StatefulWidget {
  const KasirScreen({super.key});

  @override
  State<KasirScreen> createState() => _KasirScreenState();
}

class _KasirScreenState extends State<KasirScreen> {
  // Timer untuk jam realtime
  late Timer _timer;
  String _currentTime = "";
  String _currentDate = "";

  // State Interaktif Kasir
  String _activeCategory = 'Semua';
  String _orderType = 'Dine In';
  String _paymentMethod = 'QRIS';

  // Konstanta Warna Tailwind
  final Color primary = const Color(0xFF006194);
  final Color primaryContainer = const Color(0xFF007BB9);
  final Color secondary = const Color(0xFF396477);
  final Color secondaryContainer = const Color(0xFFBAE6FD);
  final Color surface = const Color(0xFFF8F9FF);
  final Color surfaceContainerLowest = const Color(0xFFFFFFFF);
  final Color surfaceContainerLow = const Color(0xFFEFF4FF);
  final Color surfaceContainer = const Color(0xFFE5EEFF);
  final Color surfaceContainerHigh = const Color(0xFFDCE9FF);
  final Color onSurface = const Color(0xFF0B1C30);
  final Color onSurfaceVariant = const Color(0xFF3F4850);
  final Color onPrimary = const Color(0xFFFFFFFF);
  final Color outline = const Color(0xFF707881);
  final Color onSecondaryContainer = const Color(0xFF3D687C);

  // Data Dummy Kategori
  final List<String> _categories = [
    'Semua',
    'Kopi Susu',
    'Manual Brew',
    'Non-Kopi',
    'Camilan Tradisional',
    'Makanan Berat'
  ];

  // Data Dummy Produk
  final List<Map<String, dynamic>> _products = [
    {
      'cat': 'Kopi Susu',
      'name': 'Kopi Susu Cak Kebo',
      'desc': 'Gula aren organik, double espresso',
      'price': 18000,
      'img': 'https://lh3.googleusercontent.com/aida-public/AB6AXuARybcadp9pYMQHJULLtF0871NifTwt1KSueZDE_gbnnaIFzKWeun-ksPIfMnBJiU7JfBvI2PVHHLzbxrCBwkliATgfRgqyPolB0sAemmbjbDaST9aSLDwEhGtxi0Y7Cke9U_pwhWZx9JvdQVdnN_GapDmRB5h_IrQD4LSmg6dmO6qnnJeoP5tqSjQh1Uf1o11LULqrEUOQshES8RR1bdPn3I4xG5f2QXEtcDRqv9ZUz-vTSn6PxIgfjQ',
      'tag': 'Favorit'
    },
    {
      'cat': 'Manual Brew',
      'name': 'Espresso Single',
      'desc': 'Arabica Gayo wash 30ml',
      'price': 15000,
      'img': 'https://lh3.googleusercontent.com/aida-public/AB6AXuDaaCnJp_jl4wJMguixT2C0oEHKSxcYq0r-Huao-aYpCmZmyaSx1SFz1d9QrN2m2HlryQ3Ys0R2eVFH1X-RnwAe_kkHbpAvZ_7eCVWZhrNXsdP9D198BpZOKeFtSmDThI2lJUUeK8Hkx6VxVBRGLJ1RvB2zSPO3oWw2KtxO71BkJGU0s7jKFQlxoJOPZQQPD2XyTZRvQPEy_Fg2B6pZH38UBT-k5pq2mnvPn5Mz8JWRTqjTcFTZ5-3Bvg',
    },
    {
      'cat': 'Manual Brew',
      'name': 'Americano Dingin',
      'desc': 'Clean body, fruity notes',
      'price': 16000,
      'img': 'https://lh3.googleusercontent.com/aida-public/AB6AXuACU9VPBe25t4rbG5HD7MD4VIc-EHESgrVYRomYzRxUH4G8zwtKI2sQy0kobtwQ6xYhycuf_J0iDZE2OvY-nUyijfE7W201nJEAXMKTlcCeKYPZ0WyNxXJPPxPoOtvrH6G4jR8lf6yoofCFODIYwrUTMtT7bMVUG6IRtBplAX5tnVjc8RRCx0HxJ4E72dnyT50ImQspGp_0PQt0MxPFvZq9-LRCjw8Crpt47CwHw_q55141Npux5fC49A',
    },
    {
      'cat': 'Non-Kopi',
      'name': 'Matcha Latte',
      'desc': 'Uji matcha murni dengan oatmilk',
      'price': 26000,
      'img': 'https://lh3.googleusercontent.com/aida-public/AB6AXuCnpi1EXgLfpIPOEQdbBOMduUZJfOn7_NWpMByE83_vlrNg3m-Gk8QLAvwMDzmPxaWUpUPOVy9Rhix5f_FmJUTS_O594-iuiCQOg8qSmU3lfww7rootD3cHNEm65SpSBCoznf3N8zJOU7INbtMuHMUBG49xAlFE9TN_dEzZGjVRM3YAx2vEIYCFpbzUUfFz1kS9fhaaBLkRzfQEFPJaqwI3Vtsgnm-3n26NOVBP-gGwi2e1r03u-AKXAw',
      'tag': 'Artisan'
    },
    {
      'cat': 'Camilan Tradisional',
      'name': 'Pisang Goreng Wijen',
      'desc': 'Pisang kepok madu, renyah wangi',
      'price': 16000,
      'img': 'https://lh3.googleusercontent.com/aida-public/AB6AXuD8Cux6ikayaCbNXUstL6MLPbuBqkSoVxxLO8vtzWkBj-UotkPdW0ctLjPjN3lC8LzSqZnj9yaMJ5Ivz6VxUJWB3-AquCCrfRwdW3yFz5W7BKlD9dkvVvWNE705IIxQd2pnsA57CVM2-WUq4IY14Hd8PsUbvj4S7K1PLHeiM7AUNX0n4jgMACj0czPW8Oc6irEW08UwH80yxLiPE8yo6W8SO2KiXoZLSAZXfciP3jMOVpu6BKlQ_UBDrw',
    },
    {
      'cat': 'Makanan Berat',
      'name': 'Nasi Goreng Kampung',
      'desc': 'Telur mata sapi, acar segar',
      'price': 28000,
      'img': 'https://lh3.googleusercontent.com/aida-public/AB6AXuC1h55M00RisBoFXM7SiZuNbZU-TFfVNorP_7c_66_IJiqCYVY9kMfs_Jiq5NHChRW26tZbrYOrsD29cQEjCiFWtScsJsUOLrV6Wbis-vjfEqjGsvNo1i446x0VzTZu8Du4ccPMoQ5MGogmnRGdNb27wmM1FrsX2f79L44Vl-ZoHtIF3nlCDmlKFcpoVvPRQCgpvrFuM_J8hpsth2_aCdRYxqdwh4TnJUCjlTJIyp0NytFtmv_W5tGltQ',
    },
  ];

  // Data Dummy Keranjang (Stateful)
  final List<Map<String, dynamic>> _cart = [
    {
      'name': 'Kopi Susu Cak Kebo',
      'variant': 'Normal Ice, Less Sugar',
      'price': 18000,
      'qty': 1
    },
    {
      'name': 'Pisang Goreng Wijen',
      'variant': 'Topping Cokelat Keju',
      'price': 16000, // Harga satuan
      'qty': 2
    },
    {
      'name': 'Matcha Latte',
      'variant': 'Substitusi Oatmilk (+Rp 6.000)',
      'price': 26000,
      'qty': 1
    },
  ];

  @override
  void initState() {
    super.initState();
    _updateTime();
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (mounted) _updateTime();
    });
  }

  @override
  void dispose() {
    _timer.cancel();
    super.dispose();
  }

  void _updateTime() {
    final now = DateTime.now();
    setState(() {
      _currentTime = "${DateFormat('HH:mm').format(now)} WIB";
      _currentDate = DateFormat('EEEE, dd MMM yyyy', 'id_ID').format(now);
    });
  }

  // Helper Format Rupiah
  String _formatRp(int number) {
    final formatCurrency = NumberFormat.currency(
        locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return formatCurrency.format(number);
  }

  @override
  Widget build(BuildContext context) {
    // Menghitung Total Keranjang
    int subtotal = _cart.fold(0, (sum, item) => sum + (item['price'] as int) * (item['qty'] as int));
    int tax = (subtotal * 0.10).round();
    int total = subtotal + tax;

    return Scaffold(
      backgroundColor: surface,
      // ==========================================
      // HEADER POS TERMINAL
      // ==========================================
      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(70),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
          decoration: BoxDecoration(
            color: surfaceContainerLowest.withOpacity(0.9),
            boxShadow: [
              BoxShadow(
                  color: Colors.lightBlue.withOpacity(0.06),
                  blurRadius: 20,
                  offset: const Offset(0, 4))
            ],
          ),
          child: SafeArea(
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                // KIRI: Logo & Info Kasir
                Row(
                  children: [
                    Container(
                      width: 40,
                      height: 40,
                      decoration: BoxDecoration(
                        color: primary,
                        borderRadius: BorderRadius.circular(12),
                        boxShadow: [
                          BoxShadow(
                              color: primary.withOpacity(0.25), blurRadius: 12)
                        ],
                      ),
                      child: Icon(Icons.local_cafe, color: onPrimary, size: 20),
                    ),
                    const SizedBox(width: 12),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text('Cak Kebo',
                            style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                                color: onSurface,
                                height: 1)),
                        Text('POS TERMINAL',
                            style: TextStyle(
                                fontSize: 10,
                                fontWeight: FontWeight.bold,
                                color: primary,
                                letterSpacing: 1.0)),
                      ],
                    ),
                    // Hanya tampil di layar lebar (Tablet/Desktop)
                    if (MediaQuery.of(context).size.width > 900) ...[
                      const SizedBox(width: 24),
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 16, vertical: 6),
                        decoration: BoxDecoration(
                          color: surfaceContainerLow,
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Row(
                          children: [
                            Container(
                              width: 8,
                              height: 8,
                              decoration: BoxDecoration(
                                  color: primaryContainer,
                                  shape: BoxShape.circle),
                            ),
                            const SizedBox(width: 8),
                            Text('Kasir: Alisa Yasmin',
                                style: TextStyle(
                                    fontSize: 12,
                                    fontWeight: FontWeight.bold,
                                    color: onSurface)),
                            const Padding(
                              padding: EdgeInsets.symmetric(horizontal: 8),
                              child: Text('•',
                                  style: TextStyle(color: Colors.grey)),
                            ),
                            Text('Shift Pagi • Outlet Senopati',
                                style: TextStyle(
                                    fontSize: 12, color: onSurfaceVariant)),
                          ],
                        ),
                      )
                    ]
                  ],
                ),

                // TENGAH: Navigasi Atas (Khas Tablet)
                if (MediaQuery.of(context).size.width > 700)
                  Container(
                    padding: const EdgeInsets.all(4),
                    decoration: BoxDecoration(
                      color: surfaceContainerLow,
                      borderRadius: BorderRadius.circular(30),
                    ),
                    child: Row(
                      children: [
                        _buildNavTab('POS', true, '/kasir'),
                        _buildNavTab('Absensi', false, '/home'),
                        _buildNavTab('Jadwal', false, '/jadwal'),
                        _buildNavTab('Profil', false, '/profil'),
                      ],
                    ),
                  ),

                // KANAN: Jam & Profil
                Row(
                  children: [
                    if (MediaQuery.of(context).size.width > 600)
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(_currentTime,
                              style: TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.bold,
                                  color: onSurface,
                                  height: 1.2)),
                          Text(_currentDate,
                              style: TextStyle(
                                  fontSize: 10, color: onSurfaceVariant)),
                        ],
                      ),
                    const SizedBox(width: 16),
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 12, vertical: 6),
                      decoration: BoxDecoration(
                        color: secondaryContainer.withOpacity(0.5),
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: Row(
                        children: [
                          Icon(Icons.table_restaurant,
                              size: 18, color: secondary),
                          const SizedBox(width: 4),
                          Text('Meja #04',
                              style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  color: onSecondaryContainer)),
                        ],
                      ),
                    ),
                    const SizedBox(width: 16),
                    Container(
                      width: 36,
                      height: 36,
                      decoration: BoxDecoration(
                          color: primary, shape: BoxShape.circle),
                      child: Icon(Icons.person, color: onPrimary, size: 18),
                    )
                  ],
                )
              ],
            ),
          ),
        ),
      ),

      // ==========================================
      // BODY (RESPONSIVE GRID)
      // ==========================================
      body: LayoutBuilder(
        builder: (context, constraints) {
          // Jika layar lebar (>900px), bagi dua kolom. Jika kecil, tumpuk ke bawah.
          bool isDesktop = constraints.maxWidth > 900;

          Widget content = isDesktop
              ? Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Kolom Kiri: Produk (60%)
                    Expanded(flex: 7, child: _buildProductSection(constraints)),
                    // Kolom Kanan: Keranjang (40%)
                    Expanded(flex: 5, child: _buildCartSection(subtotal, tax, total)),
                  ],
                )
              : Column(
                  children: [
                    _buildProductSection(constraints),
                    const SizedBox(height: 16),
                    _buildCartSection(subtotal, tax, total),
                  ],
                );

          return SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: content,
          );
        },
      ),

      // ==========================================
      // FOOTER
      // ==========================================
      bottomNavigationBar: Container(
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
        color: surfaceContainerLowest.withOpacity(0.6),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text('© 2026 Cak Kebo F&B Group • Cloud POS v2.4.1',
                style: TextStyle(fontSize: 10, color: onSurfaceVariant)),
            Row(
              children: [
                Container(
                  width: 8,
                  height: 8,
                  decoration: BoxDecoration(
                      color: primaryContainer, shape: BoxShape.circle),
                ),
                const SizedBox(width: 6),
                Text('Terhubung ke Server • Printer Dapur: Aktif',
                    style: TextStyle(fontSize: 10, color: onSurfaceVariant)),
              ],
            )
          ],
        ),
      ),
    );
  }

  // --- KOMPONEN NAVIGASI ATAS ---
  Widget _buildNavTab(String text, bool isActive, String route) {
    return GestureDetector(
      onTap: () {
        if (!isActive) Navigator.pushReplacementNamed(context, route);
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
        decoration: BoxDecoration(
          color: isActive ? primary : Colors.transparent,
          borderRadius: BorderRadius.circular(20),
          boxShadow: isActive
              ? [
                  BoxShadow(
                      color: primary.withOpacity(0.25),
                      blurRadius: 14,
                      offset: const Offset(0, 4))
                ]
              : [],
        ),
        child: Text(
          text,
          style: TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.bold,
            color: isActive ? onPrimary : onSurfaceVariant,
          ),
        ),
      ),
    );
  }

  // --- KOLOM KIRI: BAGIAN PRODUK ---
  Widget _buildProductSection(BoxConstraints constraints) {
    // Tentukan jumlah kolom grid berdasarkan lebar layar
    int crossAxisCount = constraints.maxWidth > 1200
        ? 4
        : constraints.maxWidth > 900
            ? 3
            : 2;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // 1. Search Bar
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
          decoration: BoxDecoration(
            color: surfaceContainerLowest,
            borderRadius: BorderRadius.circular(30),
            boxShadow: [
              BoxShadow(
                  color: Colors.lightBlue.withOpacity(0.05),
                  blurRadius: 18,
                  offset: const Offset(0, 4))
            ],
          ),
          child: Row(
            children: [
              Icon(Icons.search, color: outline),
              const SizedBox(width: 12),
              Expanded(
                child: TextField(
                  decoration: InputDecoration(
                    hintText: 'Cari produk makanan atau minuman...',
                    hintStyle: TextStyle(color: onSurfaceVariant, fontSize: 14),
                    border: InputBorder.none,
                  ),
                ),
              ),
              IconButton(
                icon: const Icon(Icons.qr_code_scanner),
                color: onSurfaceVariant,
                style: IconButton.styleFrom(backgroundColor: surfaceContainerLow),
                onPressed: () {},
              ),
              IconButton(
                icon: const Icon(Icons.close),
                color: onSurfaceVariant,
                style: IconButton.styleFrom(backgroundColor: surfaceContainerLow),
                onPressed: () {},
              ),
            ],
          ),
        ),
        const SizedBox(height: 16),

        // 2. Kategori Filter (Horizontal Scroll)
        SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          child: Row(
            children: _categories.map((cat) {
              bool isActive = _activeCategory == cat;
              return Padding(
                padding: const EdgeInsets.only(right: 8),
                child: ChoiceChip(
                  label: Text(cat),
                  selected: isActive,
                  onSelected: (val) => setState(() => _activeCategory = cat),
                  selectedColor: primary,
                  backgroundColor: surfaceContainerLowest,
                  labelStyle: TextStyle(
                    fontWeight: FontWeight.bold,
                    color: isActive ? onPrimary : onSurfaceVariant,
                  ),
                  shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(20),
                      side: BorderSide.none),
                  elevation: isActive ? 4 : 1,
                  shadowColor: primary.withOpacity(0.4),
                ),
              );
            }).toList(),
          ),
        ),
        const SizedBox(height: 16),

        // 3. Grid Produk
        GridView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
            crossAxisCount: crossAxisCount,
            childAspectRatio: 0.75, // Menyesuaikan proporsi kartu
            crossAxisSpacing: 16,
            mainAxisSpacing: 16,
          ),
          itemCount: _products.length,
          itemBuilder: (context, index) {
            final item = _products[index];
            return _buildProductCard(item);
          },
        ),
      ],
    );
  }

  Widget _buildProductCard(Map<String, dynamic> item) {
    return InkWell(
      onTap: () {
        // Simulasi tambah ke keranjang
      },
      borderRadius: BorderRadius.circular(24),
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: surfaceContainerLowest,
          borderRadius: BorderRadius.circular(24),
          boxShadow: [
            BoxShadow(
                color: Colors.lightBlue.withOpacity(0.06),
                blurRadius: 20,
                offset: const Offset(0, 8))
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Gambar Produk
            Expanded(
              child: Stack(
                children: [
                  ClipRRect(
                    borderRadius: BorderRadius.circular(16),
                    child: Image.network(
                      item['img'],
                      width: double.infinity,
                      fit: BoxFit.cover,
                    ),
                  ),
                  if (item.containsKey('tag'))
                    Positioned(
                      top: 8,
                      left: 8,
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 8, vertical: 2),
                        decoration: BoxDecoration(
                          color: surfaceContainerLowest.withOpacity(0.9),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          item['tag'],
                          style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: primary,
                              letterSpacing: 1),
                        ),
                      ),
                    )
                ],
              ),
            ),
            const SizedBox(height: 12),
            // Info Produk
            Text(
              item['cat'].toString().toUpperCase(),
              style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.bold,
                  letterSpacing: 1,
                  color: onSurfaceVariant),
            ),
            const SizedBox(height: 4),
            Text(
              item['name'],
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: TextStyle(
                  fontSize: 16, fontWeight: FontWeight.bold, color: onSurface),
            ),
            const SizedBox(height: 2),
            Text(
              item['desc'],
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: TextStyle(fontSize: 12, color: onSurfaceVariant),
            ),
            const SizedBox(height: 12),
            // Harga & Tombol Add
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  _formatRp(item['price']),
                  style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: primary),
                ),
                Container(
                  width: 36,
                  height: 36,
                  decoration: BoxDecoration(
                    color: secondaryContainer,
                    shape: BoxShape.circle,
                  ),
                  child: Icon(Icons.add, color: onSecondaryContainer, size: 20),
                )
              ],
            )
          ],
        ),
      ),
    );
  }

  // --- KOLOM KANAN: BAGIAN KERANJANG KASIR ---
  Widget _buildCartSection(int subtotal, int tax, int total) {
    return Container(
      margin: EdgeInsets.only(
          left: MediaQuery.of(context).size.width > 900 ? 24 : 0),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: surfaceContainerLowest,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
              color: Colors.lightBlue.withOpacity(0.08),
              blurRadius: 32,
              offset: const Offset(0, -6))
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // 1. Header Keranjang & Switcher (Dine In / Take Away)
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Text('Pesanan Saat Ini',
                      style: TextStyle(
                          fontSize: 22,
                          fontWeight: FontWeight.bold,
                          color: onSurface)),
                  const SizedBox(width: 8),
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                    decoration: BoxDecoration(
                        color: secondaryContainer.withOpacity(0.7),
                        borderRadius: BorderRadius.circular(12)),
                    child: Text('#ORD-204',
                        style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: onSecondaryContainer)),
                  )
                ],
              ),
              Container(
                padding: const EdgeInsets.all(4),
                decoration: BoxDecoration(
                  color: surfaceContainerLow,
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Row(
                  children: ['Dine In', 'Take Away'].map((type) {
                    bool isActive = _orderType == type;
                    return GestureDetector(
                      onTap: () => setState(() => _orderType = type),
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 12, vertical: 6),
                        decoration: BoxDecoration(
                          color: isActive ? primary : Colors.transparent,
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: Text(
                          type,
                          style: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                              color: isActive ? onPrimary : onSurfaceVariant),
                        ),
                      ),
                    );
                  }).toList(),
                ),
              )
            ],
          ),
          const SizedBox(height: 12),

          // 2. Info Pelanggan & Meja
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: surfaceContainerLow,
              borderRadius: BorderRadius.circular(16),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    Icon(Icons.person_pin_circle, color: primary, size: 20),
                    const SizedBox(width: 8),
                    Text('Meja 04 • Dimas Arya',
                        style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.bold,
                            color: onSurface)),
                  ],
                ),
                Icon(Icons.edit, color: primary, size: 18),
              ],
            ),
          ),
          const SizedBox(height: 16),

          // 3. List Item Keranjang
          ListView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: _cart.length,
            itemBuilder: (context, index) {
              final item = _cart[index];
              return Container(
                margin: const EdgeInsets.only(bottom: 12),
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: surface,
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Column(
                  children: [
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text("${item['qty']}x ${item['name']}",
                                  style: TextStyle(
                                      fontSize: 14,
                                      fontWeight: FontWeight.bold,
                                      color: onSurface)),
                              const SizedBox(height: 4),
                              Text(item['variant'],
                                  style: TextStyle(
                                      fontSize: 12, color: onSurfaceVariant)),
                            ],
                          ),
                        ),
                        Text(_formatRp(item['price'] * item['qty']),
                            style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                                color: onSurface)),
                      ],
                    ),
                    const SizedBox(height: 8),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        Container(
                          decoration: BoxDecoration(
                              color: surfaceContainerLowest,
                              borderRadius: BorderRadius.circular(20),
                              boxShadow: [
                                BoxShadow(
                                    color: Colors.black.withOpacity(0.05),
                                    blurRadius: 4)
                              ]),
                          child: Row(
                            children: [
                              IconButton(
                                icon: const Icon(Icons.remove, size: 16),
                                onPressed: () {},
                                constraints: const BoxConstraints(),
                                padding: const EdgeInsets.all(6),
                                style: IconButton.styleFrom(
                                    backgroundColor: surfaceContainerLow),
                              ),
                              Padding(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 12),
                                child: Text(item['qty'].toString(),
                                    style: TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.bold,
                                        color: onSurface)),
                              ),
                              IconButton(
                                icon: const Icon(Icons.add,
                                    size: 16, color: Colors.white),
                                onPressed: () {},
                                constraints: const BoxConstraints(),
                                padding: const EdgeInsets.all(6),
                                style: IconButton.styleFrom(
                                    backgroundColor: primary),
                              ),
                            ],
                          ),
                        )
                      ],
                    )
                  ],
                ),
              );
            },
          ),

          // Tombol Tambah Catatan
          TextButton.icon(
            onPressed: () {},
            icon: Icon(Icons.note_add, color: primary, size: 18),
            label: Text('+ Tambah Catatan Pesanan',
                style: TextStyle(color: primary, fontWeight: FontWeight.bold)),
          ),
          const SizedBox(height: 16),

          // 4. Ringkasan Biaya (Subtotal, Tax, Total)
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: surfaceContainerLow.withOpacity(0.7),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Column(
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text('Subtotal (${_cart.length} item)',
                        style: TextStyle(fontSize: 14, color: onSurfaceVariant)),
                    Text(_formatRp(subtotal),
                        style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.bold,
                            color: onSurface)),
                  ],
                ),
                const SizedBox(height: 8),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text('Pajak Restoran (PB1 10%)',
                        style: TextStyle(fontSize: 14, color: onSurfaceVariant)),
                    Text(_formatRp(tax),
                        style: TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.bold,
                            color: onSurface)),
                  ],
                ),
                Divider(color: surfaceContainerHigh, height: 24),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    Text('Total Akhir',
                        style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: onSurface)),
                    Text(_formatRp(total),
                        style: TextStyle(
                            fontSize: 32, // Display Large
                            fontWeight: FontWeight.bold,
                            letterSpacing: -1,
                            color: primary)),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),

          // 5. Metode Pembayaran Cepat
          Text('METODE BAYAR CEPAT',
              style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.bold,
                  letterSpacing: 1,
                  color: onSurfaceVariant)),
          const SizedBox(height: 8),
          Row(
            children: [
              _buildPaymentMethod('QRIS', Icons.qr_code_2),
              const SizedBox(width: 8),
              _buildPaymentMethod('Tunai', Icons.payments),
              const SizedBox(width: 8),
              _buildPaymentMethod('Kartu Debit', Icons.credit_card),
            ],
          ),
          const SizedBox(height: 24),

          // 6. Tombol Aksi (Bayar & Simpan)
          ElevatedButton(
            onPressed: () {},
            style: ElevatedButton.styleFrom(
              backgroundColor: primary,
              foregroundColor: onPrimary,
              elevation: 4,
              minimumSize: const Size(double.infinity, 64),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(30),
              ),
            ),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.contactless, size: 22),
                const SizedBox(width: 12),
                Text('Bayar Sekarang (${_formatRp(total)})',
                    style: const TextStyle(
                        fontSize: 18, fontWeight: FontWeight.bold)),
                const SizedBox(width: 12),
                const Icon(Icons.arrow_forward, size: 20),
              ],
            ),
          ),
          const SizedBox(height: 8),
          TextButton.icon(
            onPressed: () {},
            icon: Icon(Icons.pause_circle, color: secondary, size: 18),
            label: Text('Simpan Pesanan / Hold Bill',
                style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                    color: secondary)),
            style: TextButton.styleFrom(
              minimumSize: const Size(double.infinity, 48),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPaymentMethod(String title, IconData icon) {
    bool isActive = _paymentMethod == title;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _paymentMethod = title),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            color: isActive ? primary : surfaceContainerLow,
            borderRadius: BorderRadius.circular(16),
            boxShadow: isActive
                ? [
                    BoxShadow(
                        color: Colors.black.withOpacity(0.1), blurRadius: 4)
                  ]
                : [],
          ),
          child: Column(
            children: [
              Icon(icon, color: isActive ? onPrimary : onSurface, size: 20),
              const SizedBox(height: 4),
              Text(title,
                  style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                      color: isActive ? onPrimary : onSurface)),
            ],
          ),
        ),
      ),
    );
  }
}