# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

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
