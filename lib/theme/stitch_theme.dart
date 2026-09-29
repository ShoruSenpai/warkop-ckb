import 'package:flutter/material.dart';

class StitchTheme {
  static const Color primaryGreen = Color(0xFF0D6837);
  static const Color backgroundLight = Color(0xFFF8FAF9);
  static const Color surfaceWhite = Colors.white;
  static const Color textDark = Color(0xFF1E2428);
  static const Color textMuted = Color(0xFF64748B);
  static const Color accentWarning = Color(0xFFC27803);
  static const Color borderSubtle = Color(0xFFE2E8F0);

  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      scaffoldBackgroundColor: backgroundLight,
      primaryColor: primaryGreen,
      fontFamily: 'Roboto',
      colorScheme: ColorScheme.fromSeed(
        seedColor: primaryGreen,
        primary: primaryGreen,
      ),
      cardTheme: CardThemeData(
        color: surfaceWhite,
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: const BorderSide(color: borderSubtle, width: 1),
        ),
      ),
    );
  }
}