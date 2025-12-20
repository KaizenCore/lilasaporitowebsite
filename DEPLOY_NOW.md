# 🚀 Deploy FrizzBoss to Production NOW

## ✅ Code Successfully Pushed to GitHub!

Repository: https://github.com/Quigybobo/lilasaporitowebsite

---

## 🎯 **Deploy to Railway.app in 10 Minutes** (Recommended)

### **Step 1: Sign Up for Railway**
1. Go to: https://railway.app
2. Click **"Sign up"**
3. Sign in with GitHub (easiest)

---

### **Step 2: Create New Project**
1. Click **"New Project"**
2. Select **"Deploy from GitHub repo"**
3. Choose **"Quigybobo/lilasaporitowebsite"**
4. Railway will auto-detect Laravel and start deploying!

---

### **Step 3: Add MySQL Database**
1. In your Railway project dashboard
2. Click **"New"** → **"Database"** → **"Add MySQL"**
3. Railway automatically connects it to your app!

---

### **Step 4: Add Environment Variables**

In Railway project → **Variables** → Add these:

```env
APP_NAME=FrizzBoss
APP_ENV=production
APP_KEY=base64:A5Gq4YSzlpKeG7SMb5TuvvKEOK9cEkx0JXT8kccJ8HA=
APP_DEBUG=false
APP_URL=https://frizzboss.ca

# Database (Railway auto-fills these)
DB_CONNECTION=mysql
DB_HOST=${{MYSQL_HOST}}
DB_PORT=${{MYSQL_PORT}}
DB_DATABASE=${{MYSQL_DATABASE}}
DB_USERNAME=${{MYSQL_USER}}
DB_PASSWORD=${{MYSQL_PASSWORD}}

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.frizzboss.ca

# Stripe (LIVE KEYS - get from dashboard.stripe.com)
STRIPE_KEY=pk_live_YOUR_LIVE_KEY_HERE
STRIPE_SECRET=sk_live_YOUR_LIVE_SECRET_HERE
STRIPE_WEBHOOK_SECRET=whsec_YOUR_WEBHOOK_SECRET_HERE

# Email (Sign up for Resend.com - FREE)
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=587
MAIL_USERNAME=resend
MAIL_PASSWORD=YOUR_RESEND_API_KEY
MAIL_FROM_ADDRESS=hello@frizzboss.ca
MAIL_FROM_NAME="FrizzBoss Art Classes"

# Cache
CACHE_STORE=database
QUEUE_CONNECTION=database
```

---

### **Step 5: Run Database Migrations**

In Railway project → **Deployments** → Latest deployment → **"..."** → **"Add Build Command"**

Add this:
```bash
php artisan migrate --force --seed
```

Or use Railway CLI:
```bash
# Install Railway CLI first
npm install -g @railway/cli

# Login
railway login

# Link to your project
railway link

# Run migrations
railway run php artisan migrate --force --seed
```

---

### **Step 6: Add Custom Domain (frizzboss.ca)**

#### **In Railway:**
1. Go to your app → **Settings** → **Domains**
2. Click **"Custom Domain"**
3. Enter: `frizzboss.ca`
4. Railway will show you DNS records to add

#### **In Your Domain Registrar:**
Add these DNS records:

**For Root Domain (@):**
- Type: `A`
- Name: `@`
- Value: [Railway will provide IP]

**For WWW:**
- Type: `CNAME`
- Name: `www`
- Value: `your-app.up.railway.app`

**Wait 10-60 minutes for DNS to propagate**

---

### **Step 7: Set Up Stripe Webhook**

1. Go to: https://dashboard.stripe.com
2. Toggle to **Live mode** (top right)
3. Go to **Developers** → **Webhooks**
4. Click **"Add endpoint"**
5. Enter URL: `https://frizzboss.ca/webhook/stripe`
6. Select events:
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
7. Click **"Add endpoint"**
8. Copy the **Signing secret** (starts with `whsec_`)
9. Add it to Railway variables as `STRIPE_WEBHOOK_SECRET`

---

### **Step 8: Set Up Email (Resend - FREE)**

1. Go to: https://resend.com
2. Sign up (free account)
3. Verify your email
4. Go to **API Keys** → Create new key
5. Copy the API key
6. Add to Railway as `MAIL_PASSWORD`
7. In Resend, add domain: `frizzboss.ca`
8. Follow DNS instructions to verify domain

---

### **Step 9: Test Your Live Site!**

Once DNS propagates:

1. Visit **https://frizzboss.ca**
2. Login as admin: `lila@frizzboss.ca` / `password`
3. Create a test class
4. Logout and book it with a REAL card
5. Verify email arrives
6. Check booking appears in admin

**⚠️ Use a real card for testing in production! Test mode cards won't work with live keys.**

---

## ✅ **Post-Deployment Checklist**

- [ ] Site loads at https://frizzboss.ca
- [ ] SSL certificate is active (https works)
- [ ] Can login as admin
- [ ] Can create classes
- [ ] Image upload works
- [ ] Stripe checkout loads
- [ ] Live payment succeeds
- [ ] Email confirmation arrives
- [ ] Booking appears in admin
- [ ] Check-in works with ticket code
- [ ] Mobile responsive

---

## 🎨 **Add Content**

Now that it's live, Lila needs to:

1. **Update Admin Email**
   - Change from `lila@frizzboss.com` to her real email
   - Go to Profile → Update email

2. **Add Bio**
   - Go to About page
   - Update with Lila's real bio
   - Add her Instagram handle

3. **Upload Class Photos**
   - Create 3-5 initial classes
   - Upload high-quality photos
   - Write engaging descriptions

4. **Set Pricing**
   - Verify pricing is correct
   - Set realistic capacity limits

5. **Test Everything**
   - Book a class yourself
   - Verify emails work
   - Test on mobile

---

## 📱 **Announce Launch**

Once everything is tested:

1. **Instagram Post**
   ```
   🎨 BIG NEWS! Book my art classes directly at frizzboss.ca!

   ✨ Easier booking
   💳 Secure checkout
   🎟️ Instant ticket codes

   Link in bio! First class is [DATE]

   #FrizzBoss #ArtClasses #Painting #[YourCity]
   ```

2. **Instagram Story**
   - Show the booking page
   - Swipe up to frizzboss.ca
   - "No more Eventbrite!"

3. **Email Past Students**
   ```
   Subject: New booking site is live! 🎨

   Hey everyone!

   Great news - you can now book my art classes directly at frizzboss.ca!

   - Easier than Eventbrite
   - Instant confirmation
   - Secure checkout
   - Your own account to track bookings

   Check out the upcoming classes and reserve your spot!

   See you soon,
   Lila
   ```

---

## 💰 **Monitor Revenue**

Track everything in:

1. **Stripe Dashboard**: https://dashboard.stripe.com
   - See all payments
   - Track revenue
   - View fees

2. **Admin Dashboard**: https://frizzboss.ca/admin/dashboard
   - Total bookings
   - Popular classes
   - Upcoming classes

3. **Bookings Page**: https://frizzboss.ca/admin/bookings
   - See who's coming
   - Check-in students
   - Search by ticket code

---

## 🆘 **Troubleshooting**

### **Site not loading**
- Check DNS propagation: https://dnschecker.org
- Wait up to 48 hours for DNS
- Try incognito mode

### **Database errors**
```bash
railway run php artisan migrate:fresh --seed --force
```

### **Emails not sending**
- Verify Resend API key
- Check domain is verified in Resend
- Look in spam folder

### **Stripe not working**
- Verify using LIVE keys (pk_live_, sk_live_)
- Check webhook is configured
- View webhook logs in Stripe dashboard

### **Images not showing**
```bash
railway run php artisan storage:link
```

---

## 🎉 **Success!**

Your FrizzBoss booking system is now LIVE at:

**https://frizzboss.ca** 🇨🇦

Features:
✅ Professional booking system
✅ Stripe payments
✅ Automatic ticket codes
✅ Email confirmations
✅ Admin dashboard
✅ Mobile responsive
✅ Your own domain!

**Total Monthly Cost**: ~$1-5 (vs $50+ on Eventbrite!)

---

## 📞 **Need Help?**

Check these docs:
- `DEPLOYMENT_GUIDE.md` - Full deployment guide
- `TEST_STRIPE_PAYMENT.md` - Testing guide
- `NEXT_STEPS.md` - Post-launch tasks

---

**Congratulations on your launch! 🚀🎨**
