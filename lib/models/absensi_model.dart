class AbsensiRecord {
  const AbsensiRecord({required this.tipe, required this.waktu});

  factory AbsensiRecord.fromJson(Map<String, dynamic> json) {
    return AbsensiRecord(
      tipe: json['tipe_absen'] as String? ?? 'Absensi',
      waktu: DateTime.parse(json['waktu'] as String).toLocal(),
    );
  }

  final String tipe;
  final DateTime waktu;
}
