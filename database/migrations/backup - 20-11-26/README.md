# Warkop Cak Kebo - Clean Laravel Migrations

These migrations are intended to replace the current development migrations and recreate the PostgreSQL/Supabase schema from Laravel.

## Main type decisions

- Auto-increment IDs and foreign keys: `BIGINT` via Laravel `id()` / `foreignId()`.
- Business integers (prices, stock, ml, gram, quantity, conversion, totals): `BIGINT`.
- Order line quantity and option max choices stay `INTEGER` because their business range is small.
- Latitude / longitude stay `NUMERIC(10,8)` / `NUMERIC(11,8)`.
- Product discount value stays `NUMERIC(12,2)` because percentage discounts may need decimals.
- Bounded strings use `VARCHAR(n)` via Laravel `string(name, length)` so PostgreSQL enforces the character limit.
- Long text uses `TEXT` with explicit PostgreSQL `CHECK` constraints where a business maximum is needed.
- Sanctum uses its current `personal_access_tokens` schema for API token authentication.

## Important business limits in this version

| Area | Limit |
|---|---:|
| Product name | 100 chars |
| Product description | 1000 chars |
| Category name | 100 chars |
| Base / unit / option price | 999,999,999 |
| Product / raw material stock | 9,999,999,999 |
| Recipe amount | 9,999,999 base units |
| Packaging conversion | 9,999,999,999 base units |
| Purchase quantity | 9,999 |
| Purchase / order subtotal and totals | 99,999,999,999,999 |
| Invoice number / code | 50 chars |
| Supplier name | 100 chars |
| Purchase unit | 50 chars |
| Option group / value names | 100 chars |
| Attendance notes | 1000 chars |

These are business safeguards, not the technical capacity of `BIGINT`. Adjust the constants before production if the actual business rules change.

## Sanctum

The `personal_access_tokens` migration intentionally follows the current Sanctum 4.x migration shape: `morphs('tokenable')`, 64-character hashed token storage, `abilities`, `expires_at`, and timestamps. Keep `Laravel\\Sanctum\\HasApiTokens` on `App\\Models\\User` for Flutter/API token authentication.

## Fresh rebuild

After replacing the old migration files:

```bash
php artisan migrate:fresh --seed
```

The `migrations` table itself is managed by Laravel's migration repository; you do not create a separate migration for it.

## Note

The schema is designed for PostgreSQL/Supabase. The explicit `CHECK` constraints are PostgreSQL SQL statements so the database itself enforces business limits even if someone bypasses the frontend or Laravel validation.
