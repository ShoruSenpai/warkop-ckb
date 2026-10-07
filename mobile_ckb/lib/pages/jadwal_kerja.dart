import 'package:flutter/material.dart';

class JadwalScreen extends StatefulWidget {
  const JadwalScreen({super.key});

  @override
  State<JadwalScreen> createState() => _JadwalScreenState();
}

class _JadwalScreenState extends State<JadwalScreen> {
  // State untuk tanggal yang dipilih di kalender mingguan
  int _selectedDayIndex = 3; // Default Kamis, 24 Okt (index ke-3)
  
  // Data Hari dalam Seminggu
  final List<Map<String, dynamic>> _weekDays = [
    {'day': 'Sen', 'date': 21, 'status': 'work'},
    {'day': 'Sel', 'date': 22, 'status': 'work'},
    {'day': 'Rab', 'date': 23, 'status': 'work'},
    {'day': 'Kam', 'date': 24, 'status': 'active'},
    {'day': 'Jum', 'date': 25, 'status': 'work'},
    {'day': 'Sab', 'date': 26, 'status': 'off'},
    {'day': 'Min', 'date': 27, 'status': 'work'},
  ];

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
  final Color surfaceContainerHighest = const Color(0xFFD3E4FE);
  final Color onSurface = const Color(0xFF0B1C30);
  final Color onSurfaceVariant = const Color(0xFF3F4850);
  final Color onPrimary = const Color(0xFFFFFFFF);
  final Color onSecondaryContainer = const Color(0xFF3D687C);

  // Fungsi untuk Menampilkan Modal Bottom Sheet Ajukan Tukar Shift
  void _showSwapModal(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) {
        String selectedReplacement = 'Dimas Arya';
        TextEditingController reasonController = TextEditingController();

        return StatefulBuilder(
          builder: (BuildContext context, StateSetter setStateModal) {
            return Container(
              padding: EdgeInsets.only(
                bottom: MediaQuery.of(context).viewInsets.bottom,
                left: 20,
                right: 20,
                top: 12,
              ),
              decoration: BoxDecoration(
                color: surfaceContainerLowest,
                borderRadius: const BorderRadius.vertical(top: Radius.circular(30)),
                boxShadow: [
                  BoxShadow(
                      color: Colors.black.withOpacity(0.15),
                      blurRadius: 30,
                      offset: const Offset(0, -5))
                ],
              ),
              child: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Handle Bar & Close
                    Center(
                      child: Container(
                        width: 40,
                        height: 6,
                        decoration: BoxDecoration(
                          color: surfaceContainerHighest,
                          borderRadius: BorderRadius.circular(3),
                        ),
                      ),
                    ),
                    const SizedBox(height: 12),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Icon(Icons.sync_alt, color: primary, size: 16),
                                const SizedBox(width: 4),
                                Text('FORMULIR INTERNAL',
                                    style: TextStyle(
                                        fontSize: 10,
                                        fontWeight: FontWeight.bold,
                                        color: primary,
                                        letterSpacing: 1)),
                              ],
                            ),
                            const SizedBox(height: 2),
                            Text('Ajukan Tukar Shift',
                                style: TextStyle(
                                    fontSize: 18,
                                    fontWeight: FontWeight.bold,
                                    color: onSurface)),
                            Text('Shift Pagi • Kamis, 24 Okt (08.00 - 16.00 WIB)',
                                style: TextStyle(
                                    fontSize: 12, color: secondary)),
                          ],
                        ),
                        IconButton(
                          icon: const Icon(Icons.close, size: 18),
                          style: IconButton.styleFrom(
                            backgroundColor: surfaceContainerLow,
                            foregroundColor: onSurfaceVariant,
                          ),
                          onPressed: () => Navigator.pop(context),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),

                    // Pilih Rekan Kerja Pengganti
                    Text('Pilih Rekan Kerja Pengganti',
                        style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: onSurface)),
                    const SizedBox(height: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                      decoration: BoxDecoration(
                        color: surfaceContainerLow,
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: Row(
                        children: [
                          Icon(Icons.search, color: secondary, size: 20),
                          const SizedBox(width: 8),
                          Expanded(
                            child: TextField(
                              decoration: InputDecoration(
                                hintText: 'Cari nama barista / kasir...',
                                hintStyle: TextStyle(
                                    fontSize: 12, color: secondary.withOpacity(0.6)),
                                border: InputBorder.none,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 12),

                    Text('Rekomendasi Staf Hari Ini (Off):',
                        style: TextStyle(fontSize: 10, color: secondary)),
                    const SizedBox(height: 6),
                    
                    // Radio Pilihan 1: Dimas Arya
                    InkWell(
                      onTap: () => setStateModal(() => selectedReplacement = 'Dimas Arya'),
                      borderRadius: BorderRadius.circular(16),
                      child: Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: surfaceContainerLow,
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Row(
                              children: [
                                Container(
                                  width: 32,
                                  height: 32,
                                  decoration: BoxDecoration(
                                    color: primary.withOpacity(0.1),
                                    shape: BoxShape.circle,
                                  ),
                                  alignment: Alignment.center,
                                  child: Text('DA',
                                      style: TextStyle(
                                          fontSize: 12,
                                          fontWeight: FontWeight.bold,
                                          color: primary)),
                                ),
                                const SizedBox(width: 12),
                                Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text('Dimas Arya',
                                        style: TextStyle(
                                            fontSize: 14,
                                            fontWeight: FontWeight.bold,
                                            color: onSurface)),
                                    Text('Barista • Status: Off',
                                        style: TextStyle(
                                            fontSize: 10,
                                            fontWeight: FontWeight.bold,
                                            color: primary)),
                                  ],
                                ),
                              ],
                            ),
                            Radio<String>(
                              value: 'Dimas Arya',
                              groupValue: selectedReplacement,
                              activeColor: primary,
                              onChanged: (val) => setStateModal(() => selectedReplacement = val!),
                            )
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 8),

                    // Radio Pilihan 2: Siti Sarah
                    InkWell(
                      onTap: () => setStateModal(() => selectedReplacement = 'Siti Sarah'),
                      borderRadius: BorderRadius.circular(16),
                      child: Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: surfaceContainerLow,
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Row(
                              children: [
                                Container(
                                  width: 32,
                                  height: 32,
                                  decoration: BoxDecoration(
                                    color: surfaceContainerHigh,
                                    shape: BoxShape.circle,
                                  ),
                                  alignment: Alignment.center,
                                  child: Text('SS',
                                      style: TextStyle(
                                          fontSize: 12,
                                          fontWeight: FontWeight.bold,
                                          color: secondary)),
                                ),
                                const SizedBox(width: 12),
                                Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text('Siti Sarah',
                                        style: TextStyle(
                                            fontSize: 14,
                                            fontWeight: FontWeight.bold,
                                            color: onSurface)),
                                    Text('Floor Staff • Status: Off',
                                        style: TextStyle(
                                            fontSize: 10, color: secondary)),
                                  ],
                                ),
                              ],
                            ),
                            Radio<String>(
                              value: 'Siti Sarah',
                              groupValue: selectedReplacement,
                              activeColor: primary,
                              onChanged: (val) => setStateModal(() => selectedReplacement = val!),
                            )
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Alasan Pergantian
                    Text('Alasan Pergantian',
                        style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: onSurface)),
                    const SizedBox(height: 6),
                    Wrap(
                      spacing: 6,
                      runSpacing: 6,
                      children: ['Urusan Keluarga', 'Jadwal Kuliah / Ujian', 'Kurang Sehat']
                          .map((reason) => ActionChip(
                                label: Text(reason),
                                labelStyle: const TextStyle(fontSize: 12),
                                backgroundColor: surfaceContainer,
                                shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(20)),
                                onPressed: () {
                                  reasonController.text = reason;
                                },
                              ))
                          .toList(),
                    ),
                    const SizedBox(height: 8),
                    TextField(
                      controller: reasonController,
                      maxLines: 2,
                      decoration: InputDecoration(
                        hintText: 'Tuliskan catatan tambahan untuk Supervisor...',
                        hintStyle: TextStyle(fontSize: 12, color: secondary.withOpacity(0.6)),
                        filled: true,
                        fillColor: surfaceContainerLow,
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(16),
                          borderSide: BorderSide.none,
                        ),
                      ),
                    ),
                    const SizedBox(height: 12),

                    // Info Peringatan
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: surfaceContainerHigh.withOpacity(0.6),
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: Row(
                        children: [
                          Icon(Icons.info_outline, color: primary, size: 18),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              'Pengajuan akan diteruskan ke Supervisor Outlet untuk disetujui paling lambat H-1 shift.',
                              style: TextStyle(fontSize: 12, color: secondary),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 20),

                    // Tombol Aksi Bawah
                    Row(
                      children: [
                        Expanded(
                          child: OutlinedButton(
                            style: OutlinedButton.styleFrom(
                              backgroundColor: surfaceContainerLow,
                              foregroundColor: onSurface,
                              side: BorderSide.none,
                              padding: const EdgeInsets.symmetric(vertical: 14),
                              shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(16)),
                            ),
                            onPressed: () => Navigator.pop(context),
                            child: const Text('Batal',
                                style: TextStyle(fontWeight: FontWeight.bold)),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: ElevatedButton(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: primary,
                              foregroundColor: onPrimary,
                              elevation: 2,
                              padding: const EdgeInsets.symmetric(vertical: 14),
                              shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(16)),
                            ),
                            onPressed: () {
                              Navigator.pop(context);
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(content: Text('Pengajuan tukar shift berhasil dikirim!')),
                              );
                            },
                            child: const Text('Kirim Pengajuan',
                                style: TextStyle(fontWeight: FontWeight.bold)),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 24),
                  ],
                ),
              ),
            );
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: surface,
      // ==========================================
      // HEADER / APP BAR
      // ==========================================
      appBar: AppBar(
        backgroundColor: surface.withOpacity(0.85),
        elevation: 0,
        automaticallyImplyLeading: false, // Sembunyikan tombol back default
        title: Row(
          children: [
            Container(
              width: 36,
              height: 36,
              decoration: BoxDecoration(
                color: surfaceContainerHigh,
                shape: BoxShape.circle,
              ),
              child: Icon(Icons.coffee, color: primary, size: 20),
            ),
            const SizedBox(width: 12),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'CAK KEBO PORTAL',
                  style: TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.bold,
                      letterSpacing: 1.5,
                      color: onSurfaceVariant),
                ),
                Text(
                  'Jadwal',
                  style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: onSurface),
                ),
              ],
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: Icon(Icons.notifications_none, color: onSurfaceVariant),
            onPressed: () {},
          ),
          Container(
            margin: const EdgeInsets.only(right: 16),
            width: 32,
            height: 32,
            decoration: BoxDecoration(
              color: primary,
              shape: BoxShape.circle,
            ),
            child: Icon(Icons.person, color: onPrimary, size: 18),
          )
        ],
      ),

      // ==========================================
      // BODY KONTEN
      // ==========================================
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // 1. HEADER & PERIODE TOGGLE
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Cak Kebo Senopati',
                        style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: secondary,
                            letterSpacing: 1)),
                    const SizedBox(height: 2),
                    Text('Jadwal Kerja Saya',
                        style: TextStyle(
                            fontSize: 22,
                            fontWeight: FontWeight.bold,
                            color: onSurface)),
                  ],
                ),
                Container(
                  width: 44,
                  height: 44,
                  decoration: BoxDecoration(
                    color: surfaceContainerLowest,
                    shape: BoxShape.circle,
                    boxShadow: [
                      BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 4)
                    ],
                  ),
                  child: Icon(Icons.event_available, color: primary, size: 22),
                ),
              ],
            ),
            const SizedBox(height: 12),

            // Toggle Bulan / Minggu
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: surfaceContainerLowest,
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(color: Colors.black.withOpacity(0.03), blurRadius: 8)
                ],
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      Icon(Icons.calendar_today, size: 16, color: secondary),
                      const SizedBox(width: 8),
                      Text('Oktober 2024',
                          style: TextStyle(
                              fontSize: 12,
                              fontWeight: FontWeight.bold,
                              color: onSurface)),
                    ],
                  ),
                  Container(
                    padding: const EdgeInsets.all(2),
                    decoration: BoxDecoration(
                      color: surfaceContainerLow,
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                          decoration: BoxDecoration(
                            color: surfaceContainerLowest,
                            borderRadius: BorderRadius.circular(16),
                            boxShadow: [
                              BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 4)
                            ],
                          ),
                          child: const Text('Minggu Ini',
                              style: TextStyle(
                                  fontSize: 10, fontWeight: FontWeight.bold)),
                        ),
                        Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 12),
                          child: Text('Bulan',
                              style: TextStyle(
                                  fontSize: 10, color: secondary)),
                        ),
                      ],
                    ),
                  )
                ],
              ),
            ),
            const SizedBox(height: 20),

            // 2. STRIP KALENDER MINGGUAN HORIZONTAL
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('Pekan Ke-4 (21 - 27 Okt)',
                    style: TextStyle(fontSize: 12, color: onSurfaceVariant)),
                Row(
                  children: [
                    Container(
                      width: 6,
                      height: 6,
                      decoration: BoxDecoration(color: primary, shape: BoxShape.circle),
                    ),
                    const SizedBox(width: 4),
                    Text('5 Hari Kerja',
                        style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: primary)),
                  ],
                ),
              ],
            ),
            const SizedBox(height: 8),
            Row(
              children: List.generate(_weekDays.length, (index) {
                final item = _weekDays[index];
                bool isSelected = _selectedDayIndex == index;
                bool isOff = item['status'] == 'off';

                return Expanded(
                  child: GestureDetector(
                    onTap: () => setState(() => _selectedDayIndex = index),
                    child: Container(
                      margin: const EdgeInsets.symmetric(horizontal: 2),
                      padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 2),
                      decoration: BoxDecoration(
                        color: isSelected
                            ? primary
                            : isOff
                                ? surfaceContainerLow.withOpacity(0.7)
                                : surfaceContainerLowest,
                        borderRadius: BorderRadius.circular(16),
                        boxShadow: [
                          BoxShadow(
                              color: Colors.black.withOpacity(0.03), blurRadius: 6)
                        ],
                      ),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(item['day'],
                              style: TextStyle(
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                  color: isSelected
                                      ? onPrimary.withOpacity(0.8)
                                      : isOff
                                          ? secondary.withOpacity(0.6)
                                          : secondary)),
                          const SizedBox(height: 4),
                          Text(item['date'].toString(),
                              style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                  color: isSelected
                                      ? onPrimary
                                      : isOff
                                          ? secondary.withOpacity(0.6)
                                          : onSurface)),
                          const SizedBox(height: 6),
                          Container(
                            width: 6,
                            height: 6,
                            decoration: BoxDecoration(
                              color: isSelected
                                  ? surfaceContainerLowest
                                  : isOff
                                      ? Colors.transparent
                                      : primary.withOpacity(0.7),
                              shape: BoxShape.circle,
                            ),
                          )
                        ],
                      ),
                    ),
                  ),
                );
              }),
            ),
            const SizedBox(height: 20),

            // 3. KARTU DETAIL SHIFT UTAMA (CARD HERO)
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: surfaceContainerLowest,
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(
                      color: primary.withOpacity(0.06),
                      blurRadius: 24,
                      offset: const Offset(0, 8))
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('HARI INI',
                              style: TextStyle(
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                  color: secondary,
                                  letterSpacing: 1)),
                          Text('Kamis, 24 Oktober 2024',
                              style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                  color: onSurface)),
                        ],
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(
                          color: surfaceContainerHigh,
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: Row(
                          children: [
                            Container(
                              width: 8,
                              height: 8,
                              decoration: BoxDecoration(
                                  color: primary, shape: BoxShape.circle),
                            ),
                            const SizedBox(width: 6),
                            Text('Shift Aktif',
                                style: TextStyle(
                                    fontSize: 10,
                                    fontWeight: FontWeight.bold,
                                    color: primary)),
                          ],
                        ),
                      )
                    ],
                  ),
                  const SizedBox(height: 16),

                  // Box Info Shift
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: surfaceContainerLow,
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Container(
                              width: 40,
                              height: 40,
                              decoration: BoxDecoration(
                                color: surfaceContainerLowest,
                                borderRadius: BorderRadius.circular(12),
                              ),
                              child: Icon(Icons.local_cafe, color: primary, size: 20),
                            ),
                            const SizedBox(width: 12),
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('Shift Pagi',
                                    style: TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                        color: onSurface)),
                                Text('Opening Floor & Barista Lead',
                                    style: TextStyle(
                                        fontSize: 12, color: secondary)),
                              ],
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.baseline,
                          textBaseline: TextBaseline.alphabetic,
                          children: [
                            Text('08.00',
                                style: TextStyle(
                                    fontSize: 28,
                                    fontWeight: FontWeight.bold,
                                    color: primary)),
                            Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 6),
                              child: Text('-',
                                  style: TextStyle(
                                      fontSize: 18, color: secondary)),
                            ),
                            Text('16.00',
                                style: TextStyle(
                                    fontSize: 28,
                                    fontWeight: FontWeight.bold,
                                    color: primary)),
                            const SizedBox(width: 4),
                            Text('WIB',
                                style: TextStyle(
                                    fontSize: 12,
                                    fontWeight: FontWeight.bold,
                                    color: secondary)),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            Icon(Icons.storefront, color: primary, size: 16),
                            const SizedBox(width: 6),
                            Text('Cak Kebo - Outlet Senopati • Bar Station 01',
                                style: TextStyle(
                                    fontSize: 12,
                                    fontWeight: FontWeight.bold,
                                    color: onSurfaceVariant)),
                          ],
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Rekan Shift Satu Tim
                  Text('REKAN SHIFT SEJADWAL (2)',
                      style: TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                          color: secondary,
                          letterSpacing: 1)),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Expanded(
                        child: Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: surfaceContainerLow,
                            borderRadius: BorderRadius.circular(16),
                          ),
                          child: Row(
                            children: [
                              Container(
                                width: 28,
                                height: 28,
                                decoration: BoxDecoration(
                                  color: surfaceContainerHigh,
                                  shape: BoxShape.circle,
                                ),
                                alignment: Alignment.center,
                                child: Text('BP',
                                    style: TextStyle(
                                        fontSize: 10,
                                        fontWeight: FontWeight.bold,
                                        color: primary)),
                              ),
                              const SizedBox(width: 8),
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text('Bagas P.',
                                      style: TextStyle(
                                          fontSize: 12,
                                          fontWeight: FontWeight.bold,
                                          color: onSurface)),
                                  Text('Kasir',
                                      style: TextStyle(
                                          fontSize: 10, color: secondary)),
                                ],
                              )
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Container(
                          padding: const EdgeInsets.all(8),
                          decoration: BoxDecoration(
                            color: surfaceContainerLow,
                            borderRadius: BorderRadius.circular(16),
                          ),
                          child: Row(
                            children: [
                              Container(
                                width: 28,
                                height: 28,
                                decoration: BoxDecoration(
                                  color: secondaryContainer,
                                  shape: BoxShape.circle,
                                ),
                                alignment: Alignment.center,
                                child: Text('RK',
                                    style: TextStyle(
                                        fontSize: 10,
                                        fontWeight: FontWeight.bold,
                                        color: onSecondaryContainer)),
                              ),
                              const SizedBox(width: 8),
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text('Rina K.',
                                      style: TextStyle(
                                          fontSize: 12,
                                          fontWeight: FontWeight.bold,
                                          color: onSurface)),
                                  Text('Kitchen',
                                      style: TextStyle(
                                          fontSize: 10, color: secondary)),
                                ],
                              )
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),

                  // Tombol Aksi Tukar Shift
                  ElevatedButton(
                    onPressed: () => _showSwapModal(context),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: primary,
                      foregroundColor: onPrimary,
                      elevation: 2,
                      minimumSize: const Size(double.infinity, 52),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16),
                      ),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: const [
                        Icon(Icons.swap_horizontal_circle, size: 20),
                        SizedBox(width: 8),
                        Text('Ajukan Pergantian Shift',
                            style: TextStyle(
                                fontSize: 14, fontWeight: FontWeight.bold)),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // 4. SHIFT MENDATANG
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text('Shift Mendatang',
                    style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        color: onSurface)),
                Text('Pekan Ini', style: TextStyle(fontSize: 12, color: secondary)),
              ],
            ),
            const SizedBox(height: 12),

            // List Shift Mendatang
            Column(
              children: [
                // Jumat
                _buildUpcomingShiftCard('Jum', '25', 'Shift Siang (Closing)', '14.00 - 22.00 WIB • Bar Station', false),
                const SizedBox(height: 8),
                // Sabtu (Off)
                _buildUpcomingShiftCard('Sab', '26', 'Hari Libur Mingguan', 'Day Off Terjadwal', true),
                const SizedBox(height: 8),
                // Minggu
                _buildUpcomingShiftCard('Min', '27', 'Shift Pagi (Weekend)', '07.30 - 15.30 WIB • Cashier Lead', false),
              ],
            ),
            const SizedBox(height: 24),
          ],
        ),
      ),

      // ==========================================
      // BOTTOM NAVIGATION BAR
      // ==========================================
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: 1, // Aktif di menu Jadwal (index ke-1)
        onTap: (index) {
          if (index == 0) {
            Navigator.pushReplacementNamed(context, '/home');
          } else if (index == 2) {
            Navigator.pushReplacementNamed(context, '/kasir');
          } else if (index == 3) {
            Navigator.pushReplacementNamed(context, '/profil');
          }
        },
        type: BottomNavigationBarType.fixed,
        backgroundColor: surface.withOpacity(0.9),
        selectedItemColor: primary,
        unselectedItemColor: onSurfaceVariant,
        selectedLabelStyle:
            const TextStyle(fontWeight: FontWeight.bold, fontSize: 10),
        unselectedLabelStyle: const TextStyle(fontSize: 10),
        items: const [
          BottomNavigationBarItem(
            icon: Icon(Icons.schedule),
            label: 'Absensi',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.calendar_month),
            label: 'Jadwal',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.point_of_sale),
            label: 'POS',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.badge),
            label: 'Profil',
          ),
        ],
      ),
    );
  }

  Widget _buildUpcomingShiftCard(
      String day, String date, String title, String subtitle, bool isOff) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: surfaceContainerLowest,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 8)
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: [
              Container(
                width: 44,
                height: 44,
                decoration: BoxDecoration(
                  color: isOff ? surfaceContainerHigh.withOpacity(0.6) : surfaceContainerLow,
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text(day,
                        style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: isOff ? secondary.withOpacity(0.6) : primary)),
                    Text(date,
                        style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                            color: isOff ? secondary.withOpacity(0.6) : onSurface,
                            height: 1)),
                  ],
                ),
              ),
              const SizedBox(width: 14),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(title,
                      style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: onSurface)),
                  const SizedBox(height: 2),
                  Text(subtitle,
                      style: TextStyle(fontSize: 12, color: secondary)),
                ],
              ),
            ],
          ),
          isOff
              ? Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: surfaceContainerLow,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Text('Off',
                      style: TextStyle(
                          fontSize: 10,
                          fontWeight: FontWeight.bold,
                          color: secondary)),
                )
              : Container(
                  width: 32,
                  height: 32,
                  decoration: BoxDecoration(
                    color: surfaceContainerLow,
                    shape: BoxShape.circle,
                  ),
                  child: Icon(Icons.chevron_right, color: secondary, size: 18),
                )
        ],
      ),
    );
  }
}