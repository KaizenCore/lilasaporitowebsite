# Setup Next.js Project Skill

This skill sets up a complete Next.js 14+ project with TypeScript, Tailwind CSS, and shadcn/ui for the FizzBoss platform.

## What This Skill Does

1. Initialize Next.js project with TypeScript
2. Configure Tailwind CSS
3. Set up shadcn/ui component library
4. Create project structure
5. Add essential dependencies
6. Configure environment variables template

## Steps

### 1. Initialize Next.js Project

```bash
npx create-next-app@latest . --typescript --tailwind --app --no-src-dir --import-alias "@/*"
```

Answer prompts:
- TypeScript: Yes
- ESLint: Yes
- Tailwind CSS: Yes
- `src/` directory: No
- App Router: Yes
- Import alias: Yes (@/*)

### 2. Install Core Dependencies

```bash
npm install @prisma/client prisma zod react-hook-form @hookform/resolvers stripe @stripe/stripe-js resend
npm install -D @types/node
```

### 3. Set Up shadcn/ui

```bash
npx shadcn@latest init
```

Configuration:
- Style: Default
- Base color: Slate
- CSS variables: Yes

Install common components:
```bash
npx shadcn@latest add button card input label select textarea dialog dropdown-menu calendar
```

### 4. Create Project Structure

```bash
mkdir -p app/api/classes app/api/tickets app/api/payments app/api/webhooks
mkdir -p app/(public)/classes app/(public)/bio
mkdir -p app/(auth)/login app/(auth)/register
mkdir -p app/(dashboard)/tickets app/(dashboard)/profile
mkdir -p app/admin/classes app/admin/bookings app/admin/analytics
mkdir -p components/ui components/class components/ticket components/admin
mkdir -p lib/actions lib/utils
mkdir -p prisma
```

### 5. Create Environment Template

```bash
cat > .env.example << 'EOF'
# Database
DATABASE_URL="postgresql://user:password@localhost:5432/frizzboss"

# Auth (NextAuth.js)
NEXTAUTH_SECRET="generate-random-secret-here"
NEXTAUTH_URL="http://localhost:3000"

# Stripe
STRIPE_PUBLIC_KEY="pk_test_..."
STRIPE_SECRET_KEY="sk_test_..."
STRIPE_WEBHOOK_SECRET="whsec_..."

# Email (Resend)
RESEND_API_KEY="re_..."

# App
NEXT_PUBLIC_APP_URL="http://localhost:3000"
EOF
```

### 6. Initialize Prisma

```bash
npx prisma init
```

### 7. Create Base Configuration Files

**lib/db.ts** (Database client):
```typescript
import { PrismaClient } from '@prisma/client'

const globalForPrisma = globalThis as unknown as {
  prisma: PrismaClient | undefined
}

export const prisma = globalForPrisma.prisma ?? new PrismaClient()

if (process.env.NODE_ENV !== 'production') globalForPrisma.prisma = prisma
```

**lib/utils.ts** (Utility functions):
```typescript
import { type ClassValue, clsx } from "clsx"
import { twMerge } from "tailwind-merge"

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export function formatCurrency(amount: number) {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount)
}

export function formatDate(date: Date) {
  return new Intl.DateTimeFormat('en-US', {
    dateStyle: 'medium',
    timeStyle: 'short'
  }).format(date)
}
```

### 8. Update package.json Scripts

Add these scripts:
```json
{
  "scripts": {
    "dev": "next dev",
    "build": "next build",
    "start": "next start",
    "lint": "next lint",
    "db:push": "prisma db push",
    "db:migrate": "prisma migrate dev",
    "db:studio": "prisma studio",
    "db:seed": "tsx prisma/seed.ts"
  }
}
```

### 9. Create Basic Layout

**app/layout.tsx**:
```typescript
import type { Metadata } from "next"
import { Inter } from "next/font/google"
import "./globals.css"

const inter = Inter({ subsets: ["latin"] })

export const metadata: Metadata = {
  title: "FizzBoss - Art Classes",
  description: "Book creative art classes with Lila",
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en">
      <body className={inter.className}>{children}</body>
    </html>
  )
}
```

### 10. Create Homepage Placeholder

**app/page.tsx**:
```typescript
export default function Home() {
  return (
    <main className="min-h-screen p-8">
      <div className="max-w-4xl mx-auto">
        <h1 className="text-4xl font-bold mb-4">FizzBoss Art Classes</h1>
        <p className="text-lg text-muted-foreground">
          Welcome! Your booking platform is being built.
        </p>
      </div>
    </main>
  )
}
```

### 11. Add .gitignore Entries

Ensure these are in .gitignore:
```
.env
.env.local
node_modules
.next
```

## Verification

After setup, verify everything works:

```bash
# Start dev server
npm run dev

# Open browser to http://localhost:3000
# Should see homepage with "FizzBoss Art Classes"

# Check Prisma
npx prisma studio
# Should open database GUI
```

## Next Steps

After running this skill:
1. Copy `.env.example` to `.env` and fill in actual values
2. Set up your database (PostgreSQL or MySQL)
3. Define Prisma schema
4. Run first migration
5. Start building features!

## Troubleshooting

**Port 3000 already in use:**
```bash
npm run dev -- -p 3001
```

**Prisma client not found:**
```bash
npx prisma generate
```

**Module not found errors:**
```bash
npm install
```
