import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';

import '../services/app_data.dart';
import '../services/app_session.dart';
import '../theme/stitch_theme.dart';
import 'main_navigation.dart';

class AbsensiSuksesScreen extends StatelessWidget {
  const AbsensiSuksesScreen({
    super.key,
    required this.latitude,
    required this.longitude,
    required this.waktu,
  });

  final double latitude;
  final double longitude;
  final DateTime waktu;

  String get _timeLabel =>
      '${waktu.hour.toString().padLeft(2, '0')}:${waktu.minute.toString().padLeft(2, '0')}:${waktu.second.toString().padLeft(2, '0')} WIB';

  @override
  Widget build(BuildContext context) {
    final user = AppSession.instance.user;
    final distance = Geolocator.distanceBetween(
      outletLatitude,
      outletLongitude,
      latitude,
      longitude,
    ).round();

    return Scaffold(
      appBar: AppBar(
        backgroundColor: StitchTheme.surfaceWhite,
        elevation: 0,
        title: const Text(
          'Cak Kebo  ·  Absen',
          style: TextStyle(
            color: StitchTheme.textDark,
            fontSize: 16,
            fontWeight: FontWeight.w600,
          ),
        ),
        actions: const [
          Padding(
            padding: EdgeInsets.only(right: 16),
            child: CircleAvatar(
              radius: 19,
              backgroundColor: StitchTheme.primaryGreen,
              child: Icon(Icons.person_rounded, color: Colors.white),
            ),
          ),
        ],
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.fromLTRB(20, 20, 20, 28),
          child: Column(
            children: [
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: StitchTheme.surfaceWhite,
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: StitchTheme.borderSubtle),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const _SuccessTag(label: 'Presensi Tersinkronisasi'),
                    const SizedBox(height: 12),
                    ClipRRect(
                      borderRadius: BorderRadius.circular(10),
                      child: const LinearProgressIndicator(
                        value: 1,
                        minHeight: 6,
                        color: Color(0xFF17AD82),
                        backgroundColor: StitchTheme.borderSubtle,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),
              Container(
                width: 82,
                height: 82,
                decoration: BoxDecoration(
                  color: StitchTheme.primaryGreen.withValues(alpha: 0.09),
                  borderRadius: BorderRadius.circular(28),
                ),
                child: const Icon(
                  Icons.check_circle_rounded,
                  color: StitchTheme.primaryGreen,
                  size: 50,
                ),
              ),
              const SizedBox(height: 20),
              const Text(
                'Absensi Berhasil Dicatat',
                style: TextStyle(
                  fontSize: 25,
                  fontWeight: FontWeight.bold,
                  color: StitchTheme.textDark,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 8),
              const Text(
                'Data kehadiran telah dikirim ke server operasional.',
                style: TextStyle(color: StitchTheme.textMuted, fontSize: 14),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 12),
              const Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.cloud_done_rounded,
                    color: StitchTheme.primaryGreen,
                    size: 19,
                  ),
                  SizedBox(width: 7),
                  Text(
                    'Tersinkronisasi Online',
                    style: TextStyle(
                      color: StitchTheme.primaryGreen,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 24),
              Card(
                child: Padding(
                  padding: const EdgeInsets.all(18),
                  child: Column(
                    children: [
                      Row(
                        children: [
                          Container(
                            width: 72,
                            height: 72,
                            decoration: BoxDecoration(
                              color: StitchTheme.backgroundLight,
                              borderRadius: BorderRadius.circular(14),
                              border: Border.all(
                                color: StitchTheme.borderSubtle,
                              ),
                            ),
                            child: const Icon(
                              Icons.person_rounded,
                              color: StitchTheme.textMuted,
                              size: 36,
                            ),
                          ),
                          const SizedBox(width: 14),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  user?.name ?? 'Karyawan',
                                  style: const TextStyle(
                                    fontSize: 20,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                                const SizedBox(height: 5),
                                Text(
                                  '${user?.id ?? '-'} · ${user?.branch ?? '-'}',
                                  style: const TextStyle(
                                    color: StitchTheme.textMuted,
                                  ),
                                ),
                                const SizedBox(height: 7),
                                const Text(
                                  'Foto kehadiran terkirim',
                                  style: TextStyle(
                                    color: StitchTheme.primaryGreen,
                                    fontWeight: FontWeight.w500,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const _SuccessTag(label: 'Masuk'),
                        ],
                      ),
                      const Padding(
                        padding: EdgeInsets.symmetric(vertical: 16),
                        child: Divider(height: 1),
                      ),
                      _AttendanceDetail(
                        icon: Icons.access_time_rounded,
                        label: 'Waktu & Tanggal',
                        value: _timeLabel,
                      ),
                      const SizedBox(height: 14),
                      const _AttendanceDetail(
                        icon: Icons.calendar_month_rounded,
                        label: 'Jadwal Shift',
                        value: 'Shift Pagi (08:00 - 16:00)',
                      ),
                      const SizedBox(height: 14),
                      _AttendanceDetail(
                        icon: Icons.location_on_outlined,
                        label: 'Lokasi Presensi',
                        value: '$outletName ($distance m)',
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),
              const Row(
                children: [
                  Icon(
                    Icons.info_outline_rounded,
                    color: StitchTheme.textMuted,
                    size: 19,
                  ),
                  SizedBox(width: 9),
                  Expanded(
                    child: Text(
                      'Shift aktif. Anda dapat melanjutkan aktivitas kerja.',
                      style: TextStyle(color: StitchTheme.textMuted),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 22),
              SizedBox(
                width: double.infinity,
                height: 56,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: StitchTheme.primaryGreen,
                    foregroundColor: Colors.white,
                    elevation: 0,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(15),
                    ),
                  ),
                  onPressed: () {
                    Navigator.pushAndRemoveUntil(
                      context,
                      MaterialPageRoute(
                        builder: (_) => const MainNavigationScreen(),
                      ),
                      (route) => false,
                    );
                  },
                  child: const Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        'Kembali ke Beranda Shift',
                        style: TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      SizedBox(width: 10),
                      Icon(Icons.arrow_forward_rounded),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _SuccessTag extends StatelessWidget {
  const _SuccessTag({required this.label});

  final String label;

  @override
  Widget build(BuildContext context) => Container(
    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
    decoration: BoxDecoration(
      color: StitchTheme.primaryGreen.withValues(alpha: 0.09),
      borderRadius: BorderRadius.circular(7),
    ),
    child: Text(
      label,
      style: const TextStyle(
        color: StitchTheme.primaryGreen,
        fontWeight: FontWeight.w600,
        fontSize: 12,
      ),
    ),
  );
}

class _AttendanceDetail extends StatelessWidget {
  const _AttendanceDetail({
    required this.icon,
    required this.label,
    required this.value,
  });

  final IconData icon;
  final String label;
  final String value;

  @override
  Widget build(BuildContext context) => Row(
    children: [
      Icon(icon, size: 20, color: StitchTheme.textMuted),
      const SizedBox(width: 11),
      Expanded(
        child: Text(
          label,
          style: const TextStyle(color: StitchTheme.textMuted),
        ),
      ),
      Flexible(
        child: Text(
          value,
          textAlign: TextAlign.right,
          style: const TextStyle(
            color: StitchTheme.textDark,
            fontWeight: FontWeight.w600,
          ),
        ),
      ),
    ],
  );
}
