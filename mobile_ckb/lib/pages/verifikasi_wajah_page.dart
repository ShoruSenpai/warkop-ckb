import 'package:flutter/material.dart';
import 'dart:ui' show PathMetric;
import 'dart:async';
import 'package:intl/intl.dart';

class VerifikasiWajahScreen extends StatefulWidget {
  const VerifikasiWajahScreen({super.key});

  @override
  State<VerifikasiWajahScreen> createState() => _VerifikasiWajahScreenState();
}

class _VerifikasiWajahScreenState extends State<VerifikasiWajahScreen>
    with SingleTickerProviderStateMixin {
      
  // Kontroler Animasi untuk Guide Garis Putus-Putus
  late AnimationController _spinController;
  
  // Timer untuk Jam Realtime
  late Timer _timer;
  String _currentTime = "";

  // Konstanta Warna
  final Color primary = const Color(0xFF006194);
  final Color primaryContainer = const Color(0xFF007BB9);
  final Color primaryFixed = const Color(0xFFCCE5FF);
  final Color secondary = const Color(0xFF396477);
  final Color secondaryContainer = const Color(0xFFBAE6FD);
  final Color surface = const Color(0xFFF8F9FF);
  final Color surfaceContainerLowest = const Color(0xFFFFFFFF);
  final Color surfaceContainerLow = const Color(0xFFEFF4FF);
  final Color surfaceContainer = const Color(0xFFE5EEFF);
  final Color onSurface = const Color(0xFF0B1C30);
  final Color onSurfaceVariant = const Color(0xFF3F4850);
  final Color onPrimary = const Color(0xFFFFFFFF);
  final Color onSecondaryFixedVariant = const Color(0xFF1E4C5F);

  @override
  void initState() {
    super.initState();

    // Inisialisasi Jam Realtime
    _updateTime();
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (mounted) _updateTime();
    });

    // Inisialisasi putaran garis putus-putus oval (berulang)
    _spinController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 40),
    )..repeat();
  }

  void _updateTime() {
    setState(() {
      _currentTime = "${DateFormat('HH:mm:ss').format(DateTime.now())} WIB";
    });
  }

  @override
  void dispose() {
    _timer.cancel();
    _spinController.dispose();
    super.dispose();
  }

  // Fungsi Tombol Shutter Kamera Ditekan
  void _handleShutter() {
    // Navigasi ke halaman 'Absen Berhasil Dicatat'
    Navigator.pushReplacementNamed(context, '/absen_sukses');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: surface,
      // ==========================================
      // HEADER / APP BAR
      // ==========================================
      appBar: AppBar(
        backgroundColor: surface.withOpacity(0.8),
        elevation: 0,
        automaticallyImplyLeading: false, // Sembunyikan tombol back default
        title: Row(
          children: [
            Container(
              width: 36,
              height: 36,
              decoration: BoxDecoration(
                color: surfaceContainer,
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
                  'Absensi',
                  style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w800,
                      color: onSurface),
                ),
              ],
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: Icon(Icons.notifications_none, color: onSurfaceVariant),
            onPressed: () {},
          ),
          Container(
            margin: const EdgeInsets.only(right: 16),
            width: 32,
            height: 32,
            decoration: BoxDecoration(
              color: primary,
              shape: BoxShape.circle,
              boxShadow: [
                BoxShadow(
                  color: primary.withOpacity(0.25),
                  blurRadius: 8,
                  offset: const Offset(0, 2),
                )
              ],
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
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
        child: SizedBox(
          width: 440,
          child: Column(
          children: [
            // 1. STEPPER & HEADLINE
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
              decoration: BoxDecoration(
                color: secondaryContainer.withOpacity(0.6),
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
                    'LANGKAH 2 DARI 2 • SELFIE MASUK',
                    style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                        letterSpacing: 1,
                        color: onSecondaryFixedVariant),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Verifikasi Wajah',
              style: TextStyle(
                  fontSize: 24, fontWeight: FontWeight.w800, letterSpacing: -0.5, color: onSurface),
            ),
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
                const SizedBox(width: 6),
                Text(
                  'Posisikan wajah Anda tegak lurus di dalam bingkai',
                  style: TextStyle(fontSize: 12, color: secondary),
                ),
              ],
            ),
            const SizedBox(height: 20),

            // 2. KOTAK KAMERA (VIEWFINDER)
            Container(
              width: double.infinity,
              constraints: const BoxConstraints(maxWidth: 340),
              // Rasio 4:5 menyesuaikan bentuk portrait
              child: AspectRatio(
                aspectRatio: 4 / 5,
                child: Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    color: surfaceContainerLow,
                    borderRadius: BorderRadius.circular(24),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.lightBlue.withOpacity(0.12),
                        blurRadius: 32,
                        offset: const Offset(0, 12),
                      )
                    ],
                  ),
                  child: Stack(
                    children: [
                      // Badge Kamera Depan & Wajah Siap (Atas)
                      Align(
                        alignment: Alignment.topCenter,
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 12, vertical: 6),
                              decoration: BoxDecoration(
                                color: surfaceContainerLowest.withOpacity(0.9),
                                borderRadius: BorderRadius.circular(20),
                              ),
                              child: Row(
                                children: [
                                  Icon(Icons.videocam,
                                      color: primary, size: 16),
                                  const SizedBox(width: 6),
                                  Text('KAMERA DEPAN',
                                      style: TextStyle(
                                          fontSize: 10,
                                          fontWeight: FontWeight.bold,
                                          color: onSurface)),
                                ],
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                  horizontal: 12, vertical: 6),
                              decoration: BoxDecoration(
                                color: surfaceContainerLowest.withOpacity(0.9),
                                borderRadius: BorderRadius.circular(20),
                              ),
                              child: Row(
                                children: [
                                  Container(
                                    width: 8,
                                    height: 8,
                                    decoration: BoxDecoration(
                                        color: primary, shape: BoxShape.circle),
                                  ),
                                  const SizedBox(width: 6),
                                  Text('Wajah Siap',
                                      style: TextStyle(
                                          fontSize: 10,
                                          fontWeight: FontWeight.bold,
                                          color: primary)),
                                ],
                              ),
                            )
                          ],
                        ),
                      ),

                      // Outline Deteksi Wajah (Tengah)
                      Align(
                        alignment: Alignment.center,
                        child: Stack(
                          alignment: Alignment.center,
                          children: [
                            // Garis Oval Statis
                            Container(
                              width: 150,
                              height: 200,
                              decoration: BoxDecoration(
                                border: Border.all(
                                    color: primary.withOpacity(0.6), width: 3),
                                borderRadius:
                                    const BorderRadius.all(Radius.elliptical(75, 100)),
                              ),
                            ),
                            // Garis Putus-putus (Berputar)
                            RotationTransition(
                              turns: _spinController,
                              child: CustomPaint(
                                size: const Size(160, 210),
                                painter: DashedOvalPainter(
                                    color: primary.withOpacity(0.6)),
                              ),
                            ),
                            // Indikator Posisi Mata
                            Positioned(
                              top: 80,
                              child: Row(
                                children: [
                                  Container(
                                    width: 16,
                                    height: 2,
                                    color: primary.withOpacity(0.4),
                                  ),
                                  const SizedBox(width: 50),
                                  Container(
                                    width: 16,
                                    height: 2,
                                    color: primary.withOpacity(0.4),
                                  )
                                ],
                              ),
                            ),
                            // Badge Pencahayaan Baik (Tengah Bawah)
                            Positioned(
                              bottom: -25,
                              child: Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 12, vertical: 4),
                                decoration: BoxDecoration(
                                  color: surfaceContainerLowest.withOpacity(0.8),
                                  borderRadius: BorderRadius.circular(20),
                                ),
                                child: Row(
                                  children: [
                                    Icon(Icons.light_mode,
                                        color: secondary, size: 14),
                                    const SizedBox(width: 4),
                                    Text('Pencahayaan Baik',
                                        style: TextStyle(
                                            fontSize: 10,
                                            fontWeight: FontWeight.bold,
                                            color: secondary)),
                                  ],
                                ),
                              ),
                            )
                          ],
                        ),
                      ),

                      // Badge Validasi Bawah
                      Align(
                        alignment: Alignment.bottomCenter,
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 12, vertical: 8),
                          decoration: BoxDecoration(
                            color: surfaceContainerLowest.withOpacity(0.85),
                            borderRadius: BorderRadius.circular(16),
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Row(
                                children: [
                                  Icon(Icons.verified_user,
                                      color: primary, size: 18),
                                  const SizedBox(width: 8),
                                  Column(
                                    mainAxisSize: MainAxisSize.min,
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text('Validasi Otomatis',
                                          style: TextStyle(
                                              fontSize: 10,
                                              fontWeight: FontWeight.bold,
                                              color: onSurface)),
                                      Text('Anti Fake-GPS & Masker',
                                          style: TextStyle(
                                              fontSize: 10, color: secondary)),
                                    ],
                                  ),
                                ],
                              ),
                              Container(
                                width: 8,
                                height: 8,
                                decoration: BoxDecoration(
                                    color: primary, shape: BoxShape.circle),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
            const SizedBox(height: 16),

            // 3. KARTU TELEMETRI (Jam dan Lokasi)
            Container(
              width: double.infinity,
              constraints: const BoxConstraints(maxWidth: 340),
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: surfaceContainerLowest,
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(
                    color: Colors.lightBlue.withOpacity(0.05),
                    blurRadius: 16,
                    offset: const Offset(0, 4),
                  )
                ],
              ),
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          Icon(Icons.schedule, color: primary, size: 16),
                          const SizedBox(width: 6),
                          Text(
                            _currentTime, // Nilai jam Realtime
                            style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: onSurface),
                          ),
                        ],
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 8, vertical: 2),
                        decoration: BoxDecoration(
                          color: surfaceContainer,
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          'Shift Pagi',
                          style: TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: secondary),
                        ),
                      )
                    ],
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Icon(Icons.location_on, color: primary, size: 16),
                      const SizedBox(width: 6),
                      Expanded(
                        child: Text(
                          'Warkop Cak Kebo • Akurasi 3m',
                          style: TextStyle(fontSize: 12, color: onSurface),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                      Icon(Icons.check_circle, color: primary, size: 14),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // 4. KONTROL KAMERA BAWAH
            Container(
              width: double.infinity,
              constraints: const BoxConstraints(maxWidth: 340),
              padding: const EdgeInsets.symmetric(horizontal: 24),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  // Tombol Flash
                  Container(
                    width: 48,
                    height: 48,
                    decoration: BoxDecoration(
                      color: surfaceContainerLowest,
                      shape: BoxShape.circle,
                      boxShadow: [
                        BoxShadow(color: Colors.black.withOpacity(0.08), blurRadius: 12)
                      ]
                    ),
                    child: IconButton(
                      icon: Icon(Icons.flash_on, color: secondary),
                      onPressed: () {},
                    ),
                  ),

                  // Tombol Shutter Utama
                  GestureDetector(
                    onTap: _handleShutter,
                    child: Container(
                      width: 80,
                      height: 80,
                      padding: const EdgeInsets.all(4),
                      decoration: BoxDecoration(
                        color: surfaceContainerLowest,
                        shape: BoxShape.circle,
                        boxShadow: [
                          BoxShadow(
                              color: primary.withOpacity(0.22),
                              blurRadius: 32,
                              offset: const Offset(0, 16))
                        ],
                      ),
                      child: Container(
                        padding: const EdgeInsets.all(6),
                        decoration: BoxDecoration(
                            color: primaryFixed, shape: BoxShape.circle),
                        child: Container(
                          decoration: BoxDecoration(
                              color: primary, shape: BoxShape.circle),
                          child: Icon(Icons.photo_camera,
                              color: onPrimary, size: 30),
                        ),
                      ),
                    ),
                  ),

                  // Tombol Flip Kamera
                  Container(
                    width: 48,
                    height: 48,
                    decoration: BoxDecoration(
                      color: surfaceContainerLowest,
                      shape: BoxShape.circle,
                      boxShadow: [
                        BoxShadow(color: Colors.black.withOpacity(0.08), blurRadius: 12)
                      ]
                    ),
                    child: IconButton(
                      icon: Icon(Icons.flip_camera_ios, color: secondary),
                      onPressed: () {},
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // 5. TOMBOL BATAL BAWAH
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text(
                'Kembali ke Validasi Lokasi',
                style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                    color: secondary),
              ),
            ),
            const SizedBox(height: 16),
          ],
        ),
        ),
      ),
    ),
    );
  }
}

// Custom Painter untuk Membuat Garis Oval Putus-Putus Berputar
class DashedOvalPainter extends CustomPainter {
  final Color color;
  DashedOvalPainter({required this.color});

  @override
  void paint(Canvas canvas, Size size) {
    Paint paint = Paint()
      ..color = color
      ..strokeWidth = 2
      ..style = PaintingStyle.stroke;
    
    // Konfigurasi manual untuk bentuk putus-putus (dash array)
    double dashWidth = 8, dashSpace = 8;
    double perimeter = 2 * 3.14159 * ((size.width / 2 + size.height / 2) / 2);
    int dashCount = (perimeter / (dashWidth + dashSpace)).floor();
    
    Path path = Path()..addOval(Rect.fromLTWH(0, 0, size.width, size.height));
    Path dashPath = Path();
    
    for (PathMetric measurePath in path.computeMetrics()) {
      double distance = 0;
      while (distance < measurePath.length) {
        dashPath.addPath(
          measurePath.extractPath(distance, distance + dashWidth),
          Offset.zero,
        );
        distance += dashWidth;
        distance += dashSpace;
      }
    }
    canvas.drawPath(dashPath, paint);
  }

  @override
  bool shouldRepaint(CustomPainter oldDelegate) => false;
}