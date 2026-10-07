import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

class AbsenSuksesScreen extends StatelessWidget {
  const AbsenSuksesScreen({super.key});

  @override
  Widget build(BuildContext context) {
  
    final now = DateTime.now();
    final timeString = DateFormat('HH:mm:ss').format(now);
    final dateString = DateFormat('EEEE, dd MMM yyyy', 'id_ID').format(now);

    final Color primary = const Color(0xFF006194);
    final Color primaryContainer = const Color(0xFF007BB9);
    final Color secondary = const Color(0xFF396477);
    final Color secondaryContainer = const Color(0xFFBAE6FD);
    final Color surface = const Color(0xFFF8F9FF);
    final Color surfaceContainerLowest = const Color(0xFFFFFFFF);
    final Color surfaceContainerLow = const Color(0xFFEFF4FF);
    final Color surfaceContainer = const Color(0xFFE5EEFF);
    final Color onSurface = const Color(0xFF0B1C30);
    final Color onSurfaceVariant = const Color(0xFF3F4850);
    final Color onPrimary = const Color(0xFFFFFFFF);
    final Color onSecondaryContainer = const Color(0xFF3D687C);

    return Scaffold(
      backgroundColor: surface,
      // ==========================================
      // HEADER / APP BAR
      // ==========================================
      appBar: AppBar(
        backgroundColor: surfaceContainerLowest.withOpacity(0.9),
        elevation: 0,
        surfaceTintColor: Colors.transparent,
        automaticallyImplyLeading: false, // Sembunyikan tombol back default
        title: Text(
          'Cak Kebo - Absensi',
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.w800,
            letterSpacing: -0.3,
            color: onSurface,
          ),
        ),
        actions: [
          IconButton(
            icon: Icon(Icons.help_outline, color: onSurfaceVariant),
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
      body: Center(
        child: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
        child: SizedBox(
          width: 440,
          child: Column(
          children: [
            // 1. IKON CENTANG & HEADER (SUCCESS)
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                color: secondaryContainer.withOpacity(0.4),
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(
                    color: Colors.lightBlue.withOpacity(0.12),
                    blurRadius: 25,
                    offset: const Offset(0, 10),
                  )
                ],
              ),
              alignment: Alignment.center,
              child: Container(
                width: 56,
                height: 56,
                decoration: BoxDecoration(
                  color: surfaceContainerLowest,
                  shape: BoxShape.circle,
                  boxShadow: [
                    BoxShadow(
                        color: Colors.black.withOpacity(0.05), blurRadius: 4)
                  ],
                ),
                child: Icon(Icons.check_circle, color: primary, size: 36),
              ),
            ),
            const SizedBox(height: 16),
            Text(
              'Absen Berhasil Dicatat',
              style: TextStyle(
                  fontSize: 26, fontWeight: FontWeight.bold, color: onSurface),
            ),
            const SizedBox(height: 8),
            Text(
              'Data presensi Anda telah diverifikasi dan tersimpan di sistem.',
              textAlign: TextAlign.center,
              style: TextStyle(
                  fontSize: 14, color: onSurfaceVariant, height: 1.4),
            ),
            const SizedBox(height: 24),

            // 2. KARTU DETAIL PRESENSI (Unified Card)
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: surfaceContainerLowest,
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(
                    color: Colors.lightBlue.withOpacity(0.06),
                    blurRadius: 25,
                    offset: const Offset(0, 10),
                  )
                ],
              ),
              child: Column(
                children: [
                  // WAKTU ABSEN & BADGE 
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 16, vertical: 12),
                    decoration: BoxDecoration(
                      color: surfaceContainerLow.withOpacity(0.7),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'WAKTU ABSEN',
                              style: TextStyle(
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                  color: secondary,
                                  letterSpacing: 1.0),
                            ),
                            Row(
                              crossAxisAlignment: CrossAxisAlignment.baseline,
                              textBaseline: TextBaseline.alphabetic,
                              children: [
                                Text(
                                  timeString, // Dinamis: 12:28:45
                                  style: TextStyle(
                                      fontSize: 18,
                                      fontWeight: FontWeight.bold,
                                      color: onSurface),
                                ),
                                const SizedBox(width: 4),
                                Text(
                                  'WIB',
                                  style: TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.bold,
                                      color: secondary),
                                ),
                              ],
                            ),
                          ],
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 12, vertical: 6),
                          decoration: BoxDecoration(
                            color: secondaryContainer.withOpacity(0.6),
                            borderRadius: BorderRadius.circular(20),
                            boxShadow: [
                              BoxShadow(
                                  color: Colors.black.withOpacity(0.02),
                                  blurRadius: 4)
                            ],
                          ),
                          child: Row(
                            children: [
                              Icon(Icons.schedule,
                                  size: 16, color: onSecondaryContainer),
                              const SizedBox(width: 4),
                              Text(
                                'Tepat Waktu',
                                style: TextStyle(
                                    fontSize: 12,
                                    fontWeight: FontWeight.bold,
                                    color: onSecondaryContainer),
                              ),
                            ],
                          ),
                        )
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),

                  // LIST DETAIL INFO
                  // Tanggal
                  Padding(
                    padding: const EdgeInsets.symmetric(vertical: 8),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Icon(Icons.calendar_today,
                                size: 18, color: secondary),
                            const SizedBox(width: 8),
                            Text('Tanggal',
                                style: TextStyle(
                                    fontSize: 12, color: onSurfaceVariant)),
                          ],
                        ),
                        Text(
                          dateString, // Dinamis: Kamis, 24 Okt 2024
                          style: TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.bold,
                              color: onSurface),
                        ),
                      ],
                    ),
                  ),
                  // Karyawan
                  Padding(
                    padding: const EdgeInsets.symmetric(vertical: 8),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Icon(Icons.badge_outlined,
                                size: 18, color: secondary),
                            const SizedBox(width: 8),
                            Text('Karyawan',
                                style: TextStyle(
                                    fontSize: 12, color: onSurfaceVariant)),
                          ],
                        ),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            Text(
                              'Alisa Yasmin',
                              style: TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.bold,
                                  color: onSurface),
                            ),
                            Text(
                              'Barista • Shift Pagi',
                              style: TextStyle(
                                  fontSize: 12, color: secondary),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                  // Lokasi Outlet
                  Padding(
                    padding: const EdgeInsets.symmetric(vertical: 8),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Icon(Icons.store_outlined,
                                size: 18, color: secondary),
                            const SizedBox(width: 8),
                            Text('Lokasi Outlet',
                                style: TextStyle(
                                    fontSize: 12, color: onSurfaceVariant)),
                          ],
                        ),
                        Text(
                          'Warkop Cak Kebo',
                          style: TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.bold,
                              color: onSurface),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 8),

                  // METODE VALIDASI
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: surfaceContainerLow.withOpacity(0.4),
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'METODE VALIDASI',
                          style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: secondary,
                              letterSpacing: 1.0),
                        ),
                        const SizedBox(height: 8),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Row(
                              children: [
                                Icon(Icons.pin_drop,
                                    size: 18, color: primary),
                                const SizedBox(width: 8),
                                Text('GPS Geofence',
                                    style: TextStyle(
                                        fontSize: 12, color: onSurface)),
                              ],
                            ),
                            Text(
                              'Akurat (Radius 12m)',
                              style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  color: primary),
                            ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Row(
                              children: [
                                Icon(Icons.face, size: 18, color: primary),
                                const SizedBox(width: 8),
                                Text('Biometrik Wajah',
                                    style: TextStyle(
                                        fontSize: 12, color: onSurface)),
                              ],
                            ),
                            Text(
                              'Terverifikasi 99.4%',
                              style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  color: primary),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 32),

            // BACK & ID TRANSAKSI
            ElevatedButton(
              
              onPressed: () => Navigator.popUntil(
                  context, ModalRoute.withName('/home')),
              style: ElevatedButton.styleFrom(
                backgroundColor: primary,
                foregroundColor: onPrimary,
                elevation: 4,
                minimumSize: const Size(double.infinity, 56),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16),
                ),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: const [
                  Text(
                    'Kembali ke Beranda',
                    style: TextStyle(
                        fontSize: 14, fontWeight: FontWeight.bold),
                  ),
                  SizedBox(width: 8),
                  Icon(Icons.arrow_forward, size: 20),
                ],
              ),
            ),
            const SizedBox(height: 12),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.verified_user_outlined,
                    size: 14, color: secondary),
                const SizedBox(width: 6),
                RichText(
                  text: TextSpan(
                    text: 'ID Transaksi: ',
                    style: TextStyle(fontSize: 10, color: secondary),
                    children: [
                      TextSpan(
                        text: 'CK-ABS-20241024-0891',
                        style: TextStyle(
                            fontWeight: FontWeight.bold,
                            color: onSurfaceVariant),
                      ),
                      const TextSpan(text: ' • Tersinkron'),
                    ],
                  ),
                ),
              ],
            ),
          ],
        ),
        ),
      ),
    ),

      // ==========================================
      // BOTTOM NAVIGATION BAR
      // ==========================================
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: 0,
        type: BottomNavigationBarType.fixed,
        backgroundColor: surfaceContainerLowest.withOpacity(0.9),
        selectedItemColor: onSurfaceVariant, // Tidak aktif (abu-abu)
        unselectedItemColor: onSurfaceVariant,
        selectedLabelStyle: const TextStyle(fontSize: 10),
        unselectedLabelStyle: const TextStyle(fontSize: 10),
        items: const [
          BottomNavigationBarItem(
            icon: Icon(Icons.fingerprint),
            label: 'Absensi',
          ),
          BottomNavigationBarItem(
            icon: Icon(Icons.calendar_today),
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
}