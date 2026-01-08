# FizzBoss Art Class Booking Platform

## Project Overview
A simple, user-friendly app-style website for booking and managing art classes. This platform replaces Eventbrite and provides a streamlined experience for students to discover, book, and pay for art classes taught by Lila (FizzBoss).

## Business Context
- **Brand Name**: FizzBoss (transitioning from "Lila's")
- **Instagram**: @frizzboss
- **Primary Goal**: Make it easy for people to see classes and purchase tickets
- **Current Pain Point**: Using Eventbrite - wants in-house solution
- **Domain**: TBD (previously shopatlilas.com ~$40/year)

## Core Features & Requirements

### Must-Have Features (MVP)
1. **Main Page - Class Listings**
   - Display upcoming art classes with dates, times, and availability
   - Clear, visual presentation of class offerings
   - Simple booking flow

2. **Payment Processing**
   - Secure online payments for class tickets
   - Replace Eventbrite functionality
   - Consider: Stripe, PayPal, or Square integration

3. **Ticket System**
   - Generate unique ticket/confirmation codes
   - Authentication codes to verify attendance on arrival
   - Email confirmations with ticket details

4. **Scheduling & Calendar**
   - Visual calendar showing available classes
   - Easy date/time selection
   - Class capacity management

5. **User Accounts & Login**
   - Simple registration/login flow
   - View purchased tickets
   - Order history
   - Profile management

6. **Bio & Portfolio Page**
   - Showcase Lila's artwork and teaching style
   - About section
   - Gallery of example paintings
   - Testimonials (optional)

7. **Invoicing**
   - Automatic invoice generation
   - Email receipts
   - Admin view for tracking payments

### Nice-to-Have Features (Future)
- Waitlist functionality
- Email notifications/reminders
- Gift card purchases
- Class reviews/ratings
- Newsletter signup
- Social media integration

## Technical Stack Recommendations

### Suggested Approach
**Modern, Simple, Cost-Effective Stack:**

**Frontend:**
- Next.js 14+ (React framework)
- TypeScript (for reliability)
- Tailwind CSS (for styling)
- shadcn/ui components (for polished UI)

**Backend:**
- Next.js API routes
- Prisma ORM (database management)
- PostgreSQL or MySQL database

**Authentication:**
- NextAuth.js or Clerk (simple, secure auth)

**Payment Processing:**
- Stripe (recommended - great docs, test mode, reasonable fees)
- Alternative: Square or PayPal

**Hosting:**
- Vercel (free tier, perfect for Next.js)
- Database: Railway, Supabase, or PlanetScale (free tiers available)

**Email:**
- Resend or SendGrid (transactional emails)

**Domain:**
- Namecheap, Google Domains, or Cloudflare (~$10-40/year)

### Why This Stack?
- **Low/No Monthly Costs**: Free tiers cover small-medium traffic
- **Simple to Maintain**: All-in-one framework, minimal moving parts
- **Scalable**: Can grow with the business
- **Great Developer Experience**: Modern tooling, good documentation

## Design Principles

### User Experience
- **Simplicity First**: Clean, uncluttered interface
- **Mobile-First**: Many users will book on phones
- **Fast Loading**: Optimize images, minimal dependencies
- **Accessible**: Clear fonts, good contrast, keyboard navigation

### Visual Style
- Artistic, creative aesthetic (reflects art class nature)
- Warm, welcoming colors
- Showcase artwork prominently
- Professional but approachable

### Key User Flows

**1. Booking a Class (Student)**
```
Home → Browse Classes → Select Class →
Create Account/Login → Payment → Confirmation Email with Ticket Code
```

**2. Class Check-In (Instructor)**
```
Admin Panel → Today's Classes →
Scan/Enter Ticket Code → Mark Attendance
```

**3. Managing Classes (Admin)**
```
Admin Dashboard → Create New Class →
Set Date/Time/Price/Capacity → Publish → Monitor Bookings
```

## Development Guidelines

### Code Style
- Use TypeScript for all new files
- Prefer functional components with hooks
- Keep components small and focused (single responsibility)
- Use descriptive variable names
- Comment complex logic

### File Structure
```
/app
  /(public)
    /page.tsx           # Home/class listings
    /classes/[id]       # Individual class details
    /bio                # Portfolio/about page
  /(auth)
    /login
    /register
  /(dashboard)
    /tickets            # User's purchased tickets
    /profile
  /admin
    /classes            # Manage classes
    /bookings           # View all bookings
    /analytics          # Revenue, attendance stats
  /api
    /auth
    /payments
    /tickets
    /classes
/components
  /ui                   # shadcn components
  /class-card.tsx
  /ticket-display.tsx
  /calendar.tsx
/lib
  /db.ts               # Database client
  /auth.ts             # Auth helpers
  /stripe.ts           # Payment integration
/prisma
  /schema.prisma       # Database schema
```

### Database Schema (Conceptual)

```prisma
model User {
  id            String   @id @default(cuid())
  email         String   @unique
  name          String?
  password      String   # hashed
  isAdmin       Boolean  @default(false)
  tickets       Ticket[]
  createdAt     DateTime @default(now())
}

model Class {
  id            String   @id @default(cuid())
  title         String
  description   String
  date          DateTime
  duration      Int      # minutes
  price         Decimal
  capacity      Int
  imageUrl      String?
  tickets       Ticket[]
  createdAt     DateTime @default(now())
}

model Ticket {
  id            String   @id @default(cuid())
  ticketCode    String   @unique
  userId        String
  classId       String
  user          User     @relation(fields: [userId], references: [id])
  class         Class    @relation(fields: [classId], references: [id])
  paymentStatus String   # paid, refunded
  attended      Boolean  @default(false)
  purchasedAt   DateTime @default(now())
}

model Invoice {
  id            String   @id @default(cuid())
  ticketId      String
  amount        Decimal
  stripeId      String?
  pdfUrl        String?
  createdAt     DateTime @default(now())
}
```

### Security Considerations
- Hash passwords (use bcrypt)
- Validate all user inputs
- Use CSRF protection
- Implement rate limiting on API routes
- Secure payment processing (never store card details)
- Use environment variables for sensitive keys
- HTTPS only in production

### Testing Approach
- Test payment flow in Stripe test mode
- Test ticket generation and validation
- Test email delivery
- Manual testing of booking flows
- Responsive design testing on mobile devices

## Phase 1: MVP Milestones

### Week 1: Foundation
- [ ] Project setup (Next.js, TypeScript, Tailwind)
- [ ] Database schema design
- [ ] Basic UI components (shadcn/ui)
- [ ] Authentication system

### Week 2: Core Features
- [ ] Class listing page
- [ ] Class detail page with booking button
- [ ] User dashboard (view tickets)
- [ ] Payment integration (Stripe)

### Week 3: Admin & Polish
- [ ] Admin panel for creating/managing classes
- [ ] Ticket generation and email system
- [ ] Bio/portfolio page
- [ ] Responsive design refinements

### Week 4: Testing & Launch
- [ ] End-to-end testing
- [ ] Domain setup
- [ ] Deploy to Vercel
- [ ] Load test data migration (if any existing customers)

## Budget Estimates

### Development
- DIY with Claude assistance: Free (your time)
- Professional developer: $2,000-5,000

### Ongoing Monthly Costs
- Domain: ~$1-3/month
- Hosting (Vercel): Free tier likely sufficient
- Database: Free tier likely sufficient
- Email service: Free tier (first 3,000 emails/month)
- Stripe fees: 2.9% + $0.30 per transaction
- **Total: ~$1-5/month** (excluding transaction fees)

### Scaling Costs (if business grows)
- Vercel Pro: $20/month (if needed)
- Database upgrade: $5-20/month (if needed)
- Email upgrade: $10-20/month (for higher volume)

## Questions to Clarify

- [ ] What's the expected number of classes per month?
- [ ] Average class size (capacity)?
- [ ] Price range per class?
- [ ] Any existing customer data to migrate?
- [ ] Preferred payment methods? (credit card only, or also PayPal, etc.)
- [ ] Need for recurring/subscription classes, or all one-time?
- [ ] Refund policy? (affects payment integration setup)
- [ ] Will you offer private classes or only group classes?

## Resources & References

- Stripe Payments: https://stripe.com/docs
- Next.js Docs: https://nextjs.org/docs
- shadcn/ui: https://ui.shadcn.com
- Prisma Docs: https://www.prisma.io/docs
- NextAuth.js: https://next-auth.js.org

## Notes
- Keep it simple - avoid over-engineering
- Prioritize user experience over fancy features
- Mobile responsiveness is critical
- Fast iteration based on user feedback
- Consider soft launch with small group for testing
