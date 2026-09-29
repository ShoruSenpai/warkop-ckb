import 'package:flutter/material.dart';

import '../models/absensi_model.dart';
import '../services/app_data.dart';
import '../services/api_service.dart';
import '../services/app_session.dart';
import '../theme/stitch_theme.dart';
import 'verifikasi_lokasi_screen.dart';

class AbsensiHomeScreen extends StatefulWidget {
  const AbsensiHomeScreen({super.key});

  @override
  State<AbsensiHomeScreen> createState() => _AbsensiHomeScreenState();
}

class _AbsensiHomeScreenState extends State<AbsensiHomeScreen> {
  List<AbsensiRecord> _history = const [];
  bool _loadingHistory = true;
  bool _apiOnline = false;
  String? _historyError;

  @override
  void initState() {
    super.initState();
    _loadHistory();
  }

  Future<void> _loadHistory() async {
    final user = AppSession.instance.user;
    if (user == null || !ApiService.isSupabaseConfigured) {
      setState(() {
        _loadingHistory = false;
        _apiOnline = false;
      });
      return;
    }

    setState(() {
      _loadingHistory = true;
      _historyError = null;
    });
    try {
      final history = await ApiService.riwayatAbsensi(karyawanId: user.id);
      if (!mounted) return;
      setState(() {
        _history = history;
        _apiOnline = true;
        _loadingHistory = false;
      });
    } catch (error) {
      if (!mounted) return;
      setState(() {
        _apiOnline = false;
        _historyError = error.toString();
        _loadingHistory = false;
      });
    }
  }

  String _dateLabel(DateTime date) {
    const weekdays = [
      'Senin',
      'Selasa',
      'Rabu',
      'Kamis',
      'Jumat',
      'Sabtu',
      'Minggu',
    ];
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
    final today = DateTime.now();
    final isYesterday = DateUtils.dateOnly(date).isAtSameMomentAs(
      DateUtils.dateOnly(today.subtract(const Duration(days: 1))),
    );
    if (isYesterday) return 'Kemarin (${date.day} ${months[date.month - 1]})';
    return '${weekdays[date.weekday - 1]}, ${date.day} ${months[date.month - 1]}';
  }

  String _timeLabel(DateTime date) =>
      '${date.hour.toString().padLeft(2, '0')}:${date.minute.toString().padLeft(2, '0')} WIB';

  @override
  Widget build(BuildContext context) {
    final user = AppSession.instance.user;
    final now = DateTime.now();
    final isCompact = MediaQuery.sizeOf(context).width < 420;
    final isWide = MediaQuery.sizeOf(context).width >= 600;
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
    const weekdays = [
      'Senin',
      'Selasa',
      'Rabu',
      'Kamis',
      'Jumat',
      'Sabtu',
      'Minggu',
    ];

    return Scaffold(
      appBar: AppBar(
        backgroundColor: StitchTheme.surfaceWhite,
        elevation: 0,
        title: Row(
          children: [
            Flexible(
              child: Text(
                'Cak Kebo',
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(
                  color: StitchTheme.textDark,
                  fontWeight: FontWeight.bold,
                  fontSize: 17,
                ),
              ),
            ),
            if (isWide)
              const Text(
                ' · Absen',
                style: TextStyle(color: StitchTheme.textMuted, fontSize: 13),
              ),
            const Spacer(),
            _StatusPill(
              label: _apiOnline
                  ? 'Online'
                  : ApiService.isSupabaseConfigured
                  ? 'Offline'
                  : isCompact
                  ? 'API'
                  : 'API belum diatur',
              active: _apiOnline,
            ),
            SizedBox(width: isCompact ? 6 : 10),
            const CircleAvatar(
              radius: 20,
              backgroundColor: StitchTheme.primaryGreen,
              child: Icon(Icons.person_rounded, color: Colors.white, size: 22),
            ),
          ],
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(20, 18, 20, 28),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              '${weekdays[now.weekday - 1]}, ${now.day} ${months[now.month - 1]} ${now.year}  ·  ${_timeLabel(now)}',
              style: const TextStyle(
                color: StitchTheme.textMuted,
                fontSize: 14,
              ),
            ),
            const SizedBox(height: 18),
            Card(
              child: Padding(
                padding: const EdgeInsets.all(18),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Expanded(
                          child: Text(
                            'SHIFT HARI INI',
                            style: TextStyle(
                              color: StitchTheme.textMuted,
                              fontSize: 12,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                        _Tag(
                          label: now.hour < 16
                              ? 'Pagi · 8 Jam'
                              : 'Sore · 8 Jam',
                        ),
                      ],
                    ),
                    const SizedBox(height: 14),
                    const FittedBox(
                      fit: BoxFit.scaleDown,
                      alignment: Alignment.centerLeft,
                      child: Text(
                        '08:00 - 16:00 WIB',
                        style: TextStyle(
                          fontSize: 30,
                          fontWeight: FontWeight.bold,
                          color: StitchTheme.textDark,
                        ),
                      ),
                    ),
                    const SizedBox(height: 14),
                    const Divider(height: 1),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        const Icon(
                          Icons.storefront_outlined,
                          size: 20,
                          color: StitchTheme.textMuted,
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                user?.branch ?? 'Outlet',
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                                style: const TextStyle(
                                  color: StitchTheme.textMuted,
                                  fontSize: 15,
                                ),
                              ),
                              const SizedBox(height: 4),
                              const Text(
                                'Toleransi s/d 08:15 WIB',
                                style: TextStyle(
                                  color: StitchTheme.accentWarning,
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 16),
            InkWell(
              onTap: () => Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) => const VerifikasiLokasiScreen(),
                ),
              ),
              borderRadius: BorderRadius.circular(18),
              child: Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 16,
                  vertical: 15,
                ),
                decoration: BoxDecoration(
                  color: StitchTheme.primaryGreen.withValues(alpha: 0.07),
                  border: Border.all(
                    color: StitchTheme.primaryGreen.withValues(alpha: 0.18),
                  ),
                  borderRadius: BorderRadius.circular(18),
                ),
                child: Row(
                  children: [
                    const Icon(
                      Icons.near_me_rounded,
                      color: StitchTheme.primaryGreen,
                    ),
                    const SizedBox(width: 12),
                    const Expanded(
                      child: Text(
                        'Periksa radius outlet',
                        style: TextStyle(
                          fontWeight: FontWeight.w600,
                          color: StitchTheme.textDark,
                        ),
                      ),
                    ),
                    Text(
                      'Radius ${outletGeofenceRadiusMeters.round()} m',
                      style: const TextStyle(
                        color: StitchTheme.primaryGreen,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 22),
            if (isCompact)
              const Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Verifikasi Wajah',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                      color: StitchTheme.textDark,
                    ),
                  ),
                  SizedBox(height: 8),
                  Row(
                    children: [
                      CircleAvatar(
                        radius: 4,
                        backgroundColor: StitchTheme.primaryGreen,
                      ),
                      SizedBox(width: 7),
                      Text(
                        'Kamera siap',
                        style: TextStyle(
                          color: StitchTheme.primaryGreen,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ],
              )
            else
              const Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    'Verifikasi Wajah',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.w600,
                      color: StitchTheme.textDark,
                    ),
                  ),
                  Row(
                    children: [
                      CircleAvatar(
                        radius: 4,
                        backgroundColor: StitchTheme.primaryGreen,
                      ),
                      SizedBox(width: 7),
                      Text(
                        'Kamera siap',
                        style: TextStyle(
                          color: StitchTheme.primaryGreen,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            const SizedBox(height: 14),
            SizedBox(
              width: double.infinity,
              height: 58,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: StitchTheme.primaryGreen,
                  foregroundColor: Colors.white,
                  elevation: 0,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(16),
                  ),
                ),
                onPressed: () => Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => const VerifikasiLokasiScreen(),
                  ),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const Icon(Icons.login_rounded),
                    const SizedBox(width: 10),
                    Flexible(
                      child: Text(
                        'Absen Masuk Sekarang',
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: TextStyle(
                          fontSize: isCompact ? 15 : 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ),
            const SizedBox(height: 26),
            Card(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(16, 14, 16, 8),
                child: Column(
                  children: [
                    Row(
                      children: [
                        const Expanded(
                          child: Text(
                            'Riwayat Terakhir',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                        IconButton(
                          onPressed: _loadHistory,
                          tooltip: 'Muat ulang riwayat',
                          icon: const Icon(
                            Icons.refresh_rounded,
                            color: StitchTheme.primaryGreen,
                          ),
                        ),
                      ],
                    ),
                    const Divider(height: 1),
                    if (_loadingHistory)
                      const Padding(
                        padding: EdgeInsets.all(18),
                        child: CircularProgressIndicator(strokeWidth: 2),
                      )
                    else if (_historyError != null)
                      Padding(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        child: Text(
                          _historyError!,
                          style: const TextStyle(
                            color: StitchTheme.accentWarning,
                            fontSize: 13,
                          ),
                        ),
                      )
                    else if (_history.isEmpty)
                      Padding(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        child: Text(
                          ApiService.isSupabaseConfigured
                              ? 'Belum ada absensi tercatat.'
                              : 'Hubungkan Supabase untuk memuat riwayat.',
                          style: const TextStyle(
                            color: StitchTheme.textMuted,
                            fontSize: 13,
                          ),
                        ),
                      )
                    else
                      ..._history.map(
                        (record) => _HistoryRow(
                          record: record,
                          dateLabel: _dateLabel(record.waktu),
                          timeLabel: _timeLabel(record.waktu),
                        ),
                      ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _StatusPill extends StatelessWidget {
  const _StatusPill({required this.label, required this.active});

  final String label;
  final bool active;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 6),
      decoration: BoxDecoration(
        color: StitchTheme.backgroundLight,
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: StitchTheme.borderSubtle),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          CircleAvatar(
            radius: 4,
            backgroundColor: active
                ? StitchTheme.primaryGreen
                : StitchTheme.textMuted,
          ),
          const SizedBox(width: 6),
          Text(
            label,
            style: const TextStyle(fontSize: 11, color: StitchTheme.textDark),
          ),
        ],
      ),
    );
  }
}

class _Tag extends StatelessWidget {
  const _Tag({required this.label});

  final String label;

  @override
  Widget build(BuildContext context) => Container(
    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
    decoration: BoxDecoration(
      color: StitchTheme.primaryGreen.withValues(alpha: 0.08),
      borderRadius: BorderRadius.circular(8),
    ),
    child: Text(
      label,
      style: const TextStyle(
        color: StitchTheme.primaryGreen,
        fontSize: 12,
        fontWeight: FontWeight.w600,
      ),
    ),
  );
}

class _HistoryRow extends StatelessWidget {
  const _HistoryRow({
    required this.record,
    required this.dateLabel,
    required this.timeLabel,
  });

  final AbsensiRecord record;
  final String dateLabel;
  final String timeLabel;

  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.symmetric(vertical: 12),
    child: Row(
      children: [
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                '$dateLabel · ${record.tipe}',
                style: const TextStyle(
                  color: StitchTheme.textDark,
                  fontWeight: FontWeight.w500,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                timeLabel,
                style: const TextStyle(color: StitchTheme.textMuted),
              ),
            ],
          ),
        ),
        const _Tag(label: 'Tercatat'),
      ],
    ),
  );
}
