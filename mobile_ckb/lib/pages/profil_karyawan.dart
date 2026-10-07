import 'package:flutter/material.dart';

class ProfilScreen extends StatelessWidget {
  const ProfilScreen({super.key});

  @override
  Widget build(BuildContext context) {
    // Konstanta Warna Tailwind
    final Color primary = const Color(0xFF006194);
    final Color primaryFixed = const Color(0xFFCCE5FF);
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
    final Color errorContainer = const Color(0xFFFFDAD6);
    final Color onErrorContainer = const Color(0xFF93000A);
    final Color outline = const Color(0xFF707881);
    final Color outlineVariant = const Color(0xFFBFC7D2);
    final Color onSecondaryFixedVariant = const Color(0xFF1E4C5F);

    // Fungsi Konfirmasi Logout
    void handleLogout() {
      showDialog(
        context: context,
        builder: (BuildContext context) {
          return AlertDialog(
            backgroundColor: surfaceContainerLowest,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(16),
            ),
            title: Text(
              'Keluar Akun?',
              style: TextStyle(
                  fontWeight: FontWeight.bold, color: onSurface, fontSize: 18),
            ),
            content: Text(
              'Apakah Anda yakin ingin keluar dari sesi kasir Alisa Yasmin?',
              style: TextStyle(color: onSurfaceVariant, fontSize: 14),
            ),
            actions: [
              TextButton(
                child: Text('Batal',
                    style: TextStyle(
                        color: onSurfaceVariant, fontWeight: FontWeight.bold)),
                onPressed: () => Navigator.of(context).pop(),
              ),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: onErrorContainer,
                  foregroundColor: Colors.white,
                  elevation: 0,
                ),
                onPressed: () {
                  // Tutup Dialog
                  Navigator.of(context).pop();
                  // Navigasi Kembali ke halaman Login (Buang semua tumpukan layar)
                  Navigator.pushNamedAndRemoveUntil(
                      context, '/login', (route) => false);
                },
                child: const Text('Ya, Keluar',
                    style: TextStyle(fontWeight: FontWeight.bold)),
              ),
            ],
          );
        },
      );
    }

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
              child: Icon(Icons.storefront, color: primary, size: 20),
            ),
            const SizedBox(width: 12),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'CAK KEBO',
                  style: TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.bold,
                      letterSpacing: 1.5,
                      color: secondary),
                ),
                Text(
                  'Profil Karyawan',
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
          children: [
            // 1. KARTU PROFIL UTAMA (FOTO & NAMA)
            Column(
              children: [
                Stack(
                  alignment: Alignment.bottomRight,
                  children: [
                    Container(
                      width: 96,
                      height: 96,
                      padding: const EdgeInsets.all(4),
                      decoration: BoxDecoration(
                        color: surfaceContainerHigh,
                        shape: BoxShape.circle,
                      ),
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(50),
                        child: Image.network(
                          'https://lh3.googleusercontent.com/aida-public/AB6AXuD25xhPa6GD1kUMzQ8aEYSGAWIwVg_HRLHBLxyYRsKuT8Z9i6JJbBitqvJza_ZfHk_mrtlhlJ6_1PkcIOPUnhtI78lTgPrC5UCPrEnLTtQEMfXAaPhQ4wm7eUKX_y9GajAfcVqCBbZgHHashittNhMVetUUIMPPauHr_xJ5isXZSaHz5htSSu2sLKJdWO4yUS3IhSCJYHXil38SBTTGGgbLMpqsBVeYTEafgpPVPGhI1eSC38NxJHmwPA',
                          fit: BoxFit.cover,
                        ),
                      ),
                    ),
                    // Indikator Online (Titik Hijau/Primary)
                    Container(
                      width: 16,
                      height: 16,
                      margin: const EdgeInsets.only(right: 4, bottom: 4),
                      decoration: BoxDecoration(
                        color: surfaceContainerLowest,
                        shape: BoxShape.circle,
                      ),
                      alignment: Alignment.center,
                      child: Container(
                        width: 10,
                        height: 10,
                        decoration: BoxDecoration(
                          color: primary,
                          shape: BoxShape.circle,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Text(
                  'Alisa Yasmin',
                  style: TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.bold,
                      color: onSurface),
                ),
                const SizedBox(height: 4),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.badge, size: 15, color: outline),
                    const SizedBox(width: 6),
                    Text(
                      'ID Karyawan CK-88219',
                      style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          color: onSurfaceVariant),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                  decoration: BoxDecoration(
                    color: secondaryContainer.withOpacity(0.4),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Container(
                        width: 6,
                        height: 6,
                        decoration: BoxDecoration(
                            color: primary, shape: BoxShape.circle),
                      ),
                      const SizedBox(width: 6),
                      Text(
                        'Kasir • Shift Pagi',
                        style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            color: onSecondaryFixedVariant),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 24),

            // 2. KARTU OPERASIONAL HARI INI
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: surfaceContainerLowest,
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                      color: Colors.black.withOpacity(0.02),
                      blurRadius: 10,
                      offset: const Offset(0, 4))
                ],
              ),
              child: Column(
                children: [
                  // Judul Kartu
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          Icon(Icons.assignment_turned_in,
                              color: primary, size: 20),
                          const SizedBox(width: 8),
                          Text(
                            'Operasional Hari Ini',
                            style: TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.bold,
                                color: onSurface),
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
                        child: Text(
                          'Kamis, 8 Okt 2026',
                          style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: onSurfaceVariant),
                        ),
                      )
                    ],
                  ),
                  const SizedBox(height: 16),

                  // 3 Grid Status Ringkasan
                  Row(
                    children: [
                      Expanded(
                        child: Container(
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          decoration: BoxDecoration(
                            color: surfaceContainerLow.withOpacity(0.6),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Column(
                            children: [
                              Text('Status',
                                  style: TextStyle(
                                      fontSize: 10,
                                      fontWeight: FontWeight.bold,
                                      color: onSurfaceVariant)),
                              const SizedBox(height: 4),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Container(
                                    width: 8,
                                    height: 8,
                                    decoration: BoxDecoration(
                                        color: primary, shape: BoxShape.circle),
                                  ),
                                  const SizedBox(width: 4),
                                  Text('Absen',
                                      style: TextStyle(
                                          fontSize: 12,
                                          fontWeight: FontWeight.bold,
                                          color: onSurface)),
                                ],
                              ),
                              const SizedBox(height: 2),
                              Text('Terverifikasi',
                                  style: TextStyle(
                                      fontSize: 10,
                                      fontWeight: FontWeight.bold,
                                      color: primary)),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Container(
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          decoration: BoxDecoration(
                            color: surfaceContainerLow.withOpacity(0.6),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Column(
                            children: [
                              Text('Jam Masuk',
                                  style: TextStyle(
                                      fontSize: 10,
                                      fontWeight: FontWeight.bold,
                                      color: onSurfaceVariant)),
                              const SizedBox(height: 4),
                              Text('07:55',
                                  style: TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.bold,
                                      color: onSurface)),
                              const SizedBox(height: 2),
                              Text('Tepat Waktu',
                                  style: TextStyle(
                                      fontSize: 10,
                                      fontWeight: FontWeight.bold,
                                      color: secondary)),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Container(
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          decoration: BoxDecoration(
                            color: surfaceContainerLow.withOpacity(0.6),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Column(
                            children: [
                              Text('Jadwal',
                                  style: TextStyle(
                                      fontSize: 10,
                                      fontWeight: FontWeight.bold,
                                      color: onSurfaceVariant)),
                              const SizedBox(height: 4),
                              Text('08:00 - 16',
                                  style: TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.bold,
                                      color: onSurface)),
                              const SizedBox(height: 2),
                              Text('Senopati',
                                  style: TextStyle(
                                      fontSize: 10,
                                      fontWeight: FontWeight.bold,
                                      color: onSurfaceVariant)),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),

                  // Progress Bar Durasi Kerja
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: surfaceContainerLow.withOpacity(0.4),
                      borderRadius: BorderRadius.circular(12),
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
                                color: surfaceContainerHighest,
                                shape: BoxShape.circle,
                              ),
                              child: Icon(Icons.schedule,
                                  color: primary, size: 16),
                            ),
                            const SizedBox(width: 8),
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('Durasi Kerja Aktif',
                                    style: TextStyle(
                                        fontSize: 10,
                                        fontWeight: FontWeight.bold,
                                        color: onSurface)),
                                Text('5 Jam 42 Menit berlalu',
                                    style: TextStyle(
                                        fontSize: 12,
                                        color: onSurfaceVariant)),
                              ],
                            ),
                          ],
                        ),
                        // Mini Progress Bar manual menggunakan SizedBox
                        Container(
                          width: 64,
                          height: 8,
                          decoration: BoxDecoration(
                            color: surfaceContainerHighest,
                            borderRadius: BorderRadius.circular(4),
                          ),
                          alignment: Alignment.centerLeft,
                          child: Container(
                            width: 64 * 0.71, // 71% Progress
                            height: 8,
                            decoration: BoxDecoration(
                              color: primary,
                              borderRadius: BorderRadius.circular(4),
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

            // 3. DAFTAR MENU (Riwayat Absensi dll)
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: surfaceContainerLowest,
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                      color: Colors.black.withOpacity(0.02),
                      blurRadius: 10,
                      offset: const Offset(0, 4))
                ],
              ),
              child: Column(
                children: [
                  // Menu 1
                  ListTile(
                    onTap: () {},
                    contentPadding:
                        const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    leading: Container(
                      width: 40,
                      height: 40,
                      decoration: BoxDecoration(
                        color: surfaceContainerHigh.withOpacity(0.7),
                        shape: BoxShape.circle,
                      ),
                      child: Icon(Icons.calendar_month,
                          color: primary, size: 20),
                    ),
                    title: Text(
                      'Riwayat Absensi',
                      style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: onSurface),
                    ),
                    subtitle: Text(
                      'Rekap shift dan jam lembur',
                      style: TextStyle(fontSize: 12, color: onSurfaceVariant),
                    ),
                    trailing: Icon(Icons.chevron_right, color: outline),
                  ),
                  Divider(color: surfaceContainer, height: 1),
                  // Menu 2
                  ListTile(
                    onTap: () {},
                    contentPadding:
                        const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    leading: Container(
                      width: 40,
                      height: 40,
                      decoration: BoxDecoration(
                        color: surfaceContainerHigh.withOpacity(0.7),
                        shape: BoxShape.circle,
                      ),
                      child: Icon(Icons.support_agent,
                          color: primary, size: 20),
                    ),
                    title: Text(
                      'Bantuan Operasional',
                      style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: onSurface),
                    ),
                    subtitle: Text(
                      'Kontak Manager Outlet & panduan',
                      style: TextStyle(fontSize: 12, color: onSurfaceVariant),
                    ),
                    trailing: Icon(Icons.chevron_right, color: outline),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),

            // 4. QUOTE PENYEMANGAT
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: primaryFixed.withOpacity(0.25),
                borderRadius: BorderRadius.circular(16),
              ),
              child: Row(
                children: [
                  Container(
                    width: 32,
                    height: 32,
                    decoration: BoxDecoration(
                        color: surfaceContainerLowest,
                        shape: BoxShape.circle),
                    child: Icon(Icons.local_cafe, color: primary, size: 18),
                  ),
                  const SizedBox(width: 12),
                  Text(
                    '"semangat yh."',
                    style: TextStyle(
                        fontSize: 12,
                        fontStyle: FontStyle.italic,
                        color: onSurfaceVariant),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // 5. TOMBOL KELUAR & VERSI
            ElevatedButton(
              onPressed: handleLogout,
              style: ElevatedButton.styleFrom(
                backgroundColor: errorContainer.withOpacity(0.6),
                foregroundColor: onErrorContainer,
                elevation: 0,
                minimumSize: const Size(double.infinity, 50),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: const [
                  Icon(Icons.logout, size: 19),
                  SizedBox(width: 8),
                  Text(
                    'Keluar Akun',
                    style: TextStyle(
                        fontSize: 14, fontWeight: FontWeight.bold),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
            Column(
              children: [
                Text(
                  'Versi 2.4.0 • Cak Kebo POS & Attendance',
                  style: TextStyle(
                      fontSize: 10,
                      fontWeight: FontWeight.bold,
                      letterSpacing: 1.0,
                      color: outline),
                ),
                const SizedBox(height: 4),
                Text(
                  'Koneksi Server: Terhubung (Outlet Senopati 01)',
                  style: TextStyle(fontSize: 10, color: outlineVariant),
                ),
              ],
            ),
            const SizedBox(height: 16),
          ],
        ),
      ),

      // ==========================================
      // BOTTOM NAVIGATION BAR
      // ==========================================
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: 3, // Aktif di menu Profil (index ke-3)
        onTap: (index) {
          if (index == 0) {
            // Kembali ke Home menggunakan route
            Navigator.pushReplacementNamed(context, '/home');
          } else if (index == 1) {
            Navigator.pushReplacementNamed(context, '/jadwal');
          } else if (index == 2) {
            Navigator.pushReplacementNamed(context, '/kasir');
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
            icon: Icon(Icons.timer),
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