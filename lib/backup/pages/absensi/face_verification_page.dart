import 'dart:async';

import 'package:flutter/material.dart';

class FaceVerificationPage extends StatefulWidget {
  const FaceVerificationPage({super.key});

  @override
  State<FaceVerificationPage> createState() =>
      _FaceVerificationPageState();
}

class _FaceVerificationPageState
    extends State<FaceVerificationPage> {
  Timer? timer;

  String currentTime = '';

  @override
  void initState() {
    super.initState();

    updateTime();

    timer = Timer.periodic(
      const Duration(seconds: 1),
      (timer) {
        updateTime();
      },
    );
  }

  void updateTime() {
    final now = DateTime.now();

    const months = [
      'Jan',
      'Feb',
      'Mar',
      'Apr',
      'Mei',
      'Jun',
      'Jul',
      'Agu',
      'Sep',
      'Okt',
      'Nov',
      'Des',
    ];

    final day =
        now.day.toString().padLeft(2, '0');

    final month = months[now.month - 1];

    final year = now.year;

    final hour =
        now.hour.toString().padLeft(2, '0');

    final minute =
        now.minute.toString().padLeft(2, '0');

    final second =
        now.second.toString().padLeft(2, '0');

    setState(() {
      currentTime =
          '$day $month $year $hour:$minute:$second WIB';
    });
  }

  @override
  void dispose() {
    timer?.cancel();
    super.dispose();
  }

  // Tombol ambil foto
  void takePhoto() {
    // Untuk sementara hanya memberikan feedback.
    // Kamera asli bisa ditambahkan setelah ini.
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Foto kehadiran diambil'),
        duration: Duration(seconds: 1),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF7F9FB),

      // =================================================
      // HEADER
      // =================================================
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 1,
        titleSpacing: 16,

        title: Row(
          children: [
            const Text(
              'Cak Kebo',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: Color(0xFF191C1E),
              ),
            ),

            const SizedBox(width: 5),

            const Text(
              '•',
              style: TextStyle(
                color: Colors.grey,
              ),
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
            margin: const EdgeInsets.only(
              right: 16,
            ),
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

      // =================================================
      // BODY
      // =================================================
      body: SafeArea(
        child: Column(
          children: [

            // ===========================================
            // STATUS 3/3
            // ===========================================
            Padding(
              padding: const EdgeInsets.fromLTRB(
                16,
                8,
                16,
                4,
              ),
              child: Row(
                mainAxisAlignment:
                    MainAxisAlignment.spaceBetween,
                children: [

                  Row(
                    children: [
                      Container(
                        width: 6,
                        height: 6,
                        decoration:
                            const BoxDecoration(
                          color: Color(0xFF006C49),
                          shape: BoxShape.circle,
                        ),
                      ),

                      const SizedBox(width: 6),

                      const Text(
                        'Verifikasi Foto (3/3)',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: Color(0xFF6C7A71),
                        ),
                      ),
                    ],
                  ),

                  // Tombol close
                  IconButton(
                    onPressed: () {
                      Navigator.pop(context);
                    },
                    icon: const Icon(
                      Icons.close,
                      size: 20,
                    ),
                  ),
                ],
              ),
            ),

            // ===========================================
            // PROGRESS BAR
            // ===========================================
            Padding(
              padding: const EdgeInsets.symmetric(
                horizontal: 16,
              ),
              child: Container(
                height: 4,
                decoration: BoxDecoration(
                  color: const Color(0xFFECEEF0),
                  borderRadius:
                      BorderRadius.circular(10),
                ),
                child: Container(
                  width: double.infinity,
                  decoration: BoxDecoration(
                    color: const Color(0xFF006C49),
                    borderRadius:
                        BorderRadius.circular(10),
                  ),
                ),
              ),
            ),

            // ===========================================
            // JUDUL
            // ===========================================
            const Padding(
              padding: EdgeInsets.fromLTRB(
                16,
                8,
                16,
                8,
              ),
              child: Column(
                children: [
                  Text(
                    'Ambil Foto Kehadiran',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w600,
                      color: Color(0xFF191C1E),
                    ),
                  ),

                  SizedBox(height: 4),

                  Text(
                    'Posisikan wajah Anda tepat di dalam bingkai.',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 12,
                      color: Color(0xFF6C7A71),
                    ),
                  ),
                ],
              ),
            ),

            // ===========================================
            // CAMERA VIEW
            // ===========================================
            Expanded(
              child: Padding(
                padding: const EdgeInsets.symmetric(
                  horizontal: 16,
                ),
                child: Container(
                  width: double.infinity,
                  decoration: BoxDecoration(
                    color: const Color(0xFF1E232A),
                    borderRadius:
                        BorderRadius.circular(16),
                  ),
                  child: Stack(
                    children: [

                      // ---------------------------------
                      // AREA WAJAH
                      // ---------------------------------
                      Center(
                        child: Column(
                          mainAxisSize:
                              MainAxisSize.min,
                          children: [

                            Container(
                              width: 128,
                              height: 160,
                              decoration:
                                  BoxDecoration(
                                shape: BoxShape.circle,
                                border: Border.all(
                                  color:
                                      const Color(
                                    0xFFBBCABF,
                                  ),
                                  width: 2,
                                ),
                              ),
                              child: const Column(
                                mainAxisAlignment:
                                    MainAxisAlignment
                                        .center,
                                children: [
                                  Icon(
                                    Icons.face,
                                    size: 48,
                                    color:
                                        Color(0xFFBBCABF),
                                  ),

                                  SizedBox(height: 8),

                                  Text(
                                    'Posisikan Wajah',
                                    textAlign:
                                        TextAlign.center,
                                    style: TextStyle(
                                      fontSize: 11,
                                      color:
                                          Color(
                                        0xFFBBCABF,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),

                      // ---------------------------------
                      // STATUS WAJAH TERDETEKSI
                      // ---------------------------------
                      Positioned(
                        top: 12,
                        left: 12,
                        right: 12,
                        child: Row(
                          mainAxisAlignment:
                              MainAxisAlignment
                                  .spaceBetween,
                          children: [

                            Container(
                              padding:
                                  const EdgeInsets
                                      .symmetric(
                                horizontal: 10,
                                vertical: 6,
                              ),
                              decoration: BoxDecoration(
                                color: const Color(
                                  0xFF1E232A,
                                ).withOpacity(0.85),
                                borderRadius:
                                    BorderRadius
                                        .circular(20),
                              ),
                              child: const Row(
                                children: [
                                  Icon(
                                    Icons.circle,
                                    size: 8,
                                    color: Color(
                                      0xFF10B981,
                                    ),
                                  ),

                                  SizedBox(width: 6),

                                  Text(
                                    'Wajah Terdeteksi',
                                    style: TextStyle(
                                      fontSize: 11,
                                      fontWeight:
                                          FontWeight.w500,
                                      color:
                                          Colors.white,
                                    ),
                                  ),
                                ],
                              ),
                            ),

                            // Jam
                            Container(
                              padding:
                                  const EdgeInsets
                                      .symmetric(
                                horizontal: 8,
                                vertical: 6,
                              ),
                              decoration: BoxDecoration(
                                color: const Color(
                                  0xFF1E232A,
                                ).withOpacity(0.85),
                                borderRadius:
                                    BorderRadius
                                        .circular(20),
                              ),
                              child: Row(
                                children: [
                                  const Icon(
                                    Icons.schedule,
                                    size: 14,
                                    color: Colors.white,
                                  ),

                                  const SizedBox(
                                    width: 4,
                                  ),

                                  Text(
                                    currentTime,
                                    style:
                                        const TextStyle(
                                      fontSize: 10,
                                      color:
                                          Colors.white,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),

                      // ---------------------------------
                      // FRAME WAJAH
                      // ---------------------------------
                      Positioned(
                        left: 24,
                        right: 24,
                        top: 24,
                        bottom: 24,
                        child: Center(
                          child: Container(
                            width: 80,
                            height: 96,
                            decoration:
                                BoxDecoration(
                              shape: BoxShape.circle,
                              border: Border.all(
                                color: const Color(
                                  0xFF4EDEA3,
                                ).withOpacity(0.4),
                              ),
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),

            // ===========================================
            // INFO
            // ===========================================
            Padding(
              padding: const EdgeInsets.fromLTRB(
                16,
                8,
                16,
                0,
              ),
              child: Row(
                mainAxisAlignment:
                    MainAxisAlignment.center,
                children: [
                  const Icon(
                    Icons.verified,
                    size: 15,
                    color: Color(0xFF3C4A42),
                  ),

                  const SizedBox(width: 6),

                  Flexible(
                    child: Text(
                      'Foto terverifikasi geofence Senopati '
                      'dan otomatis dibubuhi tanda waktu resmi.',
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                        fontSize: 11,
                        color: Color(0xFF3C4A42),
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // ===========================================
            // CAMERA CONTROLS
            // ===========================================
            Padding(
              padding: const EdgeInsets.fromLTRB(
                16,
                12,
                16,
                8,
              ),
              child: Column(
                children: [

                  Row(
                    mainAxisAlignment:
                        MainAxisAlignment.spaceBetween,
                    children: [

                      // Balik kamera
                      _cameraButton(
                        icon: Icons.flip_camera_ios,
                        onPressed: () {
                          // Balik kamera
                        },
                      ),

                      // SHUTTER
                      GestureDetector(
                        onTap: takePhoto,
                        child: Container(
                          width: 68,
                          height: 68,
                          padding: const EdgeInsets.all(
                            4,
                          ),
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            border: Border.all(
                              color:
                                  const Color(0xFF006C49),
                              width: 2,
                            ),
                          ),
                          child: Container(
                            decoration:
                                const BoxDecoration(
                              shape: BoxShape.circle,
                              color: Color(0xFF006C49),
                            ),
                            child: const Icon(
                              Icons.photo_camera,
                              size: 24,
                              color: Colors.white,
                            ),
                          ),
                        ),
                      ),

                      // Flash
                      _cameraButton(
                        icon: Icons.flash_auto,
                        onPressed: () {
                          // Flash otomatis
                        },
                      ),
                    ],
                  ),

                  const SizedBox(height: 8),

                  // BATAL
                  TextButton(
                    onPressed: () {
                      Navigator.pop(context);
                    },
                    child: const Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(
                          Icons.arrow_back,
                          size: 15,
                        ),
                        SizedBox(width: 4),
                        Text(
                          'Batal',
                          style: TextStyle(
                            fontSize: 11,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),

      // =================================================
      // BOTTOM NAVIGATION
      // =================================================
      bottomNavigationBar:
          BottomNavigationBar(
        currentIndex: 0,
        type: BottomNavigationBarType.fixed,
        selectedItemColor:
            const Color(0xFF006C49),
        unselectedItemColor:
            const Color(0xFF6C7A71),
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

  // ===================================================
  // CAMERA BUTTON
  // ===================================================

  Widget _cameraButton({
    required IconData icon,
    required VoidCallback onPressed,
  }) {
    return SizedBox(
      width: 44,
      height: 44,
      child: Material(
        color: const Color(0xFFECEEF0),
        shape: const CircleBorder(),
        child: InkWell(
          customBorder: const CircleBorder(),
          onTap: onPressed,
          child: Icon(
            icon,
            size: 20,
            color: const Color(0xFF6C7A71),
          ),
        ),
      ),
    );
  }
}