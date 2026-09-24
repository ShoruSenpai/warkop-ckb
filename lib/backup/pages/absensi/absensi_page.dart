import 'dart:async';
import 'package:flutter/material.dart';
import '../../core/constants/app_colors.dart';
import '../../core/constants/app_text_styles.dart';
import '../../widgets/custom_bottom_nav.dart';
import 'widgets/shift_info_card.dart';
import 'widgets/gps_status_bar.dart';
import 'widgets/history_card.dart';

enum CheckInState { idle, loading, success }

class AbsensiPage extends StatefulWidget {
  const AbsensiPage({super.key});

  @override
  State<AbsensiPage> createState() => _AbsensiPageState();
}

class _AbsensiPageState extends State<AbsensiPage> {
  CheckInState _checkInState = CheckInState.idle;
  int _navIndex = 0;
  Timer? _clockTimer;
  DateTime _now = DateTime.now();

  static const _days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
  static const _months = [
    'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
  ];

  @override
  void initState() {
    super.initState();
    _clockTimer = Timer.periodic(const Duration(seconds: 1), (_) {
      setState(() => _now = DateTime.now());
    });
  }

  @override
  void dispose() {
    _clockTimer?.cancel();
    super.dispose();
  }

  String get _formattedDateTime {
    final d = _days[_now.weekday % 7];
    final m = _months[_now.month - 1];
    String two(int v) => v.toString().padLeft(2, '0');
    return '$d, ${_now.day} $m ${_now.year} | ${two(_now.hour)}:${two(_now.minute)}:${two(_now.second)} WIB';
  }

  // Tiru alur pada script asli: tombol -> loading 1200ms -> sukses.
  // Idealnya navigasi berpindah ke verifikasi_wajah_page lalu verifikasi_gps_page
  // sebelum sampai di sini; disederhanakan agar polanya terlihat jelas.
  Future<void> _handleCheckIn() async {
    setState(() => _checkInState = CheckInState.loading);
    await Future.delayed(const Duration(milliseconds: 1200));
    if (!mounted) return;
    setState(() => _checkInState = CheckInState.success);
  }

  void _handleRefreshGps() {
    // TODO: panggil LocationService untuk ambil ulang koordinat & radius.
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Memperbarui lokasi GPS...'), duration: Duration(seconds: 1)),
    );
  }

  void _handleSubstitute() {
    // TODO: navigasi ke halaman pengajuan gantikan shift.
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.surface,
      appBar: _buildAppBar(),
      bottomNavigationBar: CustomBottomNav(
        currentIndex: _navIndex,
        onTap: (i) => setState(() => _navIndex = i),
      ),
      body: SafeArea(
        top: false,
        child: SingleChildScrollView(
          padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                _formattedDateTime,
                style: AppTextStyles.bodySm.copyWith(fontSize: 12),
              ),
              const SizedBox(height: 12),
              const ShiftInfoCard(
                shiftLabel: 'Pagi • 8 Jam',
                timeRange: '08:00 - 16:00',
                station: 'Bar Station Utama',
                toleranceLabel: 'Toleransi s/d 08:15',
              ),
              const SizedBox(height: 12),
              GpsStatusBar(
                label: 'Radius Outlet Valid (12m)',
                isValid: true,
                onRefresh: _handleRefreshGps,
              ),
              const SizedBox(height: 16),
              _buildFaceVerificationLabel(),
              const SizedBox(height: 16),
              _buildActionButtons(),
              const SizedBox(height: 16),
              HistoryCard(
                items: const [
                  AttendanceLogItem(
                    dateLabel: 'Kemarin (23 Okt)',
                    timeRange: '07:55 - 16:05 WIB',
                    statusLabel: 'Tepat Waktu',
                  ),
                  AttendanceLogItem(
                    dateLabel: 'Minggu (22 Okt)',
                    timeRange: '07:58 - 16:02 WIB',
                    statusLabel: 'Tepat Waktu',
                  ),
                ],
                onSeeAll: () {},
              ),
            ],
          ),
        ),
      ),
    );
  }

  PreferredSizeWidget _buildAppBar() {
    return AppBar(
      backgroundColor: AppColors.surfaceContainerLowest.withOpacity(0.9),
      elevation: 0,
      scrolledUnderElevation: 0,
      toolbarHeight: 56,
      titleSpacing: 16,
      title: Row(
        children: [
          Text('Warkop Cak Kebo', style: AppTextStyles.headlineSm),
          const SizedBox(width: 6),
          Text('•', style: AppTextStyles.labelSm),
          const SizedBox(width: 6),
          Text('Absensi', style: AppTextStyles.labelMd),
          const SizedBox(width: 10),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
            decoration: BoxDecoration(
              color: AppColors.surfaceContainer,
              borderRadius: BorderRadius.circular(4),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  width: 6,
                  height: 6,
                  decoration: const BoxDecoration(
                    color: AppColors.primaryContainer,
                    shape: BoxShape.circle,
                  ),
                ),
                const SizedBox(width: 4),
                Text('Online', style: AppTextStyles.labelSm),
              ],
            ),
          ),
        ],
      ),
      actions: [
        Padding(
          padding: const EdgeInsets.only(right: 16),
          child: CircleAvatar(
            radius: 16,
            backgroundColor: AppColors.primary,
            child: const Icon(Icons.person, color: AppColors.onPrimary, size: 18),
          ),
        ),
      ],
    );
  }

  Widget _buildFaceVerificationLabel() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text('Verifikasi Wajah', style: AppTextStyles.labelMd),
        Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 6,
              height: 6,
              decoration: const BoxDecoration(color: AppColors.primaryContainer, shape: BoxShape.circle),
            ),
            const SizedBox(width: 4),
            Text('Kamera Siap', style: AppTextStyles.labelSm.copyWith(color: AppColors.primary)),
          ],
        ),
      ],
    );
    // Catatan: pratinjau kamera sesungguhnya ada di halaman
    // verifikasi_wajah_page.dart (pakai package `camera`), bukan di sini.
  }

  Widget _buildActionButtons() {
    return Column(
      children: [
        SizedBox(
          width: double.infinity,
          height: 48,
          child: ElevatedButton(
            onPressed: _checkInState == CheckInState.loading ? null : _handleCheckIn,
            style: ElevatedButton.styleFrom(
              backgroundColor: _checkInState == CheckInState.success
                  ? const Color(0xFF065F46)
                  : AppColors.primary,
              foregroundColor: Colors.white,
              elevation: 1,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
            ),
            child: _buildCheckInButtonContent(),
          ),
        ),
        const SizedBox(height: 10),
        SizedBox(
          width: double.infinity,
          height: 44,
          child: OutlinedButton.icon(
            onPressed: _handleSubstitute,
            icon: const Icon(Icons.swap_horiz, size: 18, color: AppColors.onSurfaceVariant),
            label: Text('Gantikan Shift Rekan', style: AppTextStyles.labelMd),
            style: OutlinedButton.styleFrom(
              foregroundColor: AppColors.onSurface,
              side: const BorderSide(color: AppColors.outlineVariant),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildCheckInButtonContent() {
    switch (_checkInState) {
      case CheckInState.idle:
        return Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.login, size: 20),
            const SizedBox(width: 8),
            Text('Absen Masuk Sekarang', style: AppTextStyles.labelLg.copyWith(color: Colors.white)),
          ],
        );
      case CheckInState.loading:
        return Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const SizedBox(
              width: 18,
              height: 18,
              child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
            ),
            const SizedBox(width: 10),
            Text('Memverifikasi Presensi...', style: AppTextStyles.labelLg.copyWith(color: Colors.white)),
          ],
        );
      case CheckInState.success:
        return Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.done_all, size: 22),
            const SizedBox(width: 8),
            Text('Absensi Berhasil Masuk!', style: AppTextStyles.labelLg.copyWith(color: Colors.white)),
          ],
        );
    }
  }
}
