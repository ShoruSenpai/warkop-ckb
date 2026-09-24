import 'package:flutter/material.dart';

class AttendanceSuccessPage extends StatelessWidget {
  const AttendanceSuccessPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF7F9FB),

      // ================= HEADER =================
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
                color: Color(0xFF6C7A71),
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
                      color: Color(0xFF6C7A71),
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
            12,
            16,
            20,
          ),
          child: Column(
            children: [

              // ================= PROGRESS =================
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  boxShadow: const [
                    BoxShadow(
                      color: Color(0x10000000),
                      blurRadius: 4,
                      offset: Offset(0, 1),
                    ),
                  ],
                ),
                child: Column(
                  children: [

                    Row(
                      children: [
                        Container(
                          padding:
                              const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 2,
                          ),
                          decoration: BoxDecoration(
                            color: const Color(0xFF006C49)
                                .withOpacity(0.10),
                            borderRadius:
                                BorderRadius.circular(4),
                          ),
                          child: const Row(
                            children: [
                              Icon(
                                Icons.circle,
                                size: 6,
                                color:
                                    Color(0xFF10B981),
                              ),
                              SizedBox(width: 4),
                              Text(
                                'Presensi Terverifikasi',
                                style: TextStyle(
                                  fontSize: 11,
                                  fontWeight:
                                      FontWeight.w600,
                                  color:
                                      Color(0xFF006C49),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 8),

                    // Progress 100%
                    Container(
                      width: double.infinity,
                      height: 6,
                      decoration: BoxDecoration(
                        color: const Color(0xFFE6E8EA),
                        borderRadius:
                            BorderRadius.circular(10),
                      ),
                      child: Container(
                        width: double.infinity,
                        decoration: BoxDecoration(
                          color:
                              const Color(0xFF10B981),
                          borderRadius:
                              BorderRadius.circular(10),
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 16),

              // ================= SUCCESS HEADER =================
              const Column(
                children: [

                  // Icon berhasil
                  CircleAvatar(
                    radius: 32,
                    backgroundColor: Color(0x1A006C49),
                    child: Icon(
                      Icons.check_circle,
                      size: 32,
                      color: Color(0xFF006C49),
                    ),
                  ),

                  SizedBox(height: 8),

                  Text(
                    'Absensi Berhasil Dicatat',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.w600,
                      color: Color(0xFF191C1E),
                    ),
                  ),

                  SizedBox(height: 4),

                  Text(
                    'Data kehadiran Anda telah tercatat dan '
                    'tersinkronisasi ke server operasional.',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 12,
                      color: Color(0xFF565E74),
                    ),
                  ),

                  SizedBox(height: 8),

                  Row(
                    mainAxisAlignment:
                        MainAxisAlignment.center,
                    children: [
                      Icon(
                        Icons.cloud_done,
                        size: 14,
                        color: Color(0xFF006C49),
                      ),

                      SizedBox(width: 4),

                      Flexible(
                        child: Text(
                          'Tersinkronisasi Online • '
                          '#ATT-20260922-042',
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight:
                                FontWeight.w600,
                            color: Color(0xFF006C49),
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),

              const SizedBox(height: 16),

              // ================= DATA PRESENSI =================
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(12),
                  boxShadow: const [
                    BoxShadow(
                      color: Color(0x10000000),
                      blurRadius: 4,
                      offset: Offset(0, 1),
                    ),
                  ],
                ),
                child: Column(
                  children: [

                    // DATA KARYAWAN
                    Row(
                      children: [

                        Container(
                          width: 56,
                          height: 56,
                          decoration: BoxDecoration(
                            color:
                                const Color(0xFFECEEF0),
                            borderRadius:
                                BorderRadius.circular(8),
                            border: Border.all(
                              color:
                                  const Color(0xFFBBCABF)
                                      .withOpacity(0.3),
                            ),
                          ),
                          child: const Icon(
                            Icons.person,
                            size: 32,
                            color: Color(0xFF565E74),
                          ),
                        ),

                        const SizedBox(width: 12),

                        Expanded(
                          child: Column(
                            crossAxisAlignment:
                                CrossAxisAlignment.start,
                            children: [

                              Row(
                                mainAxisAlignment:
                                    MainAxisAlignment
                                        .spaceBetween,
                                children: [

                                  const Text(
                                    'Alisa Yasmin',
                                    style: TextStyle(
                                      fontSize: 18,
                                      fontWeight:
                                          FontWeight.w600,
                                    ),
                                  ),

                                  Container(
                                    padding:
                                        const EdgeInsets
                                            .symmetric(
                                      horizontal: 8,
                                      vertical: 2,
                                    ),
                                    decoration:
                                        BoxDecoration(
                                      color: const Color(
                                        0x1A006C49,
                                      ),
                                      borderRadius:
                                          BorderRadius
                                              .circular(4),
                                    ),
                                    child: const Text(
                                      'Tepat Waktu',
                                      style: TextStyle(
                                        fontSize: 11,
                                        fontWeight:
                                            FontWeight.w600,
                                        color:
                                            Color(
                                          0xFF006C49,
                                        ),
                                      ),
                                    ),
                                  ),
                                ],
                              ),

                              const SizedBox(height: 2),

                              const Text(
                                'EMP-042 • Barista Station',
                                style: TextStyle(
                                  fontSize: 12,
                                  color:
                                      Color(0xFF565E74),
                                ),
                              ),

                              const SizedBox(height: 3),

                              const Row(
                                children: [
                                  Icon(
                                    Icons.verified,
                                    size: 13,
                                    color:
                                        Color(0xFF006C49),
                                  ),
                                  SizedBox(width: 4),
                                  Text(
                                    'Face Match 99.4%',
                                    style: TextStyle(
                                      fontSize: 11,
                                      fontWeight:
                                          FontWeight.w500,
                                      color:
                                          Color(
                                        0xFF006C49,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),

                    const SizedBox(height: 16),

                    // GARIS
                    Container(
                      width: double.infinity,
                      height: 1,
                      color: const Color(0xFFE6E8EA),
                    ),

                    const SizedBox(height: 16),

                    // ================= DETAIL =================

                    _detailRow(
                      icon: Icons.schedule,
                      title: 'Waktu & Tanggal',
                      value: '17:42:34 WIB • 22 Sep',
                    ),

                    const SizedBox(height: 12),

                    _detailRow(
                      icon: Icons.event_note,
                      title: 'Jadwal Shift',
                      value:
                          'Shift Pagi (08:00 - 16:00)',
                    ),

                    const SizedBox(height: 12),

                    _detailRow(
                      icon: Icons.location_on,
                      title: 'Lokasi Presensi',
                      value: 'Jalan tidar',
                      extra: '(12m)',
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 12),

              // ================= CATATAN =================
              const Row(
                mainAxisAlignment:
                    MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.info,
                    size: 16,
                    color: Color(0xFF565E74),
                  ),

                  SizedBox(width: 8),

                  Flexible(
                    child: Text(
                      'Shift aktif. Silakan buka terminal POS '
                      'atau hubungi supervisor.',
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 12,
                        color: Color(0xFF565E74),
                      ),
                    ),
                  ),
                ],
              ),

              const SizedBox(height: 16),

              // ================= BUTTON =================
              SizedBox(
                width: double.infinity,
                height: 48,
                child: ElevatedButton(
                  onPressed: () {
                    Navigator.popUntil(
                      context,
                      (route) => route.isFirst,
                    );
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
                        'Kembali ke Beranda Shift',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                        ),
                      ),

                      SizedBox(width: 8),

                      Icon(
                        Icons.arrow_forward,
                        size: 18,
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),

      // ================= BOTTOM NAVIGATION =================
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
  // DETAIL ROW
  // ===================================================

  static Widget _detailRow({
    required IconData icon,
    required String title,
    required String value,
    String? extra,
  }) {
    return Row(
      children: [
        Icon(
          icon,
          size: 18,
          color: const Color(0xFF565E74),
        ),

        const SizedBox(width: 8),

        Expanded(
          child: Text(
            title,
            style: const TextStyle(
              fontSize: 12,
              color: Color(0xFF565E74),
            ),
          ),
        ),

        Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              value,
              textAlign: TextAlign.right,
              style: const TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: Color(0xFF191C1E),
              ),
            ),

            if (extra != null) ...[
              const SizedBox(width: 4),
              Text(
                extra,
                style: const TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.w600,
                  color: Color(0xFF006C49),
                ),
              ),
            ],
          ],
        ),
      ],
    );
  }
}