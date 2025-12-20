# FrizzBoss - Quick Start Guide

## 🚀 Getting Started

### Prerequisites
- Docker Desktop (running)
- Git
- Node.js (for frontend assets)

### Initial Setup

1. **Start Docker containers** (Laravel Sail):
   ```bash
   cd fizzboss-booking
   ./vendor/bin/sail up -d
   ```

2. **Set up environment**:
   ```bash
   cp .env.example .env
   # Edit .env if needed
   ```

3. **Run migrations and seeders**:
   ```bash
   ./vendor/bin/sail artisan migrate:fresh --seed
   ```
   This creates:
   - All database tables
   - Admin user: `lila@frizzboss.com` / `password`
   - Default site settings

4. **Install Laravel Breeze** (authentication):
   ```bash
   ./vendor/bin/sail composer require laravel/breeze --dev
   ./vendor/bin/sail artisan breeze:install blade
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run dev
   ```

5. **Access the application**:
   - Frontend: http://localhost
   - Admin login: http://localhost/login
   - Credentials: `lila@frizzboss.com` / `password`

---

## 📁 Project Structure

```
fizzboss-booking/
├── app/Models/          # ✅ 5 models with relationships
├── app/Http/Middleware/ # ✅ Admin protection middleware
├── database/migrations/ # ✅ 6 database tables
├── database/seeders/    # ✅ Admin user + settings
└── [controllers, views to be built next]
```

---

## 🗄️ Database

### Tables Created
1. `users` - Students + Admin (Lila)
2. `art_classes` - Class listings
3. `bookings` - Student bookings with ticket codes
4. `payments` - Stripe transactions
5. `email_logs` - Email tracking
6. `site_settings` - Configurable settings

### Default Admin Account
- **Email**: lila@frizzboss.com
- **Password**: password
- **Role**: Admin (is_admin = true)

---

## 🛠️ Useful Commands

### Docker/Sail
```bash
./vendor/bin/sail up -d      # Start containers
./vendor/bin/sail down       # Stop containers
./vendor/bin/sail artisan    # Run artisan commands
./vendor/bin/sail composer   # Run composer
./vendor/bin/sail npm        # Run npm
```

### Database
```bash
./vendor/bin/sail artisan migrate          # Run migrations
./vendor/bin/sail artisan migrate:fresh    # Reset database
./vendor/bin/sail artisan db:seed          # Run seeders
./vendor/bin/sail artisan migrate:fresh --seed  # Reset + seed
```

### Development
```bash
./vendor/bin/sail npm run dev   # Start Vite dev server
./vendor/bin/sail artisan tinker  # Laravel REPL
./vendor/bin/sail test           # Run tests
```

---

## 📚 Documentation

- **IMPLEMENTATION_STATUS.md** - Detailed project status, database schema, next steps
- **FOR_LILA.md** - Original project requirements and features for Lila

---

## ⚡ Next Steps

1. Build admin class management (create/edit classes)
2. Build public class browsing pages
3. Implement booking + Stripe payment flow
4. Set up email notifications
5. Create admin dashboard

See IMPLEMENTATION_STATUS.md for full roadmap!
