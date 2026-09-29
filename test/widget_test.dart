// This is a basic Flutter widget test.
//
// To perform an interaction with a widget in your test, use the WidgetTester
// utility in the flutter_test package. For example, you can send tap and scroll
// gestures. You can also use WidgetTester to find child widgets in the widget
// tree, read text, and verify that the values of widget properties are correct.

import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';

import 'package:warkop_try/main.dart';
import 'package:warkop_try/services/api_service.dart';
import 'package:warkop_try/services/app_session.dart';

void main() {
  setUp(() => AppSession.instance.logout());

  testWidgets('Aplikasi membuka halaman login', (WidgetTester tester) async {
    await tester.pumpWidget(const MyApp());

    expect(find.text('Cak Kebo'), findsOneWidget);
    expect(find.text('Username'), findsOneWidget);
    expect(find.text('Password'), findsOneWidget);
    expect(find.text('Masuk ke Aplikasi'), findsOneWidget);
  });

  test('PGRST205 memberi petunjuk menjalankan migrasi attendance', () {
    final error = ApiException.fromResponse(
      404,
      '{"code":"PGRST205","message":"Could not find table"}',
    );

    expect(error.toString(), contains('public.attendance belum dibuat'));
    expect(error.toString(), contains('20260927000000_create_attendance.sql'));
  });

  testWidgets('Username menentukan sesi karyawan atau kasir', (
    WidgetTester tester,
  ) async {
    await tester.pumpWidget(const MyApp());

    await tester.enterText(
      find.byKey(const ValueKey('login-username')),
      'raka',
    );
    await tester.enterText(
      find.byKey(const ValueKey('login-password')),
      '123456',
    );
    await tester.tap(find.text('Masuk ke Aplikasi'));
    await tester.pumpAndSettle();

    expect(AppSession.instance.user?.isCashier, isTrue);
  });

  testWidgets('Tab POS hanya muncul untuk karyawan kasir', (
    WidgetTester tester,
  ) async {
    AppSession.instance.login('alisa', '123456');
    await tester.pumpWidget(const MyApp());

    expect(find.text('POS'), findsNothing);
  });

  testWidgets('PIN wajib benar sebelum POS dibuka', (
    WidgetTester tester,
  ) async {
    AppSession.instance.login('raka', '123456');
    await tester.pumpWidget(const MyApp());

    await tester.tap(find.text('POS'));
    await tester.pumpAndSettle();
    await tester.enterText(
      find.byKey(const ValueKey('pos-pin-input')),
      '111111',
    );
    await tester.tap(find.text('Buka POS'));
    await tester.pumpAndSettle();
    expect(find.text('PIN kasir tidak valid.'), findsOneWidget);

    await tester.enterText(
      find.byKey(const ValueKey('pos-pin-input')),
      '123456',
    );
    await tester.tap(find.text('Buka POS'));
    await tester.pumpAndSettle();
    expect(find.text('POS Kasir'), findsOneWidget);
  });

  testWidgets('POS menyesuaikan layar ponsel', (WidgetTester tester) async {
    tester.view.physicalSize = const Size(390, 844);
    tester.view.devicePixelRatio = 1;
    addTearDown(tester.view.resetPhysicalSize);
    addTearDown(tester.view.resetDevicePixelRatio);
    AppSession.instance.login('raka', '123456');
    await tester.pumpWidget(const MyApp());

    await tester.tap(find.text('POS'));
    await tester.pumpAndSettle();
    await tester.enterText(
      find.byKey(const ValueKey('pos-pin-input')),
      '123456',
    );
    await tester.tap(find.text('Buka POS'));
    await tester.pumpAndSettle();

    expect(find.text('POS Kasir'), findsOneWidget);
    expect(tester.takeException(), isNull);
  });
}
