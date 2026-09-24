import 'package:flutter/material.dart';
import 'core/constants/app_colors.dart';
import 'pages/absensi/absensi_page.dart';
import 'pages/absensi/attendance_success.dart';
import 'pages/absensi/face_verification_page.dart';
import 'pages/absensi/gps_verification_page.dart';
import 'pages/placeholder_page.dart';

void main() => runApp(const CafeOpsApp());

class CafeOpsApp extends StatelessWidget {
  const CafeOpsApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Warkop Cak Kebo - Absensi',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        scaffoldBackgroundColor: AppColors.surface,
        colorScheme: ColorScheme.fromSeed(
          seedColor: AppColors.primary,
          primary: AppColors.primary,
        ),
        fontFamily: 'Inter',
        useMaterial3: true,
      ),
      initialRoute: '/',
      routes: {
        '/': (_) => const AbsensiPage(),
        '/gps-verification': (_) => const AttendancePage(),
        '/face-verification': (_) => const FaceVerificationPage(),
        '/attendance-success': (_) => const AttendanceSuccessPage(),
        '/jadwal': (_) => const PlaceholderPage(
              navIndex: 1,
              title: 'Jadwal',
              description: 'Modul jadwal belum tersedia di versi ini.',
              icon: Icons.calendar_month,
            ),
        '/pos': (_) => const PlaceholderPage(
              navIndex: 2,
              title: 'POS',
              description: 'Modul POS belum tersedia di versi ini.',
              icon: Icons.point_of_sale,
            ),
        '/profil': (_) => const PlaceholderPage(
              navIndex: 3,
              title: 'Profil',
              description: 'Modul profil belum tersedia di versi ini.',
              icon: Icons.account_circle,
            ),
      },
    );
  }
}
