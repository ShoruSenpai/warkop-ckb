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
  static const Color primaryColor = Color(0xFF006C49);
  static const Color primaryContainer = Color(0xFF10B981);
  static const Color surfaceColor = Color(0xFFF7F9FB);
  static const Color cardColor = Colors.white;
  static const Color borderColor = Color(0xFFE2E8F0);
  static const Color textColor = Color(0xFF191C1E);
  static const Color secondaryText = Color(0xFF3C4A42);

  Timer? _timer;
  DateTime _currentTime = DateTime.now();

  bool _isChecking = false;
  bool _success = false;

  int _selectedIndex = 0;

  @override
  void initState() {
    super.initState();

    _timer = Timer.periodic(
      const Duration(seconds: 1),
      (_) {
        if (mounted) {
          setState(() {
            _currentTime = DateTime.now();
          });
        }
      },
    );
  }

  @override
  void dispose() {
    _timer?.cancel();
    super.dispose();
  }

  String _dayName(int weekday) {
    const days = [
      'Senin',
      'Selasa',
      'Rabu',
      'Kamis',
      'Jumat',
      'Sabtu',
      'Minggu',
    ];

    return days[weekday - 1];
  }

  String _monthName(int month) {
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

    return months[month - 1];
  }

  String _liveDateTime() {
    final hour =
        _currentTime.hour.toString().padLeft(2, '0');

    final minute =
        _currentTime.minute.toString().padLeft(2, '0');

    final second =
        _currentTime.second.toString().padLeft(2, '0');

    return '${_dayName(_currentTime.weekday)}, '
        '${_currentTime.day} '
        '${_monthName(_currentTime.month)} '
        '${_currentTime.year} | '
        '$hour:$minute:$second WIB';
  }

  Future<void> _startVerification() async {
    if (_isChecking || _success) return;

    setState(() {
      _isChecking = true;
    });

    await Future.delayed(
      const Duration(milliseconds: 1200),
    );

    if (!mounted) return;

    setState(() {
      _isChecking = false;
      _success = true;
    });
  }

  void _refreshGps() {
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Memperbarui lokasi GPS...'),
        duration: Duration(seconds: 1),
      ),
    );
  }

  void _substituteShift() {
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Fitur penggantian shift dipilih'),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: surfaceColor,

      // ======================================================
      // HEADER
      // ======================================================

      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(64),
        child: Container(
          decoration: BoxDecoration(
            color: cardColor.withOpacity(0.95),
            boxShadow: const [
              BoxShadow(
                color: Color(0x0A000000),
                blurRadius: 8,
                offset: Offset(0, 1),
              ),
            ],
          ),
          child: SafeArea(
            child: Padding(
              padding:
                  const EdgeInsets.symmetric(horizontal: 16),
              child: Row(
                mainAxisAlignment:
                    MainAxisAlignment.spaceBetween,
                children: [

                  // Cafe name
                  Row(
                    children: [
                      const Text(
                        'Cak Kebo',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.w600,
                          color: textColor,
                        ),
                      ),

                      const SizedBox(width: 6),

                      const Text(
                        '•',
                        style: TextStyle(
                          color: secondaryText,
                        ),
                      ),

                      const SizedBox(width: 6),

                      const Text(
                        'Absensi',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w500,
                          color: secondaryText,
                        ),
                      ),

                      const SizedBox(width: 10),

                      // Online
                      Container(
                        padding:
                            const EdgeInsets.symmetric(
                          horizontal: 6,
                          vertical: 3,
                        ),
                        decoration: BoxDecoration(
                          color:
                              const Color(0xFFECEEF0),
                          borderRadius:
                              BorderRadius.circular(4),
                        ),
                        child: Row(
                          children: [
                            Container(
                              width: 6,
                              height: 6,
                              decoration:
                                  const BoxDecoration(
                                color: primaryContainer,
                                shape: BoxShape.circle,
                              ),
                            ),

                            const SizedBox(width: 5),

                            const Text(
                              'Online',
                              style: TextStyle(
                                fontSize: 11,
                                color: secondaryText,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),

                  // Profile
                  Container(
                    width: 32,
                    height: 32,
                    decoration:
                        const BoxDecoration(
                      color: primaryColor,
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
            ),
          ),
        ),
      ),

      // ======================================================
      // BODY
      // ======================================================

      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.fromLTRB(
            16,
            16,
            16,
            90,
          ),
          child: Column(
            crossAxisAlignment:
                CrossAxisAlignment.start,
            children: [

              // ==================================================
              // STATUS / LIVE DATETIME
              // ==================================================

              Row(
                mainAxisAlignment:
                    MainAxisAlignment.spaceBetween,
                crossAxisAlignment:
                    CrossAxisAlignment.start,
                children: [

                  const SizedBox(),

                  Column(
                    crossAxisAlignment:
                        CrossAxisAlignment.end,
                    children: [
                      Row(
                        children: [
                          Container(
                            width: 6,
                            height: 6,
                            decoration:
                                const BoxDecoration(
                              color: primaryContainer,
                              shape: BoxShape.circle,
                            ),
                          ),

                          const SizedBox(width: 6),

                          Text(
                            _liveDateTime(),
                            style:
                                const TextStyle(
                              fontSize: 12,
                              color: textColor,
                              fontWeight:
                                  FontWeight.w500,
                            ),
                          ),
                        ],
                      ),

                      const SizedBox(height: 3),

                      const Text(
                        'Belum Absen',
                        style: TextStyle(
                          fontSize: 11,
                          color: Color(0xFFB45309),
                          fontWeight:
                              FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ],
              ),

              const SizedBox(height: 12),

              // ==================================================
              // SHIFT CARD
              // ==================================================

              _buildShiftCard(),

              const SizedBox(height: 12),

              // ==================================================
              // GPS STATUS
              // ==================================================

              _buildGpsStatus(),

              const SizedBox(height: 12),

              // ==================================================
              // FACE VERIFICATION INSTRUCTION
              // ==================================================

              _buildFaceInstruction(),

              const SizedBox(height: 4),

              // ==================================================
              // BUTTON
              // ==================================================

              _buildButtons(),
            ],
          ),
        ),
      ),

      // ======================================================
      // BOTTOM NAVIGATION
      // ======================================================

      bottomNavigationBar:
          _buildBottomNavigation(),
    );
  }

  // ==========================================================
  // SHIFT CARD
  // ==========================================================

  Widget _buildShiftCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: cardColor,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: borderColor,
        ),
        boxShadow: const [
          BoxShadow(
            color: Color(0x0A000000),
            blurRadius: 4,
            offset: Offset(0, 1),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment:
            CrossAxisAlignment.start,
        children: [

          Row(
            mainAxisAlignment:
                MainAxisAlignment.spaceBetween,
            children: const [
              Text(
                'SHIFT HARI INI',
                style: TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.w500,
                  letterSpacing: 0.8,
                  color: secondaryText,
                ),
              ),

              Text(
                'Cabang Senopati',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w500,
                  color: secondaryText,
                ),
              ),
            ],
          ),

          const SizedBox(height: 10),

          Row(
            crossAxisAlignment:
                CrossAxisAlignment.end,
            children: const [
              Text(
                '08:00 – 16:00',
                style: TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.w700,
                  color: textColor,
                ),
              ),

              SizedBox(width: 8),

              Padding(
                padding:
                    EdgeInsets.only(bottom: 3),
                child: Text(
                  'WIB',
                  style: TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w500,
                    color: secondaryText,
                  ),
                ),
              ),
            ],
          ),

          const SizedBox(height: 12),

          Row(
            children: [
              const Icon(
                Icons.schedule,
                size: 15,
                color: Color(0xFFB45309),
              ),

              const SizedBox(width: 6),

              const Text(
                'Toleransi keterlambatan s/d ',
                style: TextStyle(
                  fontSize: 12,
                  color: secondaryText,
                ),
              ),

              const Text(
                '08:15 WIB',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                  color: textColor,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  // ==========================================================
  // GPS STATUS
  // ==========================================================

  Widget _buildGpsStatus() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(
        horizontal: 12,
        vertical: 10,
      ),
      decoration: BoxDecoration(
        color: const Color(0xFFF2F4F6),
        borderRadius: BorderRadius.circular(8),
        border: Border.all(
          color: borderColor,
        ),
      ),
      child: Row(
        mainAxisAlignment:
            MainAxisAlignment.spaceBetween,
        children: [

          Row(
            children: const [
              Icon(
                Icons.near_me,
                size: 18,
                color: primaryColor,
              ),

              SizedBox(width: 8),

              Text(
                'Dalam Radius Kafe (12m)',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w500,
                  color: textColor,
                ),
              ),
            ],
          ),

          Row(
            children: [
              const Icon(
                Icons.check_circle,
                size: 14,
                color: primaryColor,
              ),

              const SizedBox(width: 4),

              const Text(
                'Valid',
                style: TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.w600,
                  color: primaryColor,
                ),
              ),

              const SizedBox(width: 8),

              TextButton(
                onPressed: _refreshGps,
                style: TextButton.styleFrom(
                  padding: EdgeInsets.zero,
                  minimumSize: Size.zero,
                  tapTargetSize:
                      MaterialTapTargetSize
                          .shrinkWrap,
                ),
                child: const Text(
                  'Refresh',
                  style: TextStyle(
                    fontSize: 11,
                    decoration:
                        TextDecoration.underline,
                    color: secondaryText,
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  // ==========================================================
  // FACE INSTRUCTION
  // ==========================================================

  Widget _buildFaceInstruction() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(
        horizontal: 16,
        vertical: 24,
      ),
      child: Column(
        crossAxisAlignment:
            CrossAxisAlignment.center,
        children: [

          // Face icon
          Container(
            width: 56,
            height: 56,
            decoration: BoxDecoration(
              color: const Color(0xFFECEEF0),
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.face,
              size: 26,
              color: primaryColor,
            ),
          ),

          const SizedBox(height: 12),

          const Text(
            'Verifikasi Wajah Masuk',
            textAlign: TextAlign.center,
            style: TextStyle(
              fontSize: 17,
              fontWeight: FontWeight.w600,
              color: textColor,
            ),
          ),

          const SizedBox(height: 6),

          const Text(
            'Kamera akan memindai wajah saat '
            'tombol ditekan. Posisikan wajah '
            'di area terang.',
            textAlign: TextAlign.center,
            style: TextStyle(
              fontSize: 13,
              height: 1.5,
              color: secondaryText,
            ),
          ),
        ],
      ),
    );
  }

  // ==========================================================
  // BUTTONS
  // ==========================================================

  Widget _buildButtons() {
    return Column(
      children: [

        SizedBox(
          width: double.infinity,
          height: 48,
          child: ElevatedButton(
            onPressed:
                _startVerification,
            style: ElevatedButton.styleFrom(
              backgroundColor:
                  _success
                      ? const Color(0xFF065F46)
                      : primaryColor,
              foregroundColor: Colors.white,
              elevation: 1,
              shape:
                  RoundedRectangleBorder(
                borderRadius:
                    BorderRadius.circular(12),
              ),
            ),
            child: _buildButtonContent(),
          ),
        ),

        const SizedBox(height: 12),

        SizedBox(
          width: double.infinity,
          child: TextButton(
            onPressed:
                _substituteShift,
            child: const Text(
              'Gantikan Shift Rekan',
              style: TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w500,
                color: secondaryText,
              ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildButtonContent() {
    if (_isChecking) {
      return const Row(
        mainAxisAlignment:
            MainAxisAlignment.center,
        children: [

          SizedBox(
            width: 20,
            height: 20,
            child:
                CircularProgressIndicator(
              strokeWidth: 2,
              color: Colors.white,
            ),
          ),

          SizedBox(width: 10),

          Text(
            'Memverifikasi Presensi...',
            style: TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      );
    }

    if (_success) {
      return const Row(
        mainAxisAlignment:
            MainAxisAlignment.center,
        children: [

          Icon(
            Icons.done_all,
            size: 22,
          ),

          SizedBox(width: 8),

          Text(
            'Absensi Berhasil Masuk!',
            style: TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.w700,
            ),
          ),
        ],
      );
    }

    return const Row(
      mainAxisAlignment:
          MainAxisAlignment.center,
      children: [

        Icon(
          Icons.camera_alt,
          size: 20,
        ),

        SizedBox(width: 8),

        Text(
          'Absen Masuk Sekarang',
          style: TextStyle(
            fontSize: 15,
            fontWeight: FontWeight.w600,
          ),
        ),
      ],
    );
  }

  // ==========================================================
  // BOTTOM NAVIGATION
  // ==========================================================

  Widget _buildBottomNavigation() {
    return NavigationBar(
      selectedIndex: _selectedIndex,
      backgroundColor: Colors.white,
      elevation: 4,
      height: 68,
      onDestinationSelected: (index) {
        setState(() {
          _selectedIndex = index;
        });
      },
      destinations: const [
        NavigationDestination(
          icon: Icon(
            Icons.how_to_reg_outlined,
          ),
          selectedIcon: Icon(
            Icons.how_to_reg,
          ),
          label: 'Absensi',
        ),

        NavigationDestination(
          icon: Icon(
            Icons.calendar_month_outlined,
          ),
          selectedIcon: Icon(
            Icons.calendar_month,
          ),
          label: 'Jadwal',
        ),

        NavigationDestination(
          icon: Icon(
            Icons.point_of_sale_outlined,
          ),
          selectedIcon: Icon(
            Icons.point_of_sale,
          ),
          label: 'POS',
        ),

        NavigationDestination(
          icon: Icon(
            Icons.account_circle_outlined,
          ),
          selectedIcon: Icon(
            Icons.account_circle,
          ),
          label: 'Profil',
        ),
      ],
    );
  }
}