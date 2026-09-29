import 'package:flutter/material.dart';

import '../theme/stitch_theme.dart';
import '../services/app_session.dart';
import 'absensi_home_screen.dart';
import 'jadwal_screen.dart';
import 'pos_screen.dart';
import 'profil_screen.dart';

class MainNavigationScreen extends StatefulWidget {
  const MainNavigationScreen({super.key});

  @override
  State<MainNavigationScreen> createState() => _MainNavigationScreenState();
}

class _MainNavigationScreenState extends State<MainNavigationScreen> {
  int _currentIndex = 0;

  Future<void> _selectTab(int index, {required bool isCashier}) async {
    if (isCashier && index == 2) {
      final accessGranted = await showDialog<bool>(
        context: context,
        builder: (_) =>
            _PosPinDialog(verifyPin: AppSession.instance.verifyPosPin),
      );
      if (accessGranted != true) return;
    }

    if (mounted) setState(() => _currentIndex = index);
  }

  @override
  Widget build(BuildContext context) {
    final user = AppSession.instance.user;
    final isCashier = user?.isCashier == true;
    final screens = [
      const AbsensiHomeScreen(),
      const JadwalScreen(),
      if (isCashier) const PosScreen(),
      const ProfilScreen(),
    ];
    final navigationItems = <BottomNavigationBarItem>[
      const BottomNavigationBarItem(
        icon: Icon(Icons.how_to_reg_rounded),
        label: 'Absensi',
      ),
      const BottomNavigationBarItem(
        icon: Icon(Icons.calendar_month_rounded),
        label: 'Jadwal',
      ),
      if (isCashier)
        const BottomNavigationBarItem(
          icon: Icon(Icons.point_of_sale_rounded),
          label: 'POS',
        ),
      const BottomNavigationBarItem(
        icon: Icon(Icons.person_rounded),
        label: 'Profil',
      ),
    ];
    return Scaffold(
      body: IndexedStack(index: _currentIndex, children: screens),
      bottomNavigationBar: BottomNavigationBar(
        currentIndex: _currentIndex,
        selectedItemColor: StitchTheme.primaryGreen,
        unselectedItemColor: StitchTheme.textMuted,
        type: BottomNavigationBarType.fixed,
        backgroundColor: StitchTheme.surfaceWhite,
        onTap: (index) => _selectTab(index, isCashier: isCashier),
        items: navigationItems,
      ),
    );
  }
}

class _PosPinDialog extends StatefulWidget {
  const _PosPinDialog({required this.verifyPin});

  final bool Function(String) verifyPin;

  @override
  State<_PosPinDialog> createState() => _PosPinDialogState();
}

class _PosPinDialogState extends State<_PosPinDialog> {
  final _pinController = TextEditingController();
  String? _error;

  @override
  void dispose() {
    _pinController.dispose();
    super.dispose();
  }

  void _submit() {
    if (widget.verifyPin(_pinController.text)) {
      Navigator.pop(context, true);
    } else {
      setState(() => _error = 'PIN kasir tidak valid.');
    }
  }

  @override
  Widget build(BuildContext context) => AlertDialog(
    title: const Text('Verifikasi PIN POS'),
    content: TextField(
      key: const ValueKey('pos-pin-input'),
      controller: _pinController,
      keyboardType: TextInputType.number,
      maxLength: 6,
      obscureText: true,
      autofocus: true,
      onSubmitted: (_) => _submit(),
      decoration: InputDecoration(
        labelText: 'PIN kasir',
        prefixIcon: const Icon(Icons.lock_outline),
        errorText: _error,
      ),
    ),
    actions: [
      TextButton(
        onPressed: () => Navigator.pop(context, false),
        child: const Text('Batal'),
      ),
      FilledButton(onPressed: _submit, child: const Text('Buka POS')),
    ],
  );
}
