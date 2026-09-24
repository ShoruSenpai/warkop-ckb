import 'package:flutter/material.dart';
import 'core/constants/app_colors.dart';
import 'pages/absensi/absensi_page.dart';

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
      home: const AbsensiPage(),
    );
  }
}
