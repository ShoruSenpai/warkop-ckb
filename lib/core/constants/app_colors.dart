import 'package:flutter/material.dart';

/// Warna diambil dari konfigurasi Tailwind pada HTML asli.
class AppColors {
  AppColors._();

  static const primary = Color(0xFF006C49);
  static const primaryContainer = Color(0xFF10B981);
  static const onPrimary = Color(0xFFFFFFFF);
  static const onPrimaryContainer = Color(0xFF00422B);

  static const surface = Color(0xFFF7F9FB);
  static const surfaceContainerLowest = Color(0xFFFFFFFF);
  static const surfaceContainer = Color(0xFFECEEF0);
  static const surfaceContainerHigh = Color(0xFFE6E8EA);

  static const onSurface = Color(0xFF191C1E);
  static const onSurfaceVariant = Color(0xFF3C4A42);

  static const outlineVariant = Color(0xFFE2E8F0);

  static const error = Color(0xFFBA1A1A);

  // Status badge (emerald / amber) — dipakai untuk label "Tepat Waktu",
  // "Radius Outlet Valid", toleransi jam, dsb.
  static const emerald50 = Color(0xFFECFDF5);
  static const emerald200 = Color(0xFFA7F3D0);
  static const emerald800 = Color(0xFF065F46);
  static const emerald950 = Color(0xFF022C22);
  static const amber800 = Color(0xFF92400E);
}
