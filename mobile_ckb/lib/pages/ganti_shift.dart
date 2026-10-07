import 'package:flutter/material.dart';

class ShiftScreen extends StatefulWidget {
  const ShiftScreen({super.key});

  @override
  State<ShiftScreen> createState() => _ShiftScreenState();
}

class _ShiftScreenState extends State<ShiftScreen> {
  // State Filter Tab ('all', 'pending', 'completed')
  String _activeFilter = 'all';

  // State status permintaan masuk ('none', 'accepted', 'declined')
  String _incomingStatus = 'none';

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
  final Color outlineVariant = const Color(0xFFBFC7D1);
  final Color onPrimary = const Color(0xFFFFFFFF);
  final Color onSecondaryContainer = const Color(0xFF3D687C);
  final Color errorContainer = const Color(0xFFFFDAD6);
  final Color onErrorContainer = const Color(0xFF93000A);
  final Color error = const Color(0xFFBA1A1A);

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
        automaticallyImplyLeading: false,
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
            // 1. KEMBALI & TITLE HEADER
            GestureDetector(
              onTap: () => Navigator.pop(context),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.arrow_back, size: 18, color: secondary),
                  const SizedBox(width: 4),
                  Text('Kembali ke Jadwal',
                      style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          color: secondary)),
                ],
              ),
            ),
            const SizedBox(height: 12),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Pergantian Shift',
                        style: TextStyle(
                            fontSize: 26,
                            fontWeight: FontWeight.bold,
                            color: onSurface)),
                    Text('Permintaan & Riwayat Staf',
                        style: TextStyle(fontSize: 14, color: onSurfaceVariant)),
                  ],
                ),
                Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                    color: surfaceContainerLow,
                    shape: BoxShape.circle,
                  ),
                  child: Icon(Icons.help_outline, color: secondary, size: 20),
                ),
              ],
            ),
            const SizedBox(height: 16),

            // Segmented Filter Tabs (Semua, Menunggu, Selesai)
            Container(
              padding: const EdgeInsets.all(4),
              decoration: BoxDecoration(
                color: surfaceContainerLow,
                borderRadius: BorderRadius.circular(20),
              ),
              child: Row(
                children: [
                  _buildTabButton('Semua', 'all'),
                  _buildTabButton('Menunggu', 'pending'),
                  _buildTabButton('Selesai', 'completed'),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // 2. SECTION 1: PERMINTAAN MASUK (Hanya tampil jika filter 'all' atau 'pending')
            if (_activeFilter != 'completed') ...[
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      Text('Permintaan Masuk',
                          style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                              color: onSurface)),
                      const SizedBox(width: 8),
                      Container(
                        width: 8,
                        height: 8,
                        decoration: BoxDecoration(
                            color: primary, shape: BoxShape.circle),
                      ),
                    ],
                  ),
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: secondaryContainer,
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Text('1 Permintaan Baru',
                        style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: onSecondaryContainer)),
                  ),
                ],
              ),
              const SizedBox(height: 12),

              // Kartu Permintaan Masuk
              Container(
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  color: surfaceContainerLowest,
                  borderRadius: BorderRadius.circular(24),
                  boxShadow: [
                    BoxShadow(
                        color: Colors.black.withOpacity(0.03), blurRadius: 10)
                  ],
                ),
                child: Column(
                  children: [
                    // Profil Requester
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Row(
                          children: [
                            CircleAvatar(
                              radius: 24,
                              backgroundImage: const NetworkImage(
                                  'https://lh3.googleusercontent.com/aida-public/AB6AXuA3mm_UjnC6nk5dDZFXiLeixQqOkD0kBo-gx2Cr2LJr8qHFPBKJi5Qd0naSmm4-e98oIUp6voEbs-D5pHYcO54h0q_OwMsB0zsi-CWZcXcqeO_RbOMnEP034rbnffkbQPzexgPU7DfBIi7u8n80SUE6zLI2JgvqONavIAEeKC_J_vkSvavCLkFh4kW5CpRRonRyFbKKLvZiQ6spqg2yNhQP9uhp_fHpTKSgEY8gZLWVeEe_dIBj5_zpQw'),
                            ),
                            const SizedBox(width: 12),
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('Budi Santoso',
                                    style: TextStyle(
                                        fontSize: 14,
                                        fontWeight: FontWeight.bold,
                                        color: onSurface)),
                                Text('Floor Staff & Kasir',
                                    style: TextStyle(
                                        fontSize: 12, color: onSurfaceVariant)),
                              ],
                            ),
                          ],
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: surfaceContainerLow,
                            borderRadius: BorderRadius.circular(16),
                          ),
                          child: Row(
                            children: [
                              Icon(Icons.schedule, size: 12, color: secondary),
                              const SizedBox(width: 4),
                              Text('2 jam lalu',
                                  style: TextStyle(
                                      fontSize: 10, color: secondary)),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),

                    // Detail Proposal Tukar Shift
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
                              Icon(Icons.event_repeat, color: primary, size: 18),
                              const SizedBox(width: 8),
                              Text('Budi meminta Anda menggantikan Shift Malam',
                                  style: TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.bold,
                                      color: onSurface)),
                            ],
                          ),
                          const SizedBox(height: 12),
                          Container(
                            padding: const EdgeInsets.all(10),
                            decoration: BoxDecoration(
                              color: surfaceContainerLowest,
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text('Shift Diajukan:',
                                    style: TextStyle(
                                        fontSize: 12, color: onSurfaceVariant)),
                                Text('Jum, 10 Okt • 15.00 - 23.00 WIB',
                                    style: TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.bold,
                                        color: onSurface)),
                              ],
                            ),
                          ),
                          const SizedBox(height: 6),
                          Container(
                            padding: const EdgeInsets.all(10),
                            decoration: BoxDecoration(
                              color: surfaceContainerLowest,
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Text('Jadwal Anda:',
                                    style: TextStyle(
                                        fontSize: 12, color: onSurfaceVariant)),
                                Text('Shift Pagi (08.00 - 16.00 WIB)',
                                    style: TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.bold,
                                        color: secondary)),
                              ],
                            ),
                          ),
                          const SizedBox(height: 12),
                          Row(
                            children: [
                              Icon(Icons.format_quote,
                                  size: 16, color: outlineVariant),
                              const SizedBox(width: 6),
                              Text('“Izin nenek saya ikut balapan!”',
                                  style: TextStyle(
                                      fontSize: 12,
                                      fontStyle: FontStyle.italic,
                                      color: onSurfaceVariant)),
                            ],
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Tombol Terima / Tolak (atau feedback jika sudah dipilih)
                    if (_incomingStatus == 'none')
                      Row(
                        children: [
                          Expanded(
                            child: OutlinedButton(
                              style: OutlinedButton.styleFrom(
                                backgroundColor: surfaceContainer,
                                foregroundColor: onSurfaceVariant,
                                side: BorderSide.none,
                                padding:
                                    const EdgeInsets.symmetric(vertical: 14),
                                shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(16)),
                              ),
                              onPressed: () => setState(
                                  () => _incomingStatus = 'declined'),
                              child: const Text('Tolak',
                                  style: TextStyle(fontWeight: FontWeight.bold)),
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: primaryContainer,
                                foregroundColor: onPrimary,
                                elevation: 0,
                                padding:
                                    const EdgeInsets.symmetric(vertical: 14),
                                shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(16)),
                              ),
                              onPressed: () => setState(
                                  () => _incomingStatus = 'accepted'),
                              child: const Text('Terima',
                                  style: TextStyle(fontWeight: FontWeight.bold)),
                            ),
                          ),
                        ],
                      )
                    else
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: _incomingStatus == 'accepted'
                              ? secondaryContainer
                              : surfaceContainer,
                          borderRadius: BorderRadius.circular(16),
                        ),
                        alignment: Alignment.center,
                        child: Text(
                          _incomingStatus == 'accepted'
                              ? '✓ Permintaan diterima! Mengirim approval ke Supervisor...'
                              : '✕ Permintaan pergantian shift telah ditolak santun.',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: _incomingStatus == 'accepted'
                                ? onSecondaryContainer
                                : onSurfaceVariant,
                          ),
                        ),
                      ),
                  ],
                ),
              ),
              const SizedBox(height: 24),
            ],

            // 3. SECTION 2: RIWAYAT PENGAJUAN (History Log)
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Row(
                  children: [
                    Text('Riwayat Pengajuan',
                        style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: onSurface)),
                    const SizedBox(width: 6),
                    Text('(3 Total)',
                        style: TextStyle(fontSize: 12, color: onSurfaceVariant)),
                  ],
                ),
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: surfaceContainerLow,
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Row(
                    children: [
                      Icon(Icons.calendar_month, size: 14, color: secondary),
                      const SizedBox(width: 4),
                      Text('Bulan Ini',
                          style: TextStyle(fontSize: 12, color: secondary)),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),

            // Daftar History Card
            Column(
              children: [
                // History 1: Approved (Selesai)
                if (_activeFilter == 'all' || _activeFilter == 'completed')
                  _buildHistoryCard(
                    title: 'Kamis, 24 Okt 2024',
                    subtitle: 'Shift Pagi • 08.00 - 16.00 WIB',
                    status: 'Disetujui',
                    statusColor: secondaryContainer,
                    textColor: onSecondaryContainer,
                    partner: 'Dimas Arya (Barista)',
                    reason: 'Jadwal Ujian Semester',
                    footerText: 'Disetujui oleh SPV Rian pada 22 Okt',
                    icon: Icons.coffee,
                  ),
                if (_activeFilter == 'all' || _activeFilter == 'completed')
                  const SizedBox(height: 12),

                // History 2: Pending (Menunggu)
                if (_activeFilter == 'all' || _activeFilter == 'pending')
                  _buildHistoryCard(
                    title: 'Senin, 28 Okt 2024',
                    subtitle: 'Shift Siang • 12.00 - 20.00 WIB',
                    status: 'Menunggu SPV',
                    statusColor: surfaceContainerHighest,
                    textColor: onSurfaceVariant,
                    partner: 'Menggantikan Siti Sarah',
                    reason: 'Urusan Keluarga',
                    footerText: 'Telah disetujui rekan kerja, menunggu approval outlet lead',
                    icon: Icons.wb_sunny,
                  ),
                if (_activeFilter == 'all' || _activeFilter == 'pending')
                  const SizedBox(height: 12),

                // History 3: Rejected (Selesai)
                if (_activeFilter == 'all' || _activeFilter == 'completed')
                  _buildHistoryCard(
                    title: 'Minggu, 13 Okt 2024',
                    subtitle: 'Shift Pagi • 07.00 - 15.00 WIB',
                    status: 'Ditolak',
                    statusColor: errorContainer,
                    textColor: onErrorContainer,
                    partner: 'Kevin Pratama',
                    reason: 'Kebutuhan mendadak',
                    footerText: 'Slot kuota shift minimum outlet tidak terpenuhi',
                    icon: Icons.wb_twilight,
                    isError: true,
                  ),
              ],
            ),
            const SizedBox(height: 20),

            // 4. BANNER BAWAH: PERLU TUKAR SHIFT LAIN?
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: secondaryContainer.withOpacity(0.4),
                borderRadius: BorderRadius.circular(24),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      Container(
                        width: 40,
                        height: 40,
                        decoration: BoxDecoration(
                          color: surfaceContainerLowest,
                          shape: BoxShape.circle,
                        ),
                        child: Icon(Icons.add_circle, color: primary, size: 22),
                      ),
                      const SizedBox(width: 12),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('Perlu Tukar Shift Lain?',
                              style: TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.bold,
                                  color: onSurface)),
                          Text('Ajukan permohonan ke rekan satu outlet',
                              style: TextStyle(fontSize: 12, color: secondary)),
                        ],
                      ),
                    ],
                  ),
                  ElevatedButton(
                    onPressed: () {},
                    style: ElevatedButton.styleFrom(
                      backgroundColor: primary,
                      foregroundColor: onPrimary,
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12)),
                    ),
                    child: const Text('Ajukan',
                        style: TextStyle(fontWeight: FontWeight.bold)),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),
          ],
        ),
      ),

      // ==========================================
      // BOTTOM NAVIGATION BAR
      // ==========================================
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: 1, // Aktif di menu Jadwal/Shift
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

  // Helper untuk Tombol Tab Filter
  Widget _buildTabButton(String title, String filterKey) {
    bool isActive = _activeFilter == filterKey;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => _activeFilter = filterKey),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 10),
          decoration: BoxDecoration(
            color: isActive ? surfaceContainerLowest : Colors.transparent,
            borderRadius: BorderRadius.circular(16),
            boxShadow: isActive
                ? [BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 4)]
                : [],
          ),
          alignment: Alignment.center,
          child: Text(
            title,
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.bold,
              color: isActive ? primary : onSurfaceVariant,
            ),
          ),
        ),
      ),
    );
  }

  // Helper untuk Kartu Riwayat (History Card)
  Widget _buildHistoryCard({
    required String title,
    required String subtitle,
    required String status,
    required Color statusColor,
    required Color textColor,
    required String partner,
    required String reason,
    required String footerText,
    required IconData icon,
    bool isError = false,
  }) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: surfaceContainerLowest,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.02), blurRadius: 8)
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  Container(
                    width: 40,
                    height: 40,
                    decoration: BoxDecoration(
                      color: surfaceContainerHigh,
                      shape: BoxShape.circle,
                    ),
                    child: Icon(icon, color: primary, size: 20),
                  ),
                  const SizedBox(width: 12),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(title,
                          style: TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.bold,
                              color: onSurface)),
                      Text(subtitle,
                          style: TextStyle(fontSize: 12, color: onSurfaceVariant)),
                    ],
                  ),
                ],
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: statusColor,
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Text(status,
                    style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                        color: textColor)),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: surfaceContainerLow,
              borderRadius: BorderRadius.circular(16),
            ),
            child: Column(
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text('Partner:',
                        style: TextStyle(fontSize: 12, color: onSurfaceVariant)),
                    Text(partner,
                        style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: onSurface)),
                  ],
                ),
                const SizedBox(height: 4),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text('Alasan:',
                        style: TextStyle(fontSize: 12, color: onSurfaceVariant)),
                    Text(reason,
                        style: TextStyle(fontSize: 12, color: onSurface)),
                  ],
                ),
              ],
            ),
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Icon(
                isError ? Icons.error_outline : Icons.verified,
                size: 16,
                color: isError ? error : primary,
              ),
              const SizedBox(width: 6),
              Expanded(
                child: Text(footerText,
                    style: TextStyle(
                      fontSize: 12,
                      color: isError ? error : secondary,
                    )),
              ),
            ],
          ),
        ],
      ),
    );
  }
}