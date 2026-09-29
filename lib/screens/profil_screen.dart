import 'package:flutter/material.dart';
import '../services/app_session.dart';
import '../theme/stitch_theme.dart';

class ProfilScreen extends StatelessWidget {
  const ProfilScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final user = AppSession.instance.user;
    return Scaffold(
      appBar: AppBar(
        backgroundColor: StitchTheme.surfaceWhite,
        elevation: 0,
        title: const Text('Profil Karyawan', style: TextStyle(color: StitchTheme.textDark, fontWeight: FontWeight.bold)),
      ),
      body: Padding(
        padding: const EdgeInsets.all(20.0),
        child: Column(
          children: [
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Row(
                  children: [
                    CircleAvatar(radius: 28, backgroundColor: StitchTheme.backgroundLight, child: Icon(Icons.person, color: StitchTheme.primaryGreen, size: 30)),
                    SizedBox(width: 16),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(user?.name ?? 'Pengguna', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: StitchTheme.textDark)),
                        const SizedBox(height: 4),
                        Text('${user?.id ?? '-'} • ${user?.role ?? '-'}', style: const TextStyle(color: StitchTheme.textMuted, fontSize: 13)),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            if (user?.isCashier == true && AppSession.instance.activeShift != null) ...[
              const SizedBox(height: 12),
              Card(
                child: ListTile(
                  leading: const Icon(Icons.schedule_rounded, color: StitchTheme.primaryGreen),
                  title: const Text('Shift sedang aktif', style: TextStyle(fontWeight: FontWeight.bold)),
                  subtitle: Text(AppSession.instance.activeShift!.label),
                  trailing: const Icon(Icons.check_circle, color: StitchTheme.primaryGreen),
                ),
              ),
            ],
            const Spacer(),
            SizedBox(
              width: double.infinity,
              height: 52,
              child: OutlinedButton(
                style: OutlinedButton.styleFrom(
                  side: const BorderSide(color: Colors.redAccent),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                ),
                onPressed: AppSession.instance.logout,
                child: const Text('Keluar dari Akun', style: TextStyle(color: Colors.redAccent, fontWeight: FontWeight.bold, fontSize: 15)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}