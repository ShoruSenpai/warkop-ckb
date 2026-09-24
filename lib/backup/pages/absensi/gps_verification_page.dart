import 'dart:async';
import 'package:flutter/material.dart';

class AttendancePage extends StatefulWidget {
  const AttendancePage({super.key});

  @override
  State<AttendancePage> createState() => _AttendancePageState();
}

class _AttendancePageState extends State<AttendancePage> {
  bool isScanning = false;

  String statusTitle = 'Sinyal GPS Terkunci';
  String statusChip = 'Terhubung';
  String distance = '12m (Dalam Radius 50m)';

  // Proses pindai ulang GPS
  void scanGPS() {
    setState(() {
      isScanning = true;
      statusTitle = 'Mencari Satelit...';
      statusChip = 'Memindai...';
    });

    Timer(const Duration(milliseconds: 1200), () {
      if (!mounted) return;

      setState(() {
        isScanning = false;
        statusTitle = 'Lokasi Terverifikasi';
        statusChip = 'Terverifikasi';
        distance = '9 meter (Valid)';
      });
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF7F9FB),

      // ================= HEADER =================
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 1,
        title: Row(
          children: [
            const Text(
              'Cak Kebo',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w600,
              ),
            ),

            const SizedBox(width: 5),

            const Text(
              '•',
              style: TextStyle(color: Colors.grey),
            ),

            const SizedBox(width: 5),

            const Text(
              'Absensi',
              style: TextStyle(
                fontSize: 12,
                color: Colors.grey,
              ),
            ),

            const SizedBox(width: 8),

            // Online
            Container(
              padding: const EdgeInsets.symmetric(
                horizontal: 6,
                vertical: 3,
              ),
              decoration: BoxDecoration(
                color: const Color(0xFFECEEF0),
                borderRadius: BorderRadius.circular(4),
              ),
              child: const Row(
                children: [
                  Icon(
                    Icons.circle,
                    size: 7,
                    color: Color(0xFF10B981),
                  ),
                  SizedBox(width: 4),
                  Text(
                    'Online',
                    style: TextStyle(
                      fontSize: 11,
                      color: Colors.grey,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),

        actions: [
          Container(
            margin: const EdgeInsets.only(right: 16),
            width: 32,
            height: 32,
            decoration: const BoxDecoration(
              color: Color(0xFF006C49),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.person,
              color: Colors.white,
              size: 18,
            ),
          ),
        ],
      ),

      // ================= BODY =================
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.fromLTRB(
            16,
            20,
            16,
            90,
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [

              // ================= JUDUL =================
              const Text(
                'Verifikasi Lokasi Kerja',
                style: TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.w600,
                  color: Color(0xFF191C1E),
                ),
              ),

              const SizedBox(height: 16),

              // ================= RADAR =================
              Container(
                width: double.infinity,
                height: 192,
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Center(
                  child: Stack(
                    alignment: Alignment.center,
                    children: [

                      // Lingkaran luar
                      Container(
                        width: 192,
                        height: 192,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: const Color(0xFF006C49)
                              .withOpacity(0.05),
                        ),
                      ),

                      // Lingkaran tengah
                      Container(
                        width: 144,
                        height: 144,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: const Color(0xFF006C49)
                              .withOpacity(0.10),
                        ),
                      ),

                      // Lingkaran dalam
                      Container(
                        width: 96,
                        height: 96,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: const Color(0xFF006C49)
                              .withOpacity(0.20),
                        ),
                      ),

                      // Icon Cak Kebo
                      Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Container(
                            width: 44,
                            height: 44,
                            decoration: const BoxDecoration(
                              color: Color(0xFF006C49),
                              shape: BoxShape.circle,
                            ),
                            child: const Icon(
                              Icons.storefront,
                              color: Colors.white,
                              size: 22,
                            ),
                          ),

                          const SizedBox(height: 4),

                          Container(
                            padding:
                                const EdgeInsets.symmetric(
                              horizontal: 8,
                              vertical: 3,
                            ),
                            decoration: BoxDecoration(
                              color: const Color(0xFFECEEF0),
                              borderRadius:
                                  BorderRadius.circular(20),
                            ),
                            child: const Text(
                              'Cak Kebo',
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),

              const SizedBox(height: 16),

              // ================= GPS CARD =================
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Column(
                  children: [

                    // Status GPS
                    Row(
                      children: [

                        Container(
                          width: 32,
                          height: 32,
                          decoration: BoxDecoration(
                            color: const Color(0xFF006C49)
                                .withOpacity(0.10),
                            shape: BoxShape.circle,
                          ),
                          child: isScanning
                              ? const SizedBox(
                                  width: 18,
                                  height: 18,
                                  child:
                                      CircularProgressIndicator(
                                    strokeWidth: 2,
                                    color: Color(0xFF006C49),
                                  ),
                                )
                              : const Icon(
                                  Icons.near_me,
                                  size: 18,
                                  color: Color(0xFF006C49),
                                ),
                        ),

                        const SizedBox(width: 10),

                        Expanded(
                          child: Column(
                            crossAxisAlignment:
                                CrossAxisAlignment.start,
                            children: [
                              Text(
                                statusTitle,
                                style: const TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),

                              const SizedBox(height: 2),

                              const Text(
                                'Akurasi tinggi (±3.8m)',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.grey,
                                ),
                              ),
                            ],
                          ),
                        ),

                        // Status chip
                        Container(
                          padding:
                              const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 4,
                          ),
                          decoration: BoxDecoration(
                            color: isScanning
                                ? const Color(0xFFECEEF0)
                                : const Color(0xFF006C49)
                                    .withOpacity(0.10),
                            borderRadius:
                                BorderRadius.circular(5),
                          ),
                          child: Text(
                            statusChip,
                            style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w600,
                              color: isScanning
                                  ? Colors.grey
                                  : const Color(0xFF006C49),
                            ),
                          ),
                        ),
                      ],
                    ),

                    const Divider(height: 24),

                    // Target Outlet
                    Row(
                      mainAxisAlignment:
                          MainAxisAlignment.spaceBetween,
                      children: [
                        const Row(
                          children: [
                            Icon(
                              Icons.store,
                              size: 16,
                              color: Colors.grey,
                            ),
                            SizedBox(width: 4),
                            Text(
                              'Target Outlet',
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.grey,
                              ),
                            ),
                          ],
                        ),

                        const Text(
                          'Cabang Utama',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 12),

                    // Jarak Outlet
                    Row(
                      mainAxisAlignment:
                          MainAxisAlignment.spaceBetween,
                      children: [
                        const Row(
                          children: [
                            Icon(
                              Icons.radar,
                              size: 16,
                              color: Colors.grey,
                            ),
                            SizedBox(width: 4),
                            Text(
                              'Jarak ke Outlet',
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.grey,
                              ),
                            ),
                          ],
                        ),

                        Text(
                          distance,
                          style: const TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                            color: Color(0xFF006C49),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 16),

              // ================= INFORMASI =================
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: const Color(0xFFF2F4F6),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: const Row(
                  crossAxisAlignment:
                      CrossAxisAlignment.start,
                  children: [
                    Icon(
                      Icons.info_outline,
                      size: 18,
                      color: Color(0xFF6C7A71),
                    ),

                    SizedBox(width: 8),

                    Expanded(
                      child: Text(
                        'Pastikan GPS aktif dengan akurasi '
                        'tinggi di pengaturan perangkat.',
                        style: TextStyle(
                          fontSize: 12,
                          color: Color(0xFF6C7A71),
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 24),

              // ================= LANJUT =================
              SizedBox(
                width: double.infinity,
                height: 44,
                child: ElevatedButton(
                  onPressed: () {
                    // Nanti diarahkan ke face_verification.dart
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor:
                        const Color(0xFF006C49),
                    foregroundColor: Colors.white,
                    elevation: 0,
                    shape: RoundedRectangleBorder(
                      borderRadius:
                          BorderRadius.circular(12),
                    ),
                  ),
                  child: const Row(
                    mainAxisAlignment:
                        MainAxisAlignment.center,
                    children: [
                      Text(
                        'Lanjut: Verifikasi Wajah',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      SizedBox(width: 5),
                      Icon(
                        Icons.arrow_forward,
                        size: 18,
                      ),
                    ],
                  ),
                ),
              ),

              const SizedBox(height: 8),

              // ================= PINDAI ULANG =================
              SizedBox(
                width: double.infinity,
                height: 44,
                child: ElevatedButton(
                  onPressed: isScanning
                      ? null
                      : scanGPS,
                  style: ElevatedButton.styleFrom(
                    backgroundColor:
                        const Color(0xFFF2F4F6),
                    foregroundColor:
                        const Color(0xFF191C1E),
                    elevation: 0,
                    shape: RoundedRectangleBorder(
                      borderRadius:
                          BorderRadius.circular(12),
                    ),
                  ),
                  child: Row(
                    mainAxisAlignment:
                        MainAxisAlignment.center,
                    children: [
                      const Icon(
                        Icons.refresh,
                        size: 18,
                      ),
                      const SizedBox(width: 5),
                      Text(
                        isScanning
                            ? 'Memindai GPS...'
                            : 'Pindai Ulang Sinyal GPS',
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ),
              ),

              const SizedBox(height: 8),

              // ================= BATAL =================
              Center(
                child: TextButton(
                  onPressed: () {
                    Navigator.pop(context);
                  },
                  child: const Text(
                    'Batal / Kembali ke Beranda Shift',
                    style: TextStyle(
                      fontSize: 12,
                      color: Color(0xFF6C7A71),
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),

      // ================= BOTTOM NAVIGATION =================
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: 0,
        type: BottomNavigationBarType.fixed,
        selectedItemColor: const Color(0xFF006C49),
        unselectedItemColor: const Color(0xFF6C7A71),
        backgroundColor: Colors.white,
        items: const [
          BottomNavigationBarItem(
            icon: Icon(Icons.how_to_reg),
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
            icon: Icon(Icons.account_circle),
            label: 'Profil',
          ),
        ],
      ),
    );
  }
}