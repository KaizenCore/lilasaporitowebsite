# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

---
## CURRENT STATUS (2026-01-27)

### Blocking Issue: Production Mail Not Working
**Problem**: Party inquiry form submits successfully but fails on email send with "Class Resend not found"

**Root Cause**:
- Dokploy container is 26+ hours old with stale cached config (`MAIL_MAILER=resend`)
- Dokploy env vars were changed but container was never redeployed
- Current Dokploy env vars have `MAIL_MAILER=log` which only logs, doesn't send

**Fix Required** (do this first thing tomorrow):
1. Go to Dokploy UI → frizzboss app → Environment Variables
2. Update mail settings:
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.resend.com
   MAIL_PORT=587
   MAIL_USERNAME=resend
   MAIL_PASSWORD=<get_resend_api_key_from_resend.com>
   MAIL_FROM_ADDRESS=hello@frizzboss.ca
   MAIL_FROM_NAME=FizzBoss
   ```
3. **Redeploy the container** (critical - env var changes don't apply until redeploy)

### Code Fixes Made Today
1. `resources/views/parties/inquire.blade.php` - Removed broken hidden `addon_ids` input that caused validation failures
2. `app/Models/PartyBooking.php` - Removed incorrect `datetime:H:i` casts for time fields (they're `time` columns, not datetime)

### Pending: Needs Resend Account Setup
- Sign up at https://resend.com (free tier)
- Add domain `frizzboss.ca` and verify DNS
- Get API key for MAIL_PASSWORD

---

## Project Overview

This is a Laravel 12 application for Lila Saporito's art class booking website with an integrated online store. It uses Stripe for payment processing, Tailwind CSS for styling, and Alpine.js for frontend interactivity.

## Development Commands

```bash
# Start all development services (server, queue, logs, vite)
composer dev

# Initial setup (install deps, generate key, migrate, build assets)
composer setup

# Run tests
composer test

# Build frontend assets
npm run build

# Run development frontend server only
npm run dev

# Code formatting
./vendor/bin/pint
```

## Architecture

### Domain Models

- **ArtClass** - Art class sessions with scheduling, capacity tracking, and pricing (in cents). Uses soft deletes.
- **Booking** - Links users to art classes with ticket codes (format: `FB-XXXX`), payment status, and attendance tracking.
- **Product** - Store products supporting both physical and digital items. Uses soft deletes.
- **ProductCategory** - Hierarchical product categories with parent/child relationships.
- **Order/OrderItem** - E-commerce order tracking.
- **Payment** - Stripe payment records linked to bookings.

### Key Services

- **StripeService** (`app/Services/StripeService.php`) - Handles Stripe payment intents and webhook verification. Uses `config('services.stripe.secret')` and `config('services.stripe.webhook_secret')`.

### Admin System

Admin routes are protected by the `admin` middleware alias (registered in `bootstrap/app.php`), which checks `auth()->user()->isAdmin()`. All admin controllers are in `App\Http\Controllers\Admin\`.

### Route Structure

- `/` - Public homepage
- `/classes`, `/classes/{slug}` - Public class listing and detail
- `/store`, `/store/{slug}` - Public storefront
- `/admin/*` - Admin panel (requires auth + admin middleware)
- `/checkout/{class:slug}` - Stripe checkout (requires auth)
- `/webhook/stripe` - Stripe webhook endpoint (no CSRF)

### Pricing Convention

All prices are stored in **cents** (`price_cents` column) and formatted via `getFormattedPriceAttribute()` accessors.

### View Organization

- `resources/views/layouts/public.blade.php` - Public pages layout (home, classes, store, about)
- `resources/views/layouts/app.blade.php` - Authenticated user pages (dashboard, bookings, profile)
- `resources/views/layouts/admin.blade.php` - Admin panel layout
- `resources/views/components/public-nav.blade.php` - Public navigation with glassmorphism
- `resources/views/components/footer.blade.php` - Shared footer component
- `resources/views/admin/` - Admin panel views

### Design System

**Color Palette (Purple/Pink theme):**
- Primary: `purple-600` / `purple-700`
- Secondary: `pink-600`
- Accent: `orange-500`
- Background gradient: `from-purple-50 via-pink-50 to-orange-50`
- Glassmorphism nav: `bg-white/80 backdrop-blur-md`

**Layout Usage:**
- Public pages use `<x-public-layout>` with `<x-slot name="title">`
- Auth pages use `<x-app-layout>` with `<x-slot name="header">`
- Admin pages use `@extends('layouts.admin')`

## Production Deployment

### Hosting
- **Platform**: Dokploy server (Docker containers)
- **Domain**: frizzboss.ca
- **Database**: PostgreSQL (container: `lilasaporito-frizzbossdb-kpliln`)
- **App Container**: `lilasaporito-frizzboss-q7jjri`

### Environment Variables (Dokploy UI)
**CRITICAL**: Env vars are set in Dokploy UI, NOT in a .env file. The container has no .env file.
- Changes in Dokploy UI require **container redeploy** to take effect
- The entrypoint.sh runs `config:cache` on container start - this caches whatever env vars exist at that moment
- If you change env vars in Dokploy but don't redeploy, the container keeps the OLD values

### Mail Configuration (SMTP via Resend)
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=587
MAIL_USERNAME=resend
MAIL_PASSWORD=<resend_api_key>
MAIL_FROM_ADDRESS=hello@frizzboss.ca
MAIL_FROM_NAME=FizzBoss
```
- Uses Resend's SMTP relay, NOT the `resend` Laravel package
- The `resend/resend-php` package was removed - do NOT reinstall it
- `MAIL_MAILER=log` only logs emails, doesn't send them

### Deployment Notes
- Config is cached inside container at startup via entrypoint.sh
- To update config: change env vars in Dokploy UI → Redeploy container
- Party bookings use: `PartyInquiryReceived`, `PartyInquiryAdmin`, `PartyQuoteSent`, `PartyBookingConfirmed`

### Common Production Issues
1. **"Class Resend not found"** - Container has stale cached config. Redeploy from Dokploy.
2. **Env vars not updating** - Must redeploy container after changing vars in Dokploy UI
3. **Mail not sending** - Check SMTP settings in Dokploy env vars, then redeploy

### Debug Commands (inside container)
```bash
docker exec -it <container_id> bash
echo $MAIL_MAILER              # Check actual env var
php artisan config:show mail.default  # Check cached config
```
