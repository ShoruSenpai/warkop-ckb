import 'dart:async';

import 'package:flutter/material.dart';

class AttendancePage extends StatefulWidget {
  const AttendancePage({super.key});

  @override
  State<AttendancePage> createState() => _AttendancePageState();
}

class _AttendancePageState extends State<AttendancePage> {
  static const Color primaryColor = Color(0xFF006C49);
  static const Color primaryContainer = Color(0xFF10B981);
  static const Color surfaceColor = Color(0xFFF7F9FB);
  static const Color cardColor = Colors.white;
  static const Color borderColor = Color(0xFFE2E8F0);
  static const Color textColor = Color(0xFF191C1E);
  static const Color secondaryText = Color(0xFF3C4A42);

  Timer? _timer;

  DateTime _currentTime = DateTime.now();

  bool _isCheckingIn = false;
  bool _attendanceSuccess = false;

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

  String _getDayName(int weekday) {
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

  String _getMonthName(int month) {
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

  String _formatTime() {
    final hour = _currentTime.hour.toString().padLeft(2, '0');
    final minute = _currentTime.minute.toString().padLeft(2, '0');
    final second = _currentTime.second.toString().padLeft(2, '0');

    return '$hour:$minute:$second';
  }

  String _formatDateTime() {
    return '${_getDayName(_currentTime.weekday)}, '
        '${_currentTime.day} '
        '${_getMonthName(_currentTime.month)} '
        '${_currentTime.year} | '
        '${_formatTime()} WIB';
  }

  Future<void> _checkIn() async {
    if (_isCheckingIn || _attendanceSuccess) return;

    setState(() {
      _isCheckingIn = true;
    });

    await Future.delayed(const Duration(milliseconds: 1200));

    if (!mounted) return;

    setState(() {
      _isCheckingIn = false;
      _attendanceSuccess = true;
    });
  }

  void _refreshGps() {
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('GPS sedang diperbarui...'),
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

      // =========================
      // HEADER
      // =========================
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
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: [
                      const Text(
                        'Cafe Ops',
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

                      // Online status
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 6,
                          vertical: 3,
                        ),
                        decoration: BoxDecoration(
                          color: const Color(0xFFECEEF0),
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: Row(
                          children: [
                            Container(
                              width: 6,
                              height: 6,
                              decoration: const BoxDecoration(
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

                  // Profile icon
                  Container(
                    width: 32,
                    height: 32,
                    decoration: const BoxDecoration(
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

      // =========================
      // BODY
      // =========================
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.fromLTRB(
            16,
            16,
            16,
            90,
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [

              // =========================
              // LIVE DATE & TIME
              // =========================
              Text(
                _formatDateTime(),
                style: const TextStyle(
                  fontSize: 12,
                  color: secondaryText,
                ),
              ),

              const SizedBox(height: 12),

              // =========================
              // SHIFT CARD
              // =========================
              _buildShiftCard(),

              const SizedBox(height: 12),

              // =========================
              // GPS STATUS
              // =========================
              _buildGpsStatus(),

              const SizedBox(height: 14),

              // =========================
              // FACE VERIFICATION
              // =========================
              _buildFaceVerification(),

              const SizedBox(height: 12),

              // =========================
              // BUTTONS
              // =========================
              _buildActionButtons(),

              const SizedBox(height: 14),

              // =========================
              // ATTENDANCE HISTORY
              // =========================
              _buildAttendanceHistory(),
            ],
          ),
        ),
      ),

      // =========================
      // BOTTOM NAVIGATION
      // =========================
      bottomNavigationBar: _buildBottomNavigation(),
    );
  }

  // ============================================================
  // SHIFT CARD
  // ============================================================

  Widget _buildShiftCard() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(12),
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
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'SHIFT HARI INI',
                style: TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.w600,
                  color: secondaryText,
                  letterSpacing: 1,
                ),
              ),

              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 8,
                  vertical: 3,
                ),
                decoration: BoxDecoration(
                  color: const Color(0xFFECFDF5),
                  borderRadius: BorderRadius.circular(4),
                  border: Border.all(
                    color: const Color(0xFFA7F3D0),
                  ),
                ),
                child: const Text(
                  'Pagi • 8 Jam',
                  style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                    color: Color(0xFF065F46),
                  ),
                ),
              ),
            ],
          ),

          const SizedBox(height: 8),

          Row(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              const Text(
                '08:00 - 16:00',
                style: TextStyle(
                  fontSize: 28,
                  fontWeight: FontWeight.w700,
                  color: textColor,
                ),
              ),

              const SizedBox(width: 8),

              const Padding(
                padding: EdgeInsets.only(bottom: 4),
                child: Text(
                  'WIB',
                  style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w500,
                    color: secondaryText,
                  ),
                ),
              ),
            ],
          ),

          const SizedBox(height: 10),

          Container(
            height: 1,
            color: borderColor,
          ),

          const SizedBox(height: 10),

          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Row(
                children: [
                  const Icon(
                    Icons.storefront,
                    size: 17,
                    color: secondaryText,
                  ),

                  const SizedBox(width: 6),

                  const Text(
                    'Bar Station Utama',
                    style: TextStyle(
                      fontSize: 13,
                      color: secondaryText,
                    ),
                  ),
                ],
              ),

              const Text(
                'Toleransi s/d 08:15',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w500,
                  color: Color(0xFF92400E),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  // ============================================================
  // GPS STATUS
  // ============================================================

  Widget _buildGpsStatus() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(
        horizontal: 12,
        vertical: 10,
      ),
      decoration: BoxDecoration(
        color: const Color(0xFFECFDF5),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: const Color(0xFFA7F3D0),
        ),
        boxShadow: const [
          BoxShadow(
            color: Color(0x08000000),
            blurRadius: 4,
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          const Row(
            children: [
              Icon(
                Icons.near_me,
                size: 18,
                color: primaryColor,
              ),

              SizedBox(width: 8),

              Text(
                'Radius Outlet Valid (12m)',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                  color: Color(0xFF064E3B),
                ),
              ),
            ],
          ),

          TextButton(
            onPressed: _refreshGps,
            style: TextButton.styleFrom(
              padding: EdgeInsets.zero,
              minimumSize: Size.zero,
              tapTargetSize: MaterialTapTargetSize.shrinkWrap,
            ),
            child: const Text(
              'Refresh GPS',
              style: TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w600,
                color: primaryColor,
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ============================================================
  // FACE VERIFICATION
  // ============================================================

  Widget _buildFaceVerification() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text(
              'Verifikasi Wajah',
              style: TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: textColor,
              ),
            ),

            Row(
              children: [
                Container(
                  width: 6,
                  height: 6,
                  decoration: const BoxDecoration(
                    color: primaryContainer,
                    shape: BoxShape.circle,
                  ),
                ),

                const SizedBox(width: 5),

                const Text(
                  'Kamera Siap',
                  style: TextStyle(
                    fontSize: 11,
                    color: primaryColor,
                  ),
                ),
              ],
            ),
          ],
        ),

        const SizedBox(height: 8),

        // Placeholder area kamera
        Container(
          width: double.infinity,
          height: 150,
          decoration: BoxDecoration(
            color: const Color(0xFFE6E8EA),
            borderRadius: BorderRadius.circular(12),
          ),
          child: const Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                Icons.camera_alt_outlined,
                size: 40,
                color: secondaryText,
              ),

              SizedBox(height: 8),

              Text(
                'Kamera siap digunakan',
                style: TextStyle(
                  fontSize: 12,
                  color: secondaryText,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  // ============================================================
  // ACTION BUTTONS
  // ============================================================

  Widget _buildActionButtons() {
    return Column(
      children: [
        SizedBox(
          width: double.infinity,
          height: 48,
          child: ElevatedButton(
            onPressed: _checkIn,
            style: ElevatedButton.styleFrom(
              backgroundColor: _attendanceSuccess
                  ? const Color(0xFF065F46)
                  : primaryColor,
              foregroundColor: Colors.white,
              elevation: 1,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
            child: _buildCheckInContent(),
          ),
        ),

        const SizedBox(height: 10),

        SizedBox(
          width: double.infinity,
          height: 44,
          child: OutlinedButton(
            onPressed: _substituteShift,
            style: OutlinedButton.styleFrom(
              foregroundColor: textColor,
              backgroundColor: cardColor,
              side: const BorderSide(
                color: borderColor,
              ),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
            child: const Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(
                  Icons.swap_horiz,
                  size: 18,
                  color: secondaryText,
                ),

                SizedBox(width: 8),

                Text(
                  'Gantikan Shift Rekan',
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildCheckInContent() {
    if (_isCheckingIn) {
      return const Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          SizedBox(
            width: 20,
            height: 20,
            child: CircularProgressIndicator(
              strokeWidth: 2,
              color: Colors.white,
            ),
          ),

          SizedBox(width: 10),

          Text(
            'Memverifikasi Presensi...',
            style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w700,
            ),
          ),
        ],
      );
    }

    if (_attendanceSuccess) {
      return const Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            Icons.done_all,
            size: 22,
          ),

          SizedBox(width: 8),

          Text(
            'Absensi Berhasil Masuk!',
            style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w700,
            ),
          ),
        ],
      );
    }

    return const Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Icon(
          Icons.login,
          size: 20,
        ),

        SizedBox(width: 8),

        Text(
          'Absen Masuk Sekarang',
          style: TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w700,
          ),
        ),
      ],
    );
  }

  // ============================================================
  // ATTENDANCE HISTORY
  // ============================================================

  Widget _buildAttendanceHistory() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(12),
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
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Riwayat Terakhir',
                style: TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                  color: textColor,
                ),
              ),

              TextButton(
                onPressed: () {},
                style: TextButton.styleFrom(
                  padding: EdgeInsets.zero,
                  minimumSize: Size.zero,
                  tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                ),
                child: const Text(
                  'Lihat Semua',
                  style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w600,
                    color: primaryColor,
                  ),
                ),
              ),
            ],
          ),

          const Divider(
            height: 1,
            color: borderColor,
          ),

          _buildHistoryItem(
            'Kemarin (23 Okt)',
            '07:55 - 16:05 WIB',
          ),

          const Divider(
            height: 1,
            color: borderColor,
          ),

          _buildHistoryItem(
            'Minggu (22 Okt)',
            '07:58 - 16:02 WIB',
          ),
        ],
      ),
    );
  }

  Widget _buildHistoryItem(
    String date,
    String time,
  ) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 10),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                date,
                style: const TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.w600,
                  color: textColor,
                ),
              ),

              const SizedBox(height: 3),

              Text(
                time,
                style: const TextStyle(
                  fontSize: 12,
                  color: secondaryText,
                ),
              ),
            ],
          ),

          Container(
            padding: const EdgeInsets.symmetric(
              horizontal: 8,
              vertical: 3,
            ),
            decoration: BoxDecoration(
              color: const Color(0xFFECFDF5),
              borderRadius: BorderRadius.circular(4),
              border: Border.all(
                color: const Color(0xFFA7F3D0),
              ),
            ),
            child: const Text(
              'Tepat Waktu',
              style: TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w600,
                color: Color(0xFF065F46),
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ============================================================
  // BOTTOM NAVIGATION
  // ============================================================

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
          icon: Icon(Icons.how_to_reg_outlined),
          selectedIcon: Icon(Icons.how_to_reg),
          label: 'Absensi',
        ),
        NavigationDestination(
          icon: Icon(Icons.calendar_month_outlined),
          selectedIcon: Icon(Icons.calendar_month),
          label: 'Jadwal',
        ),
        NavigationDestination(
          icon: Icon(Icons.point_of_sale_outlined),
          selectedIcon: Icon(Icons.point_of_sale),
          label: 'POS',
        ),
        NavigationDestination(
          icon: Icon(Icons.account_circle_outlined),
          selectedIcon: Icon(Icons.account_circle),
          label: 'Profil',
        ),
      ],
    );
  }
}