create table if not exists public.attendance (
  id uuid primary key default gen_random_uuid(),
  karyawan_id text not null,
  tipe_absen text not null,
  latitude double precision not null,
  longitude double precision not null,
  foto text not null,
  waktu timestamptz not null default now()
);

create index if not exists attendance_karyawan_waktu_idx
  on public.attendance (karyawan_id, waktu desc);

alter table public.attendance enable row level security;

comment on table public.attendance is
  'Attendance records. Add authenticated RLS policies before enabling app access.';