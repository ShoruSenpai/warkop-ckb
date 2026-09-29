import 'package:flutter/material.dart';

import '../services/app_session.dart';
import '../theme/stitch_theme.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  bool _obscurePassword = true;
  final _usernameController = TextEditingController();
  final _passwordController = TextEditingController();
  String? _error;

  @override
  void dispose() {
    _usernameController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  void _login() {
    final success = AppSession.instance.login(
      _usernameController.text,
      _passwordController.text,
    );
    if (!success) {
      setState(() => _error = 'Username atau password tidak valid.');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: StitchTheme.backgroundLight,
      body: Center(
        child: ConstrainedBox(
          constraints: const BoxConstraints(maxWidth: 440),
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(28),
            child: Card(
              child: Padding(
                padding: const EdgeInsets.all(28),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Icon(
                      Icons.local_cafe_rounded,
                      color: StitchTheme.primaryGreen,
                      size: 42,
                    ),
                    const SizedBox(height: 20),
                    const Text(
                      'Cak Kebo',
                      style: TextStyle(
                        fontSize: 30,
                        fontWeight: FontWeight.w800,
                        color: StitchTheme.textDark,
                      ),
                    ),
                    const SizedBox(height: 4),
                    const Text(
                      'Satu pintu untuk operasional outlet.',
                      style: TextStyle(color: StitchTheme.textMuted),
                    ),
                    const SizedBox(height: 28),
                    const SizedBox(height: 24),
                    TextField(
                      key: const ValueKey('login-username'),
                      controller: _usernameController,
                      textInputAction: TextInputAction.next,
                      onChanged: (_) => setState(() => _error = null),
                      decoration: const InputDecoration(
                        labelText: 'Username',
                        prefixIcon: Icon(Icons.person_outline),
                      ),
                    ),
                    const SizedBox(height: 14),
                    TextField(
                      key: const ValueKey('login-password'),
                      controller: _passwordController,
                      obscureText: _obscurePassword,
                      onSubmitted: (_) => _login(),
                      decoration: InputDecoration(
                        labelText: 'Password',
                        prefixIcon: const Icon(Icons.lock_outline),
                        suffixIcon: IconButton(
                          onPressed: () => setState(
                            () => _obscurePassword = !_obscurePassword,
                          ),
                          icon: Icon(
                            _obscurePassword
                                ? Icons.visibility_outlined
                                : Icons.visibility_off_outlined,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 6),
                    const Text(
                      'Demo: alisa / 123456 · raka / 123456',
                      style: TextStyle(
                        color: StitchTheme.textMuted,
                        fontSize: 12,
                      ),
                    ),
                    if (_error != null) ...[
                      const SizedBox(height: 12),
                      Text(
                        _error!,
                        style: const TextStyle(
                          color: Colors.redAccent,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                    const SizedBox(height: 24),
                    SizedBox(
                      width: double.infinity,
                      height: 52,
                      child: FilledButton(
                        onPressed: _login,
                        child: const Text('Masuk ke Aplikasi'),
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
