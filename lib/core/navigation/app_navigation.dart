import 'package:flutter/material.dart';

class AppNavigation {
  AppNavigation._();

  static const routes = <String>[
    '/',
    '/jadwal',
    '/pos',
    '/profil',
  ];

  static void handleBottomNav(BuildContext context, int index) {
    if (index < 0 || index >= routes.length) return;

    final targetRoute = routes[index];
    final currentRoute = ModalRoute.of(context)?.settings.name;

    if (currentRoute == targetRoute) return;

    Navigator.of(context).pushNamedAndRemoveUntil(
      targetRoute,
      (route) => false,
    );
  }
}
