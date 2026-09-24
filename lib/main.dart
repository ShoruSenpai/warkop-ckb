import 'package:flutter/material.dart';

void main() {
  runApp(const CafeOpsApp());
}

class AttendancePage extends StatelessWidget {
  const AttendancePage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Attendance')),
      body: const Center(child: Text('Attendance')),
    );
  }
}

class CafeOpsApp extends StatelessWidget {
  const CafeOpsApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'Cafe Ops',
      theme: ThemeData(
        fontFamily: 'Inter',
        scaffoldBackgroundColor: const Color(0xFFF7F9FB),
        colorScheme: ColorScheme.fromSeed(seedColor: const Color(0xFF006C49)),
        useMaterial3: true,
      ),
      home: const AttendancePage(),
    );
  }
}
