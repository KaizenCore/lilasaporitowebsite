# FrizzBoss Booking - Admin System Documentation

## Overview
This admin system provides complete management capabilities for the FrizzBoss art class booking application. It includes class management, booking oversight, customer check-in functionality, and comprehensive analytics.

## System Components

### 1. Controllers
All admin controllers are located in `app/Http/Controllers/Admin/`:

#### **DashboardController.php**
- Displays comprehensive statistics and metrics
- Shows upcoming classes with booking counts
- Displays recent bookings
- Calculates revenue (total and net)
- Shows revenue trends by month
- Lists most popular classes

**Route:** `GET /admin/dashboard`

#### **ClassController.php**
Full CRUD operations for art classes:
- `index()` - List all classes with pagination
- `create()` - Show create form
- `store()` - Save new class with image upload
- `edit()` - Show edit form
- `update()` - Update existing class
- `destroy()` - Delete class (with booking validation)

**Routes:**
- `GET /admin/classes` - List classes
- `GET /admin/classes/create` - Create form
- `POST /admin/classes` - Store new class
- `GET /admin/classes/{class}/edit` - Edit form
- `PUT /admin/classes/{class}` - Update class
- `DELETE /admin/classes/{class}` - Delete class

#### **BookingController.php**
Manage bookings and customer check-ins:
- `index()` - List bookings with search/filter
- `show()` - View booking details
- `checkIn()` - Check in customer by ticket code
- `checkInForm()` - Display class check-in page
- `manualCheckIn()` - Manually check in a booking
- `cancel()` - Cancel a booking

**Routes:**
- `GET /admin/bookings` - List bookings
- `GET /admin/bookings/{booking}` - Booking details
- `POST /admin/bookings/check-in` - Check in by ticket code
- `POST /admin/bookings/{booking}/manual-check-in` - Manual check-in
- `POST /admin/bookings/{booking}/cancel` - Cancel booking
- `GET /admin/classes/{class}/check-in` - Class check-in form

### 2. Views
All admin views are located in `resources/views/admin/`:

#### **Admin Layout**
- `resources/views/layouts/admin.blade.php` - Main admin layout (deprecated, use component)
- `resources/views/components/admin-layout.blade.php` - Admin layout component (preferred)

**Features:**
- Indigo-themed navigation bar
- Responsive mobile menu
- User dropdown with logout
- Flash message display (success, error, warning)
- Links to public site and user dashboard

#### **Dashboard Views**
- `admin/dashboard.blade.php` - Main admin dashboard

**Features:**
- 4 stat cards (Classes, Upcoming, Bookings, Revenue)
- Upcoming classes list with booking progress
- Recent bookings with check-in status
- Popular classes with visual progress bars

#### **Class Management Views**
- `admin/classes/index.blade.php` - List all classes
- `admin/classes/create.blade.php` - Create new class
- `admin/classes/edit.blade.php` - Edit existing class

**Features:**
- Table view with images, dates, capacity, status
- Visual capacity indicators (progress bars)
- Status badges (Published, Draft, Cancelled)
- Delete confirmation
- Image upload with preview

#### **Booking Management Views**
- `admin/bookings/index.blade.php` - List and manage bookings

**Features:**
- Advanced search by ticket code, name, email
- Filter by payment status, attendance status, class, date range
- Quick check-in by ticket code
- Booking table with customer info
- Status badges for payment and attendance
- Inline cancel functionality with reason
- Manual check-in buttons
- Pagination

### 3. Middleware
**AdminMiddleware.php** - Ensures user is authenticated and has admin privileges
- Located at: `app/Http/Middleware/EnsureUserIsAdmin.php`
- Registered as: `admin` middleware alias
- Checks `User::isAdmin()` method

### 4. Models Used
The admin system works with existing models:

- **ArtClass** - Art class information
  - Attributes: title, description, price_cents, capacity, class_date, etc.
  - Methods: `getSpotsAvailableAttribute()`, `getFormattedPriceAttribute()`

- **Booking** - Customer bookings
  - Attributes: ticket_code, payment_status, attendance_status
  - Methods: `checkIn()`, `cancel()`, `generateTicketCode()`

- **Payment** - Payment records
  - Attributes: amount_cents, stripe_fee_cents, net_amount_cents
  - Methods: `getFormattedAmountAttribute()`

- **User** - System users
  - Method: `isAdmin()` - Returns true if user has admin privileges

### 5. Routes Configuration
All admin routes are in `routes/web.php`:

```php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Class Management (Resource routes)
    Route::resource('classes', AdminClassController::class);

    // Booking Management
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings/check-in', [AdminBookingController::class, 'checkIn'])->name('bookings.check-in');
    Route::post('/bookings/{booking}/manual-check-in', [AdminBookingController::class, 'manualCheckIn'])->name('bookings.manual-check-in');
    Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
});
```

## Key Features

### Image Upload System
- **Storage Path:** `storage/app/public/class-images/`
- **Accepted Formats:** JPEG, PNG, GIF
- **Max Size:** 2MB
- **Access URL:** `/storage/class-images/{filename}`

**Important:** Run `php artisan storage:link` to create the public storage symlink.

### Check-in System
Two methods for checking in customers:

1. **Quick Check-in by Ticket Code**
   - Enter ticket code (e.g., FB-1234)
   - Instant validation and check-in
   - Located on bookings index page

2. **Manual Check-in from List**
   - View all bookings for a class
   - Click "Check In" button next to booking
   - Updates attendance_status to 'attended'
   - Records check-in timestamp

### Search and Filtering
**Booking Filters:**
- Search by ticket code, customer name, or email
- Filter by payment status (pending, completed, failed)
- Filter by attendance status (booked, attended, cancelled)
- Filter by specific class
- Filter by date range

### Validation Rules

#### Class Creation/Update:
- `title` - Required, max 255 characters
- `description` - Required
- `short_description` - Optional, max 500 characters
- `materials_included` - Optional
- `image` - Optional, must be image, max 2MB
- `class_date` - Required, must be future date (on create)
- `duration_minutes` - Required, 30-480 minutes
- `price_cents` - Required, integer, min 0
- `capacity` - Required, integer, 1-100
- `location` - Required, max 255 characters
- `status` - Required, must be: draft, published, cancelled

### Statistics Calculated

**Dashboard Stats:**
- Total classes count
- Upcoming classes count
- Published classes count
- Total confirmed bookings
- Upcoming bookings count
- Total revenue (gross)
- Net revenue (after Stripe fees)
- Unique customer count
- Revenue by month (last 6 months)
- Top 5 most booked classes

## Access Control

### Admin Access
Users must have `is_admin = true` in the database to access admin routes.

**Set user as admin via database:**
```sql
UPDATE users SET is_admin = 1 WHERE email = 'admin@example.com';
```

**Or via tinker:**
```php
php artisan tinker
$user = User::where('email', 'admin@example.com')->first();
$user->is_admin = true;
$user->save();
```

### Route Protection
All admin routes are protected by two middleware:
1. `auth` - Ensures user is logged in
2. `admin` - Ensures user has admin privileges

Unauthorized access returns 403 error.

## Usage Instructions

### Accessing the Admin Panel
1. Log in as an admin user
2. Navigate to `/admin/dashboard`
3. Use navigation to access Classes or Bookings

### Creating a Class
1. Go to Classes → Create New Class
2. Fill in all required fields
3. Upload an image (optional)
4. Enter price in cents (e.g., 5000 = $50.00)
5. Set status (draft/published/cancelled)
6. Click "Create Class"

### Managing Bookings
1. Go to Bookings
2. Use filters to find specific bookings
3. Check in customers using ticket code or manual button
4. Cancel bookings with optional reason

### Checking In Customers
**Method 1: Quick Check-in**
1. Customer provides ticket code
2. Enter in "Quick Check-In" field
3. Click "Check In"

**Method 2: From Booking List**
1. Find booking in list
2. Click "Check In" button
3. Confirmation message appears

## Styling

The admin panel uses Tailwind CSS with an indigo color scheme:
- Primary: Indigo-600
- Hover: Indigo-700
- Success: Green
- Warning: Yellow
- Error: Red
- Info: Blue

All views are fully responsive and work on mobile devices.

## Flash Messages

The system uses Laravel session flash messages:
- `session('success')` - Green success messages
- `session('error')` - Red error messages
- `session('warning')` - Yellow warning messages

**Example:**
```php
return redirect()->route('admin.classes.index')
    ->with('success', 'Class created successfully!');
```

## Database Requirements

Ensure your database has:
- `users.is_admin` column (boolean)
- `art_classes` table with all required fields
- `bookings` table with ticket_code, payment_status, attendance_status
- `payments` table for payment tracking

## Testing the System

1. **Create an admin user:**
```sql
UPDATE users SET is_admin = 1 WHERE id = 1;
```

2. **Access admin dashboard:**
Visit: `http://your-app.test/admin/dashboard`

3. **Test class creation:**
- Create a new class
- Upload an image
- Verify it appears in the list

4. **Test booking management:**
- Create test bookings
- Try check-in functionality
- Test search and filters

5. **Verify image storage:**
Run `php artisan storage:link` if images don't appear

## Troubleshooting

**Issue: 403 Forbidden on admin routes**
- Solution: Ensure user has `is_admin = true` in database

**Issue: Images not displaying**
- Solution: Run `php artisan storage:link`
- Verify files exist in `storage/app/public/class-images/`

**Issue: Flash messages not showing**
- Solution: Ensure admin layout is being used
- Check session configuration

**Issue: Validation errors**
- Solution: Check all required fields are filled
- Verify price is in cents (integer)
- Ensure image is under 2MB

## Future Enhancements

Potential additions:
- Email notification system
- Refund processing
- Customer management
- Reporting and exports
- Bulk operations
- Calendar view
- Waitlist management
- Discount codes

## Security Considerations

- All routes protected by auth and admin middleware
- CSRF protection on all forms
- Image upload validation
- SQL injection protection via Eloquent ORM
- XSS protection via Blade templates
- File type validation on uploads

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Admin/
│   │       ├── ClassController.php
│   │       ├── DashboardController.php
│   │       └── BookingController.php
│   └── Middleware/
│       └── EnsureUserIsAdmin.php
└── Models/
    ├── ArtClass.php
    ├── Booking.php
    ├── Payment.php
    └── User.php

resources/
└── views/
    ├── admin/
    │   ├── dashboard.blade.php
    │   ├── classes/
    │   │   ├── index.blade.php
    │   │   ├── create.blade.php
    │   │   └── edit.blade.php
    │   └── bookings/
    │       └── index.blade.php
    ├── components/
    │   └── admin-layout.blade.php
    └── layouts/
        └── admin.blade.php

storage/
└── app/
    └── public/
        └── class-images/

routes/
└── web.php
```

## Support

For issues or questions:
1. Check this documentation
2. Review Laravel logs: `storage/logs/laravel.log`
3. Verify database structure
4. Check middleware configuration
