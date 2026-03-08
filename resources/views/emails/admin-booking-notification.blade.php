<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking Notification</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" style="width: 600px; max-width: 100%; border-collapse: collapse; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #9333ea 0%, #db2777 100%); padding: 30px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: bold;">New Booking!</h1>
                        </td>
                    </tr>

                    <!-- Booking Details -->
                    <tr>
                        <td style="padding: 30px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6;">
                                        <p style="margin: 0; color: #6b7280; font-size: 13px;">Customer</p>
                                        <p style="margin: 4px 0 0; color: #1f2937; font-size: 16px; font-weight: 600;">{{ $customer?->name ?? 'N/A' }}</p>
                                        <p style="margin: 2px 0 0; color: #6b7280; font-size: 14px;">{{ $customer?->email ?? 'N/A' }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6;">
                                        <p style="margin: 0; color: #6b7280; font-size: 13px;">Class</p>
                                        <p style="margin: 4px 0 0; color: #1f2937; font-size: 16px; font-weight: 600;">{{ $artClass?->title ?? 'Art Class' }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6;">
                                        <p style="margin: 0; color: #6b7280; font-size: 13px;">Date & Time</p>
                                        <p style="margin: 4px 0 0; color: #1f2937; font-size: 16px; font-weight: 600;">{{ $artClass?->class_date?->format('l, F j, Y \a\t g:i A') ?? 'N/A' }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6;">
                                        <p style="margin: 0; color: #6b7280; font-size: 13px;">Ticket Code</p>
                                        <p style="margin: 4px 0 0; color: #9333ea; font-size: 20px; font-weight: bold; letter-spacing: 2px;">{{ $ticketCode }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f3f4f6;">
                                        <p style="margin: 0; color: #6b7280; font-size: 13px;">Price</p>
                                        <p style="margin: 4px 0 0; color: #1f2937; font-size: 16px; font-weight: 600;">{{ $artClass?->formatted_price ?? '$0.00' }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 0;">
                                        <p style="margin: 0; color: #6b7280; font-size: 13px;">Spots Remaining</p>
                                        <p style="margin: 4px 0 0; color: #1f2937; font-size: 16px; font-weight: 600;">{{ $artClass?->spots_available ?? 'N/A' }} / {{ $artClass?->capacity ?? 'N/A' }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Admin Link -->
                    <tr>
                        <td style="padding: 0 40px 30px; text-align: center;">
                            <a href="{{ url('/admin/bookings') }}" style="display: inline-block; background: linear-gradient(135deg, #9333ea 0%, #db2777 100%); color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-size: 15px; font-weight: 600;">View in Admin Panel</a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">&copy; {{ date('Y') }} FrizzBoss Admin Notification</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
