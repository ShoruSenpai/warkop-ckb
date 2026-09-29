import 'dart:convert';

import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;

import '../models/absensi_model.dart';

class ApiService {
  static const String supabaseUrl = String.fromEnvironment('SUPABASE_URL');
  static const String supabaseKey = String.fromEnvironment(
    'SUPABASE_PUBLISHABLE_KEY',
    defaultValue: String.fromEnvironment('SUPABASE_ANON_KEY'),
  );

  static bool get isSupabaseConfigured {
    final uri = Uri.tryParse(supabaseUrl);
    return uri != null &&
        (uri.scheme == 'https' || uri.scheme == 'http') &&
        uri.host.isNotEmpty &&
        (supabaseKey.startsWith('sb_publishable_') ||
            supabaseKey.startsWith('eyJ'));
  }

  static Map<String, String> get _restHeaders => {
    'apikey': supabaseKey,
    if (!supabaseKey.startsWith('sb_publishable_'))
      'Authorization': 'Bearer $supabaseKey',
  };

  static Uri get _attendanceEndpoint => Uri.parse(
    '${supabaseUrl.replaceFirst(RegExp(r'/+$'), '')}/rest/v1/attendance',
  );

  static Future<bool> kirimAbsensi({
    required String karyawanId,
    required String tipeAbsen,
    required double latitude,
    required double longitude,
    required String fotoBase64,
  }) async {
    if (!isSupabaseConfigured) {
      throw const ApiException(
        'Supabase belum dikonfigurasi. Isi SUPABASE_URL dan SUPABASE_PUBLISHABLE_KEY.',
      );
    }

    try {
      final response = await http.post(
        _attendanceEndpoint,
        headers: {
          ..._restHeaders,
          'Content-Type': 'application/json',
          'Prefer': 'return=minimal',
        },
        body: jsonEncode({
          'karyawan_id': karyawanId,
          'tipe_absen': tipeAbsen,
          'latitude': latitude,
          'longitude': longitude,
          'foto': fotoBase64,
          'waktu': DateTime.now().toIso8601String(),
        }),
      );

      if (response.statusCode >= 200 && response.statusCode < 300) return true;

      throw ApiException.fromResponse(response.statusCode, response.body);
    } catch (e) {
      if (e is ApiException) rethrow;
      debugPrint('Error koneksi database: $e');
      throw ApiException('Tidak dapat terhubung ke API absensi: $e');
    }
  }

  static Future<List<AbsensiRecord>> riwayatAbsensi({
    required String karyawanId,
  }) async {
    if (!isSupabaseConfigured) return const [];

    try {
      final response = await http.get(
        _attendanceEndpoint.replace(
          queryParameters: {
            'select': 'tipe_absen,waktu',
            'karyawan_id': 'eq.$karyawanId',
            'order': 'waktu.desc',
            'limit': '3',
          },
        ),
        headers: _restHeaders,
      );

      if (response.statusCode < 200 || response.statusCode >= 300) {
        throw ApiException.fromResponse(response.statusCode, response.body);
      }

      final rows = jsonDecode(response.body) as List<dynamic>;
      return rows
          .map((row) => AbsensiRecord.fromJson(row as Map<String, dynamic>))
          .toList();
    } catch (error) {
      if (error is ApiException) rethrow;
      debugPrint('Error mengambil riwayat absensi: $error');
      throw ApiException('Tidak dapat memuat riwayat absensi: $error');
    }
  }
}

class ApiException implements Exception {
  const ApiException(this.message);

  factory ApiException.fromResponse(int statusCode, String body) {
    try {
      final response = jsonDecode(body) as Map<String, dynamic>;
      if (response['code'] == 'PGRST205') {
        return const ApiException(
          'Tabel public.attendance belum dibuat. Jalankan '
          'supabase/migrations/20260927000000_create_attendance.sql '
          'di Supabase SQL Editor.',
        );
      }
    } on FormatException {
      // Keep the original response for non-JSON errors.
    } on TypeError {
      // Keep the original response if Supabase returns a non-object JSON value.
    }

    return ApiException('API absensi menolak data ($statusCode): $body');
  }

  final String message;

  @override
  String toString() => message;
}
