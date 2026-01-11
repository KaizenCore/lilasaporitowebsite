<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Cancelled</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f3f4f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" style="width: 600px; max-width: 100%; border-collapse: collapse; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #9333ea 0%, #db2777 100%); padding: 40px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: bold;">FrizzBoss</h1>
                            <p style="margin: 10px 0 0; color: rgba(255, 255, 255, 0.9); font-size: 16px;">Art Classes & Creative Experiences</p>
                        </td>
                    </tr>

                    <!-- Cancellation Message -->
                    <tr>
                        <td style="padding: 40px 40px 20px; text-align: center;">
                            <div style="width: 60px; height: 60px; background-color: #ef4444; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                                <span style="color: white; font-size: 30px;">&#10005;</span>
                            </div>
                            <h2 style="margin: 0; color: #1f2937; font-size: 24px;">Booking Cancelled</h2>
                            <p style="margin: 10px 0 0; color: #6b7280; font-size: 16px;">Hi {{ $user?->name ?? 'there' }}, your booking has been cancelled.</p>
                        </td>
                    </tr>

                    <!-- Class Details -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <div style="background-color: #fef2f2; border-radius: 12px; padding: 20px;">
                                <h3 style="margin: 0 0 15px; color: #991b1b; font-size: 16px;">Cancelled Booking Details</h3>

                                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="padding: 8px 0;">
                                            <p style="margin: 0; color: #6b7280; font-size: 14px;">Class</p>
                                            <p style="margin: 3px 0 0; color: #1f2937; font-size: 16px; font-weight: 600;">{{ $artClass?->title ?? 'Art Class' }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0;">
                                            <p style="margin: 0; color: #6b7280; font-size: 14px;">Original Date</p>
                                            <p style="margin: 3px 0 0; color: #1f2937; font-size: 16px;">{{ $artClass?->class_date?->format('l, F j, Y') ?? 'N/A' }} {{ $artClass?->class_date ? 'at ' . $artClass->class_date->format('g:i A') : '' }}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0;">
                                            <p style="margin: 0; color: #6b7280; font-size: 14px;">Cancelled On</p>
                                            <p style="margin: 3px 0 0; color: #1f2937; font-size: 16px;">{{ $booking->cancelled_at?->format('F j, Y \a\t g:i A') ?? now()->format('F j, Y \a\t g:i A') }}</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>

                    <!-- Refund Info -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <div style="background-color: #fefce8; border: 1px solid #fef08a; border-radius: 12px; padding: 20px;">
                                <h4 style="margin: 0 0 10px; color: #854d0e; font-size: 16px;">Refund Information</h4>
                                <p style="margin: 0; color: #713f12; font-size: 14px; line-height: 1.6;">If you would like to request a refund, please contact us on Instagram or reply to this email. Refunds are handled on a case-by-case basis according to our cancellation policy.</p>
                            </div>
                        </td>
                    </tr>

                    <!-- CTA Button -->
                    <tr>
                        <td style="padding: 0 40px 30px; text-align: center;">
                            <a href="{{ route('classes.index') }}" style="display: inline-block; background: linear-gradient(135deg, #9333ea 0%, #db2777 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 16px; font-weight: 600;">Browse Other Classes</a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px 40px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px; color: #6b7280; font-size: 14px;">Questions? Reply to this email or reach out on Instagram</p>
                            <p style="margin: 0;">
                                <a href="https://instagram.com/frizzboss" style="color: #9333ea; text-decoration: none; font-weight: 600;">@frizzboss</a>
                            </p>
                            <p style="margin: 20px 0 0; color: #9ca3af; font-size: 12px;">&copy; {{ date('Y') }} FrizzBoss. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
