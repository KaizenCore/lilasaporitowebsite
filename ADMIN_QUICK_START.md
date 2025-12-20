# Admin System - Quick Start Guide

## Initial Setup (One-Time)

### 1. Create Storage Symlink
```bash
php artisan storage:link
```
This creates a symbolic link from `public/storage` to `storage/app/public` so uploaded images are accessible.

### 2. Create Admin User
Run one of these commands to make a user an admin:

**Option A: Using Tinker**
```bash
php artisan tinker
```
Then in tinker:
```php
$user = User::where('email', 'your-email@example.com')->first();
$user->is_admin = true;
$user->save();
exit
```

**Option B: Direct SQL**
```sql
UPDATE users SET is_admin = 1 WHERE email = 'your-email@example.com';
```

**Option C: During Registration**
After registering, update the user in the database.

### 3. Verify Middleware is Registered
Check `bootstrap/app.php` contains:
```php
$middleware->alias([
    'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
]);
```

## Accessing the Admin Panel

1. Log in to your account
2. Navigate to: `http://your-app.test/admin/dashboard`
3. You should see the admin dashboard with statistics

## Quick Tour

### Admin Dashboard (`/admin/dashboard`)
- View statistics: total classes, bookings, revenue
- See upcoming classes
- Review recent bookings
- Check popular classes

### Classes Management (`/admin/classes`)
- **List Classes**: View all classes with status, capacity, bookings
- **Create Class**: Click "Create New Class" button
  - Fill in title, description, date/time
  - Upload image (optional)
  - Set price in **cents** (e.g., 5000 = $50.00)
  - Set capacity and location
  - Choose status: Draft, Published, or Cancelled
- **Edit Class**: Click "Edit" on any class
- **Delete Class**: Click "Delete" (only if no confirmed bookings)

### Bookings Management (`/admin/bookings`)
- **View All Bookings**: See all customer bookings
- **Search**: By ticket code, customer name, or email
- **Filter**: By payment status, attendance, class, or date
- **Quick Check-In**: Enter ticket code to check in customer
- **Manual Check-In**: Click "Check In" button next to booking
- **Cancel Booking**: Click "Cancel" and optionally add reason

## Common Tasks

### Create Your First Class
1. Go to `/admin/classes/create`
2. Enter:
   - **Title**: "Watercolor Basics"
   - **Short Description**: "Learn fundamental watercolor techniques"
   - **Description**: "In this 2-hour class, you'll learn..."
   - **Materials**: "All materials provided including brushes, paints, and paper"
   - **Date**: Pick a future date and time
   - **Duration**: 120 (minutes)
   - **Price**: 5000 (cents = $50.00)
   - **Capacity**: 10
   - **Location**: "Art Studio, 123 Main St"
   - **Status**: Published
3. Upload an image (optional)
4. Click "Create Class"

### Check In a Customer
**Method 1: By Ticket Code**
1. Go to `/admin/bookings`
2. Customer tells you their ticket code (e.g., "FB-1234")
3. Enter code in "Quick Check-In" field
4. Click "Check In"
5. Success message appears

**Method 2: From List**
1. Go to `/admin/bookings`
2. Find the customer's booking in the table
3. Click "Check In" button
4. Booking status changes to "Checked In"

### Cancel a Booking
1. Go to `/admin/bookings`
2. Find the booking
3. Click "Cancel" button
4. Enter cancellation reason (optional)
5. Click "Confirm Cancel"

### Edit Class Details
1. Go to `/admin/classes`
2. Click "Edit" on the class
3. Update any fields
4. Upload new image if needed (replaces old one)
5. Click "Update Class"

## Important Notes

### Price Format
- Always enter prices in **cents**
- $50.00 = 5000 cents
- $25.50 = 2550 cents
- $100.00 = 10000 cents

### Image Uploads
- Supported: JPG, PNG, GIF
- Max size: 2MB
- Stored in: `storage/app/public/class-images/`
- Accessible at: `/storage/class-images/filename.jpg`

### Class Status
- **Draft**: Not visible to customers, can edit freely
- **Published**: Visible to customers, available for booking
- **Cancelled**: Class cancelled, bookings notified

### Booking Status
- **Payment Status**:
  - Pending: Payment not completed
  - Completed: Payment successful
  - Failed: Payment failed

- **Attendance Status**:
  - Booked: Customer registered but not checked in
  - Attended: Customer checked in
  - Cancelled: Booking cancelled

### Deletion Rules
- Can only delete classes with NO confirmed bookings
- To remove a class with bookings, cancel it instead
- Set status to "Cancelled"

## Troubleshooting

### Can't Access Admin Panel (403 Error)
**Problem**: User doesn't have admin privileges
**Solution**: Run this in tinker:
```php
$user = User::find(YOUR_USER_ID);
$user->is_admin = true;
$user->save();
```

### Images Not Showing
**Problem**: Storage link not created
**Solution**: Run `php artisan storage:link`

### "Class date must be after now" Error
**Problem**: Selected date is in the past
**Solution**: Choose a future date and time

### Can't Delete Class
**Problem**: Class has confirmed bookings
**Solution**: Change status to "Cancelled" instead

## Navigation Tips

### Admin Navigation Bar
- **Dashboard**: Overview and statistics
- **Classes**: Manage all art classes
- **Bookings**: Manage customer bookings

### User Menu (Top Right)
- **Public Site**: Return to public homepage
- **User Dashboard**: Your user dashboard
- **Profile**: Edit your profile
- **Log Out**: Sign out

## Keyboard Shortcuts
(Browser standard)
- `Ctrl+F` / `Cmd+F`: Search on page
- `Tab`: Navigate between form fields
- `Enter`: Submit focused form

## Best Practices

1. **Create classes as Draft first** - Review before publishing
2. **Upload class images** - Better customer experience
3. **Set realistic capacity** - Based on your space
4. **Check in customers early** - Avoid bottlenecks at class start
5. **Use filters** - Find bookings quickly
6. **Review dashboard regularly** - Monitor booking trends

## Quick Reference

### URL Structure
- Dashboard: `/admin/dashboard`
- Classes List: `/admin/classes`
- Create Class: `/admin/classes/create`
- Edit Class: `/admin/classes/{id}/edit`
- Bookings: `/admin/bookings`

### Common Form Fields
- **title**: Class name (required)
- **description**: Full details (required)
- **price_cents**: Price in cents (required)
- **capacity**: Max students (required, 1-100)
- **class_date**: When class happens (required)
- **duration_minutes**: Length in minutes (required, 30-480)
- **location**: Where class is held (required)
- **status**: draft/published/cancelled (required)

### Filter Options
- Payment: All, Pending, Completed, Failed
- Attendance: All, Booked, Attended, Cancelled
- Class: All Classes or specific class
- Search: Ticket code, name, email

## Next Steps

After setup:
1. Create a test class
2. Practice checking in bookings
3. Try the search and filter features
4. Review the dashboard statistics
5. Familiarize yourself with all navigation options

## Need Help?

Refer to `ADMIN_SYSTEM_README.md` for:
- Detailed technical documentation
- Complete feature list
- Database structure
- Security considerations
- Advanced usage
