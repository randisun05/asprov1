# Project Context

Membership/association management web app for a professional organization (Indonesian:
pages include Keanggotaan/membership, Struktur Organisasi, Visi Misi, Hukum & Advokasi,
Hubungan Masyarakat, Sumber Pendanaan, Peraturan Organisasi, plus a public site with
Berita/Artikel/Cerita). Has a public-facing site and two authenticated areas: **Admin**
and **Member (User)**.

## Stack

- Laravel 12 (PHP 8.2+), Inertia.js 1.3 + Vue 3, Vite build
- Auth: Laravel Fortify with mandatory 2FA for admin/sekretariat
- Payments: Midtrans Snap (alternative to manual proof-of-payment upload)
- PDF/exports: dompdf, snappy, fpdf/fpdi, tcpdf, maatwebsite/excel
- QR codes: f9webltd/simple-qrcode (event attendance / member cards)
- reCAPTCHA on admin login and public registration

## Core domains

- **Members**: registration/approval flow, profile, digital member card, NIP sync
- **Events**: join/absen (attendance) rules enforced server-side, certificates
  (generation, search, bulk import/export)
- **Points/rewards** (newest feature, added 2026-07): `PointService`,
  `PointTransaction`, `EventPoint` — members earn points from events and redeem
  rewards (`RewardGroup`), enforced via `InsufficientPointsException`
- **Admin**: role management (whitelist + division enforcement), inbox, reports

## Git remotes

- `origin` → https://github.com/randisun05/asprosdma.git (original repo)
- `asprov1` → https://github.com/randisun05/asprov1.git (mirror added 2026-08-20 to
  carry full history + context to another machine)

## Development history themes (see `git log` for full detail)

- Laravel upgraded sequentially 9 → 10 → 11 → 12
- Several rounds of hardening: race conditions/double-submission on registration
  approval, GET→POST conversion for state-changing admin actions, operator bugs in
  authorization checks, rate limiting on login/registration, server-side enforcement
  of event join/absen rules (previously UI-only)
- Admin dashboard/member dashboard redesigns for clarity; global success/error/info
  popups added backend-wide
- Test coverage added incrementally across admin modules and the points feature

## Notes

- `.env` is git-ignored; copy `.env.example` and fill in credentials locally.
- `public/build/` (compiled Vite assets) is committed to the repo rather than built
  in CI — regenerate with `npm run build` after frontend changes.
