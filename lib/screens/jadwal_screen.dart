import 'package:flutter/material.dart';
import '../services/app_data.dart';
import '../services/app_session.dart';
import '../theme/stitch_theme.dart';

class JadwalScreen extends StatelessWidget {
  const JadwalScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final user = AppSession.instance.user;
    final userShifts = user == null ? shifts : shifts.where((shift) => shift.name == user.name || user.isCashier && shift.role == 'Kasir').toList();
    return Scaffold(
      appBar: AppBar(title: const Text('Jadwal Shift'), actions: [IconButton(onPressed: () {}, icon: const Icon(Icons.filter_list_rounded), tooltip: 'Filter')]),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          Text(user?.isCashier == true ? 'Shift operasional kasir' : 'Jadwal tim outlet', style: const TextStyle(fontSize: 22, fontWeight: FontWeight.bold, color: StitchTheme.textDark)),
          const SizedBox(height: 6),
          const Text('Cabang Senopati • September 2026', style: TextStyle(color: StitchTheme.textMuted)),
          const SizedBox(height: 20),
          ...userShifts.map((shift) => Card(
                margin: const EdgeInsets.only(bottom: 12),
                child: ListTile(
                  contentPadding: const EdgeInsets.symmetric(horizontal: 18, vertical: 8),
                  leading: CircleAvatar(backgroundColor: shift.role == 'Kasir' ? const Color(0xFFE8F1FF) : const Color(0xFFE6F5EC), child: Icon(shift.role == 'Kasir' ? Icons.point_of_sale : Icons.coffee, color: StitchTheme.primaryGreen)),
                  title: Text(shift.name, style: const TextStyle(fontWeight: FontWeight.bold)),
                  subtitle: Text('${shift.role} • ${shift.day}\n${shift.time}'),
                  isThreeLine: true,
                  trailing: Text(shift.status, style: TextStyle(color: shift.status == 'Aktif' ? StitchTheme.primaryGreen : StitchTheme.textMuted, fontWeight: FontWeight.w700)),
                ),
              )),
        ],
      ),
    );
  }
}
