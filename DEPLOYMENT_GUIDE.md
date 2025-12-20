# 🚀 Deploy FrizzBoss to Production (frizzboss.ca)

## Domain: frizzboss.ca 🇨🇦

This guide will help you deploy your FrizzBoss booking system to your new domain.

---

## 📋 **Deployment Options**

### **Option 1: Vercel (Recommended - FREE!)**
- ✅ Free hosting
- ✅ Automatic SSL (HTTPS)
- ✅ Global CDN
- ✅ Easy GitHub integration
- ✅ Zero configuration needed
- ⚠️ Note: Laravel on Vercel requires some setup

### **Option 2: Laravel Forge + DigitalOcean (~$12/month)**
- ✅ Optimized for Laravel
- ✅ One-click deployment
- ✅ SSL certificates
- ✅ Database included
- ⚠️ Costs $12/month for smallest droplet

### **Option 3: Shared Hosting (~$3-8/month)**
- ✅ Very cheap (Namecheap, Hostinger, SiteGround)
- ✅ cPanel included
- ✅ Email hosting included
- ⚠️ Slower than VPS
- Examples: Namecheap Stellar Plus ($3.88/mo), Hostinger Premium ($2.99/mo)

### **Option 4: Railway.app (~FREE-$5/month)**
- ✅ Free tier available ($5 credit/month)
- ✅ Laravel-friendly
- ✅ Database included
- ✅ Easy deployment
- ✅ Automatic SSL

---

## 🎯 **Recommended: Railway.app** (Easiest + Cheapest)

Railway is perfect for FrizzBoss because:
- Free $5/month credit (enough for this app!)
- One command deployment
- Automatic SSL
- MySQL database included
- Laravel-optimized

### **Deploy to Railway in 10 Minutes:**

#### 1. **Prepare Your Project**

```bash
# In your fizzboss-booking directory
git init
git add .
git commit -m "Initial commit - FrizzBoss booking system"
```

#### 2. **Create GitHub Repo**
```bash
# Create repo on GitHub, then:
git remote add origin https://github.com/YOUR_USERNAME/fizzboss-booking.git
git branch -M main
git push -u origin main
```

#### 3. **Deploy to Railway**

1. Go to https://railway.app
2. Sign up with GitHub
3. Click **"New Project"**
4. Select **"Deploy from GitHub repo"**
5. Choose your `fizzboss-booking` repo
6. Railway will auto-detect Laravel and deploy!

#### 4. **Add Environment Variables**

In Railway dashboard, go to your project → Variables:

```env
APP_NAME=FrizzBoss
APP_ENV=production
APP_KEY=base64:A5Gq4YSzlpKeG7SMb5TuvvKEOK9cEkx0JXT8kccJ8HA=
APP_DEBUG=false
APP_URL=https://frizzboss.ca

DB_CONNECTION=mysql
DB_HOST=${{MYSQL_HOST}}
DB_PORT=${{MYSQL_PORT}}
DB_DATABASE=${{MYSQL_DATABASE}}
DB_USERNAME=${{MYSQL_USER}}
DB_PASSWORD=${{MYSQL_PASSWORD}}

STRIPE_KEY=pk_live_YOUR_LIVE_KEY
STRIPE_SECRET=sk_live_YOUR_LIVE_SECRET
STRIPE_WEBHOOK_SECRET=whsec_YOUR_WEBHOOK_SECRET

MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=YOUR_SENDGRID_API_KEY
MAIL_FROM_ADDRESS="hello@frizzboss.ca"
MAIL_FROM_NAME="FrizzBoss Art Classes"
```

#### 5. **Add MySQL Database**

In Railway:
- Click **"New"** → **"Database"** → **"Add MySQL"**
- Railway automatically connects it to your app!

#### 6. **Run Migrations**

In Railway dashboard:
- Go to your app → **"Deployments"**
- Click latest deployment → **"View Logs"**
- Or use Railway CLI:

```bash
railway run php artisan migrate --seed
```

#### 7. **Point Your Domain**

In your domain registrar (where you bought frizzboss.ca):

**Add DNS Records:**
- Type: `CNAME`
- Name: `@` (or root)
- Value: `YOUR-APP.up.railway.app`

**OR use A Record:**
- Railway will give you an IP
- Point your domain to that IP

**Add Custom Domain in Railway:**
- Settings → Domains → Add Custom Domain
- Enter: `frizzboss.ca`
- Railway auto-configures SSL!

---

## 🔒 **SSL/HTTPS Setup**

Railway automatically provides SSL! Your site will be:
- ✅ https://frizzboss.ca (SSL automatically configured)
- ✅ Force HTTPS (automatic)

---

## 📧 **Email Setup (Transactional Emails)**

For sending booking confirmations, you need an email service:

### **Option 1: Resend (Recommended - FREE)**
- Free tier: 3,000 emails/month
- Easy Laravel integration
- No credit card needed

1. Sign up at https://resend.com
2. Get API key
3. Add to Railway env:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=587
MAIL_USERNAME=resend
MAIL_PASSWORD=YOUR_RESEND_API_KEY
```

### **Option 2: SendGrid (FREE)**
- Free tier: 100 emails/day
- Very reliable

### **Option 3: Mailgun (FREE)**
- Free tier: 5,000 emails/month (first 3 months)

---

## 💳 **Switch to Live Stripe Keys**

⚠️ **IMPORTANT**: Switch from test to live keys!

1. Go to https://dashboard.stripe.com
2. Toggle from **Test mode** to **Live mode**
3. Get your live keys:
   - Publishable key: `pk_live_...`
   - Secret key: `sk_live_...`
4. Update Railway environment variables:
```env
STRIPE_KEY=pk_live_YOUR_KEY
STRIPE_SECRET=sk_live_YOUR_SECRET
```

5. **Set up Webhook for Production:**
   - Stripe Dashboard → Webhooks → Add endpoint
   - URL: `https://frizzboss.ca/webhook/stripe`
   - Events: `payment_intent.succeeded`, `payment_intent.payment_failed`
   - Copy webhook secret → Add to Railway as `STRIPE_WEBHOOK_SECRET`

---

## 📝 **Pre-Launch Checklist**

- [ ] Domain pointed to Railway (DNS configured)
- [ ] SSL certificate active (https:// works)
- [ ] Environment variables set (all in production)
- [ ] Database migrated and seeded
- [ ] Admin account created (lila@frizzboss.ca)
- [ ] Stripe live keys configured
- [ ] Stripe webhook endpoint added
- [ ] Email service configured (Resend/SendGrid)
- [ ] Test booking flow with live card
- [ ] Verify email notifications work
- [ ] Update Lila's bio and Instagram link
- [ ] Add real class images
- [ ] Test on mobile devices
- [ ] Create 2-3 initial classes

---

## 🧪 **Testing on Production**

Before announcing:

1. **Create test class** (as admin)
2. **Make real booking** with real card
3. **Verify email arrives**
4. **Check ticket code displays**
5. **Test check-in process**
6. **Try on mobile phone**
7. **Ask a friend to test**

---

## 💰 **Total Monthly Cost**

Using Railway + Resend:
- **Railway**: FREE (with $5 credit, your app uses ~$3-4/month)
- **Domain**: ~$1/month ($15/year for .ca)
- **Email**: FREE (Resend 3,000 emails/month)
- **Stripe**: 2.9% + $0.30 per transaction only
- **Total**: ~$1-4/month

**Much cheaper than Eventbrite!**

---

## 🔧 **After Launch Maintenance**

### **Updating the Site**

When you need to make changes:

```bash
# Make your changes locally
git add .
git commit -m "Update classes page"
git push

# Railway auto-deploys! 🚀
```

### **Database Backups**

Railway provides automatic backups, but you can also:

```bash
# Download database backup
railway run mysqldump -u $MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE > backup.sql
```

### **View Logs**

In Railway dashboard:
- Go to your app → Deployments → View Logs
- Or use CLI: `railway logs`

---

## 🆘 **Troubleshooting**

### Site not loading
- Check DNS propagation: https://dnschecker.org
- Verify Railway deployment succeeded
- Check deployment logs

### Database errors
- Verify migrations ran: `railway run php artisan migrate:status`
- Check database connection in Railway variables

### Emails not sending
- Check email service API key is correct
- Verify email service is not in sandbox mode
- Check spam folder

### Stripe payments failing
- Verify live keys (not test keys)
- Check webhook is configured
- View webhook logs in Stripe Dashboard

---

## 📚 **Alternative: Shared Hosting Setup**

If you prefer traditional hosting like Namecheap:

1. **Upload files via FTP**
2. **Create MySQL database in cPanel**
3. **Update `.env` with database credentials**
4. **Point domain to public folder**
5. **Run migrations via SSH or Artisan UI**

Detailed guide: https://laravel.com/docs/deployment

---

## 🎉 **Launch Day!**

When you're ready to go live:

1. **Post on Instagram** (link in bio)
2. **Email past students**
3. **Create first 3-5 classes**
4. **Share with friends**
5. **Test booking process one more time**

---

## 📞 **Support Resources**

- **Railway Docs**: https://docs.railway.app
- **Laravel Docs**: https://laravel.com/docs
- **Stripe Docs**: https://stripe.com/docs
- **Domain DNS Help**: Check your registrar's support

---

## 🎨 **You're Ready to Launch!**

Your FrizzBoss booking system is production-ready. With frizzboss.ca, you'll have a professional platform that's:

✅ Faster than Eventbrite
✅ Cheaper than Eventbrite
✅ Completely customized for Lila
✅ Your own domain and brand
✅ No middleman fees

**Good luck with the launch!** 🚀
