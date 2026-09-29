# warkop_try

Flutter application for Warkop Cak Kebo staff attendance and POS.

## Attendance API

Attendance submission and recent history use the Supabase REST table `attendance`.
Run the `warkop_try (Supabase)` launch configuration and enter the Project URL and
publishable key when prompted. Values are used for that VS Code debug session and
are not stored in the repository. Never use the `sb_secret_` key in a Flutter app.
To run from PowerShell instead, provide them at runtime:

```powershell
flutter run --dart-define=SUPABASE_URL=https://YOUR_PROJECT.supabase.co --dart-define=SUPABASE_PUBLISHABLE_KEY=YOUR_PUBLISHABLE_KEY
```

The supplied project currently responds to its health check, but PostgREST
reports that `public.attendance` does not exist. Run
`supabase/migrations/20260927000000_create_attendance.sql` in the Supabase SQL
Editor to create the expected schema. Row Level Security is enabled with no
public policies by default. This demo uses local employee login rather than
Supabase Auth, so do not add an unrestricted `anon` read policy: attendance
photos and employee records would become accessible to anyone with the app.
Add authenticated policies after mapping Supabase Auth users to employees, or
route writes through a trusted Edge Function. The app reports API errors rather
than showing a false success.

The location screen checks the device GPS against the outlet coordinates and
radius in `lib/services/app_data.dart`. The current target is Kos Bu Mirza pink
(-8.1565529, 113.7189992) with a 150 m radius, resolved from the supplied Maps link.

## Demo Accounts

- Employee: `alisa` / `123456` (no POS tab)
- Cashier: `raka` / `123456` (POS tab requires PIN `123456` each time it is opened)

## Getting Started

This project is a starting point for a Flutter application.

A few resources to get you started if this is your first Flutter project:

- [Learn Flutter](https://docs.flutter.dev/get-started/learn-flutter)
- [Write your first Flutter app](https://docs.flutter.dev/get-started/codelab)
- [Flutter learning resources](https://docs.flutter.dev/reference/learning-resources)

For help getting started with Flutter development, view the
[online documentation](https://docs.flutter.dev/), which offers tutorials,
samples, guidance on mobile development, and a full API reference.
