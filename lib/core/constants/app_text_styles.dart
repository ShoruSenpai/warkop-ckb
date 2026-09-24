import 'package:flutter/material.dart';
import 'app_colors.dart';

/// Skala tipografi mengikuti fontSize pada konfigurasi Tailwind di HTML asli.
/// Font "Inter" -> di Flutter pakai GoogleFonts.inter() atau daftarkan lewat
/// pubspec (lihat catatan di README).
class AppTextStyles {
  AppTextStyles._();

  static const _fontFamily = 'Inter';

  static const headlineLg = TextStyle(
    fontFamily: _fontFamily,
    fontSize: 28,
    height: 34 / 28,
    fontWeight: FontWeight.w700,
    letterSpacing: -0.02 * 28,
    color: AppColors.onSurface,
  );

  static const headlineMd = TextStyle(
    fontFamily: _fontFamily,
    fontSize: 22,
    height: 28 / 22,
    fontWeight: FontWeight.w600,
    color: AppColors.onSurface,
  );

  static const headlineSm = TextStyle(
    fontFamily: _fontFamily,
    fontSize: 18,
    height: 24 / 18,
    fontWeight: FontWeight.w600,
    color: AppColors.onSurface,
  );

  static const bodyLg = TextStyle(
    fontFamily: _fontFamily,
    fontSize: 16,
    height: 24 / 16,
    fontWeight: FontWeight.w400,
    color: AppColors.onSurface,
  );

  static const bodyMd = TextStyle(
    fontFamily: _fontFamily,
    fontSize: 14,
    height: 20 / 14,
    fontWeight: FontWeight.w400,
    color: AppColors.onSurface,
  );

  static const bodySm = TextStyle(
    fontFamily: _fontFamily,
    fontSize: 12,
    height: 16 / 12,
    fontWeight: FontWeight.w400,
    color: AppColors.onSurfaceVariant,
  );

  static const labelLg = TextStyle(
    fontFamily: _fontFamily,
    fontSize: 14,
    height: 20 / 14,
    fontWeight: FontWeight.w700,
    color: AppColors.onSurface,
  );

  static const labelMd = TextStyle(
    fontFamily: _fontFamily,
    fontSize: 12,
    height: 16 / 12,
    fontWeight: FontWeight.w600,
    color: AppColors.onSurface,
  );

  static const labelSm = TextStyle(
    fontFamily: _fontFamily,
    fontSize: 11,
    height: 14 / 11,
    fontWeight: FontWeight.w600,
    color: AppColors.onSurfaceVariant,
  );
}
