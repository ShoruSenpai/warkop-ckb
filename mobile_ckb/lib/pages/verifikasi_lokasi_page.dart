import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class VerifikasiLokasiScreen extends StatefulWidget {
  const VerifikasiLokasiScreen({super.key});

  @override
  State<VerifikasiLokasiScreen> createState() => _VerifikasiLokasiScreenState();
}

class _VerifikasiLokasiScreenState extends State<VerifikasiLokasiScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _pulseController;
  late Animation<double> _pulseAnimation;

  // Konstanta Warna Tema
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
  final Color successColor = const Color(0xFF0D9488);

  bool _isValidating = false;

  @override
  void initState() {
    super.initState();
    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 2),
    )..repeat(reverse: true);

    _pulseAnimation = Tween<double>(begin: 0.95, end: 1.08).animate(
      CurvedAnimation(parent: _pulseController, curve: Curves.easeInOut),
    );
  }

  @override
  void dispose() {
    _pulseController.dispose();
    super.dispose();
  }

  void _proceedToFaceVerification() {
    setState(() => _isValidating = true);
    Future.delayed(const Duration(milliseconds: 600), () {
      if (!mounted) return;
      setState(() => _isValidating = false);
      // Navigasi ke Langkah 2: Verifikasi Wajah
      Navigator.pushNamed(context, '/verifikasi_wajah');
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: surface,
      // ==========================================
      // APP BAR
      // ==========================================
      appBar: AppBar(
        backgroundColor: surface.withOpacity(0.85),
        elevation: 0,
        leading: IconButton(
          icon: Icon(Icons.arrow_back_ios_new, color: onSurface, size: 20),
          onPressed: () => Navigator.pop(context),
        ),
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
            const SizedBox(width: 10),
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'CAK KEBO',
                  style: GoogleFonts.inter(
                    fontSize: 10,
                    fontWeight: FontWeight.w700,
                    letterSpacing: 1.5,
                    color: secondary,
                  ),
                ),
                Text(
                  'Absensi Staf',
                  style: GoogleFonts.inter(
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                    color: onSurface,
                  ),
                ),
              ],
            ),
          ],
        ),
        actions: [
          Container(
            margin: const EdgeInsets.only(right: 16),
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: const Color(0xFFDCFCE7),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: const Color(0xFF86EFAC)),
            ),
            child: Row(
              children: [
                Container(
                  width: 6,
                  height: 6,
                  decoration: const BoxDecoration(
                    color: Color(0xFF16A34A),
                    shape: BoxShape.circle,
                  ),
                ),
                const SizedBox(width: 6),
                Text(
                  'GPS AKTIF',
                  style: GoogleFonts.inter(
                    fontSize: 10,
                    fontWeight: FontWeight.w700,
                    color: const Color(0xFF15803D),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),

      // ==========================================
      // BODY KONTEN
      // ==========================================
      body: LayoutBuilder(
        builder: (context, constraints) {
          final isDesktop = constraints.maxWidth > 500;
          return Center(
            child: SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
              child: SizedBox(
                width: isDesktop ? 440 : double.infinity,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    // 1. STEPPER & HEADLINE
                    Container(
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 5),
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
                    decoration: BoxDecoration(color: primary, shape: BoxShape.circle),
                  ),
                  const SizedBox(width: 8),
                  Text(
                    'LANGKAH 1 DARI 2 • VALIDASI LOKASI',
                    style: GoogleFonts.inter(
                      fontSize: 10,
                      fontWeight: FontWeight.w700,
                      letterSpacing: 1,
                      color: onSecondaryFixedVariant,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 10),
            Text(
              'Verifikasi Lokasi Outlet',
              style: GoogleFonts.inter(
                fontSize: 24,
                fontWeight: FontWeight.w800,
                letterSpacing: -0.5,
                color: onSurface,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              'Pastikan perangkat berada dalam radius operasional outlet Cak Kebo',
              textAlign: TextAlign.center,
              style: GoogleFonts.inter(
                fontSize: 12,
                fontWeight: FontWeight.w400,
                color: secondary,
              ),
            ),
            const SizedBox(height: 20),

            // 2. RADAR & VISUAL GEOFENCE CARD
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: surfaceContainerLowest,
                borderRadius: BorderRadius.circular(24),
                boxShadow: [
                  BoxShadow(
                    color: primary.withOpacity(0.08),
                    blurRadius: 24,
                    offset: const Offset(0, 8),
                  ),
                ],
              ),
              child: Column(
                children: [
                  // Visual Radar Geofence
                  Stack(
                    alignment: Alignment.center,
                    children: [
                      // Lingkaran Luar (Batas Radius 50m)
                      ScaleTransition(
                        scale: _pulseAnimation,
                        child: Container(
                          width: 200,
                          height: 200,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: primary.withOpacity(0.05),
                            border: Border.all(
                              color: primary.withOpacity(0.2),
                              width: 1.5,
                            ),
                          ),
                        ),
                      ),
                      // Lingkaran Tengah
                      Container(
                        width: 140,
                        height: 140,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: secondaryContainer.withOpacity(0.35),
                          border: Border.all(
                            color: primary.withOpacity(0.3),
                            width: 1,
                          ),
                        ),
                      ),
                      // Lingkaran Inti (Posisi User)
                      Container(
                        width: 72,
                        height: 72,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: primary,
                          boxShadow: [
                            BoxShadow(
                              color: primary.withOpacity(0.35),
                              blurRadius: 18,
                              offset: const Offset(0, 6),
                            ),
                          ],
                        ),
                        child: const Icon(
                          Icons.location_on,
                          color: Colors.white,
                          size: 36,
                        ),
                      ),
                      // Label Status Mengambang
                      Positioned(
                        bottom: 4,
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: 12, vertical: 4),
                          decoration: BoxDecoration(
                            color: successColor,
                            borderRadius: BorderRadius.circular(16),
                            boxShadow: [
                              BoxShadow(
                                color: successColor.withOpacity(0.3),
                                blurRadius: 8,
                                offset: const Offset(0, 3),
                              ),
                            ],
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              const Icon(Icons.check_circle,
                                  color: Colors.white, size: 14),
                              const SizedBox(width: 5),
                              Text(
                                'Dalam Radius (12m / 50m)',
                                style: GoogleFonts.inter(
                                  fontSize: 11,
                                  fontWeight: FontWeight.w700,
                                  color: Colors.white,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),

                  // Detail Informasi Outlet & Koordinat
                  Container(
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: surfaceContainerLow,
                      borderRadius: BorderRadius.circular(16),
                    ),
                    child: Column(
                      children: [
                        _buildInfoRow(
                          icon: Icons.store_mall_directory_outlined,
                          title: 'Outlet Terdeteksi',
                          value: 'Warkop Cak Kebo - Outlet Utama',
                        ),
                        const Divider(height: 18, thickness: 0.8),
                        _buildInfoRow(
                          icon: Icons.near_me_outlined,
                          title: 'Jarak ke Mesin Kasir',
                          value: '12 Meter (Aman • Diizinkan)',
                          valueColor: successColor,
                        ),
                        const Divider(height: 18, thickness: 0.8),
                        _buildInfoRow(
                          icon: Icons.my_location_outlined,
                          title: 'Koordinat GPS',
                          value: '-7.2654° S, 112.7483° E',
                        ),
                        const Divider(height: 18, thickness: 0.8),
                        _buildInfoRow(
                          icon: Icons.wifi,
                          title: 'Jaringan Outlet',
                          value: 'CKB-Staff-5G (Terhubung)',
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 24),

            // 3. TOMBOL LANJUT KE VERIFIKASI WAJAH
            Container(
              width: double.infinity,
              height: 56,
              child: ElevatedButton(
                onPressed: _isValidating ? null : _proceedToFaceVerification,
                style: ElevatedButton.styleFrom(
                  backgroundColor: primary,
                  foregroundColor: Colors.white,
                  elevation: 2,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(30),
                  ),
                ),
                child: _isValidating
                    ? const SizedBox(
                        width: 22,
                        height: 22,
                        child: CircularProgressIndicator(
                          color: Colors.white,
                          strokeWidth: 2.5,
                        ),
                      )
                    : Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(
                            'Lanjut ke Verifikasi Wajah',
                            style: GoogleFonts.inter(
                              fontSize: 15,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                          const SizedBox(width: 8),
                          const Icon(Icons.face_retouching_natural, size: 20),
                        ],
                      ),
              ),
            ),
            const SizedBox(height: 12),

            // 4. TOMBOL BATAL / KEMBALI KE BERANDA
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: Text(
                'Batal & Kembali ke Beranda',
                style: GoogleFonts.inter(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: secondary,
                ),
              ),
            ),
            const SizedBox(height: 16),
          ],
        ),
      ),
    ),
  );
},
),
);
  }

  Widget _buildInfoRow({
    required IconData icon,
    required String title,
    required String value,
    Color? valueColor,
  }) {
    return Row(
      children: [
        Icon(icon, color: primary, size: 18),
        const SizedBox(width: 10),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: GoogleFonts.inter(
                  fontSize: 10,
                  fontWeight: FontWeight.w600,
                  color: onSurfaceVariant,
                ),
              ),
              const SizedBox(height: 1),
              Text(
                value,
                style: GoogleFonts.inter(
                  fontSize: 12,
                  fontWeight: FontWeight.w700,
                  color: valueColor ?? onSurface,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}
