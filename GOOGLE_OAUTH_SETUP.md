# Google OAuth Setup Guide for FrizzBoss

## Overview

FrizzBoss uses **Google OAuth** for student authentication, allowing users to sign in with their Google accounts. Admin accounts use password-based authentication for security.

## Authentication Strategy

- **Students**: Google OAuth only (no passwords)
- **Admin**: Password-based authentication only (separate login at `/admin/login`)

---

## Step 1: Create Google OAuth Credentials

### 1.1 Go to Google Cloud Console

Visit: https://console.cloud.google.com/

### 1.2 Create a New Project (or select existing)

1. Click the project dropdown at the top
2. Click "New Project"
3. Name it: **FrizzBoss** (or your preferred name)
4. Click "Create"

### 1.3 Enable Google+ API

1. In the left sidebar, go to **APIs & Services** → **Library**
2. Search for "Google+ API"
3. Click on it and click **Enable**

### 1.4 Configure OAuth Consent Screen

1. Go to **APIs & Services** → **OAuth consent screen**
2. Select **External** (unless you have a Google Workspace)
3. Click **Create**

**Fill in the required fields:**
- App name: `FrizzBoss`
- User support email: Your email
- Developer contact email: Your email
- App logo: (optional - upload FrizzBoss logo)

**Scopes:**
- Click "Add or Remove Scopes"
- Select:
  - `.../auth/userinfo.email`
  - `.../auth/userinfo.profile`
- Save and Continue

**Test users (Development only):**
- Add your Google email for testing
- Save and Continue

### 1.5 Create OAuth 2.0 Credentials

1. Go to **APIs & Services** → **Credentials**
2. Click **Create Credentials** → **OAuth client ID**
3. Choose **Application type**: Web application
4. Name: `FrizzBoss Web Client`

**Authorized JavaScript origins:**
```
http://localhost:8000
https://yourdomain.com  (add later for production)
```

**Authorized redirect URIs:**
```
http://localhost:8000/auth/google/callback
https://yourdomain.com/auth/google/callback  (add later for production)
```

5. Click **Create**
6. **Copy the Client ID and Client Secret** - you'll need these!

---

## Step 2: Configure Your Laravel Application

### 2.1 Update `.env` File

Open your `.env` file and update the Google OAuth credentials:

```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your_actual_client_id_here
GOOGLE_CLIENT_SECRET=your_actual_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

**Important:** Replace `your_actual_client_id_here` and `your_actual_client_secret_here` with the values from Google Cloud Console.

### 2.2 Clear Config Cache

After updating `.env`, run:

```bash
docker exec fizzboss-app php artisan config:clear
docker exec fizzboss-app php artisan cache:clear
```

---

## Step 3: Test the OAuth Flow

### 3.1 Access the Login Page

1. Go to: http://localhost:8000/login
2. You should see a "Continue with Google" button

### 3.2 Test Student Login

1. Click "Continue with Google"
2. Select your Google account
3. Grant permissions
4. You should be redirected to `/my-bookings`
5. Check that your account was created in the database

### 3.3 Test Admin Login

1. Go to: http://localhost:8000/admin/login
2. Use admin credentials:
   - Email: `lila@frizzboss.com`
   - Password: `password`
3. You should be redirected to the admin dashboard

### 3.4 Verify Admin Cannot Use Google OAuth

1. Logout
2. Try to login with Lila's Google account via `/login`
3. You should see an error: "Admin accounts must use password authentication"

---

## How It Works

### For Students

1. **Login**: `/login` → Click "Continue with Google" → Redirected to Google
2. **Google Auth**: User grants permissions
3. **Callback**: Google redirects to `/auth/google/callback`
4. **Account Creation**:
   - If new user: Account created automatically with Google info
   - If existing user: Logged in
5. **Redirect**: User sent to `/my-bookings`

### For Admin

1. **Login**: `/admin/login` → Enter email/password
2. **Validation**: System checks if user is admin
3. **Redirect**: Admin sent to `/admin/dashboard`

---

## Database Changes

The following fields were added to the `users` table:

- `google_id` (string, nullable, unique) - Google account ID
- `avatar` (string, nullable) - Profile picture from Google

---

## Security Features

1. **Separate Login Flows**: Students and admins have different authentication methods
2. **Admin Protection**: Admins cannot authenticate via Google OAuth
3. **Google Verification**: Email is automatically verified for Google users
4. **No Password Storage**: Google OAuth users don't have passwords in the database

---

## Troubleshooting

### Error: "Failed to authenticate with Google"

**Cause**: OAuth credentials not configured or incorrect

**Solution**:
1. Double-check your `.env` file has correct `GOOGLE_CLIENT_ID` and `GOOGLE_CLIENT_SECRET`
2. Run `php artisan config:clear`
3. Verify redirect URI matches exactly in Google Cloud Console

### Error: "redirect_uri_mismatch"

**Cause**: The redirect URI in Google Cloud Console doesn't match your app

**Solution**:
1. Go to Google Cloud Console → Credentials
2. Edit your OAuth client
3. Add exact redirect URI: `http://localhost:8000/auth/google/callback`
4. Save and try again

### Error: "Access blocked: This app's request is invalid"

**Cause**: Google+ API not enabled or consent screen not configured

**Solution**:
1. Enable Google+ API in Google Cloud Console
2. Complete OAuth consent screen configuration
3. Add your email as a test user (if in development mode)

### Users Can't Register

**Cause**: Trying to use old registration form

**Solution**:
- Registration is now handled automatically via Google OAuth
- Users should click "Continue with Google" on `/login` or `/register`
- Both routes now use the same Google OAuth flow

---

## Production Deployment

When deploying to production:

### 1. Update Google Cloud Console

Add your production domain to:
- Authorized JavaScript origins: `https://yourdomain.com`
- Authorized redirect URIs: `https://yourdomain.com/auth/google/callback`

### 2. Update `.env` in Production

```env
APP_URL=https://yourdomain.com
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
```

### 3. Verify OAuth Consent Screen

- Switch from "Testing" to "In Production" in Google Cloud Console
- This allows any Google user to sign in (not just test users)

---

## File Changes Summary

### New Files Created:
- `app/Http/Controllers/Auth/GoogleAuthController.php` - Handles Google OAuth flow
- `app/Http/Controllers/Auth/AdminLoginController.php` - Handles admin password login
- `resources/views/auth/admin-login.blade.php` - Admin login page
- `database/migrations/2025_12_28_190310_add_google_id_to_users_table.php` - Database changes

### Modified Files:
- `app/Models/User.php` - Added `google_id` and `avatar` to fillable
- `config/services.php` - Added Google OAuth configuration
- `routes/web.php` - Added Google OAuth and admin login routes
- `resources/views/auth/login.blade.php` - Replaced with Google OAuth button
- `resources/views/auth/register.blade.php` - Replaced with Google OAuth button
- `.env.example` - Added Google OAuth credentials template

---

## URLs Reference

| Purpose | URL | Access |
|---------|-----|--------|
| Student Login | `/login` | Google OAuth |
| Student Register | `/register` | Google OAuth (auto-creates account) |
| Admin Login | `/admin/login` | Password only |
| OAuth Redirect | `/auth/google` | Redirects to Google |
| OAuth Callback | `/auth/google/callback` | Google redirects here |

---

## Next Steps

Once Google OAuth is set up:

1. Test the complete flow with a real Google account
2. Create some test art classes as admin
3. Book a class using a student Google account
4. Proceed with Stripe payment integration

---

**Questions?** Check the Laravel Socialite documentation: https://laravel.com/docs/11.x/socialite
