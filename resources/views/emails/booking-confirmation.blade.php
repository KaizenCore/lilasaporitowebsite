<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
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

                    <!-- Success Message -->
                    <tr>
                        <td style="padding: 40px 40px 20px; text-align: center;">
                            <div style="width: 60px; height: 60px; background-color: #10b981; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                                <span style="color: white; font-size: 30px;">&#10003;</span>
                            </div>
                            <h2 style="margin: 0; color: #1f2937; font-size: 24px;">Booking Confirmed!</h2>
                            <p style="margin: 10px 0 0; color: #6b7280; font-size: 16px;">Thank you for booking with FrizzBoss, {{ $user?->name ?? 'there' }}!</p>
                        </td>
                    </tr>

                    <!-- Ticket Code -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <div style="background: linear-gradient(135deg, #faf5ff 0%, #fdf2f8 100%); border: 2px dashed #9333ea; border-radius: 12px; padding: 20px; text-align: center;">
                                <p style="margin: 0 0 5px; color: #6b7280; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Your Ticket Code</p>
                                <p style="margin: 0; color: #9333ea; font-size: 32px; font-weight: bold; letter-spacing: 3px;">{{ $ticketCode }}</p>
                                <p style="margin: 10px 0 0; color: #6b7280; font-size: 12px;">Show this code when you arrive</p>
                            </div>
                        </td>
                    </tr>

                    <!-- Class Details -->
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <h3 style="margin: 0 0 20px; color: #1f2937; font-size: 18px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">Class Details</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">Class</p>
                                        <p style="margin: 5px 0 0; color: #1f2937; font-size: 16px; font-weight: 600;">{{ $artClass?->title ?? 'Art Class' }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">Date & Time</p>
                                        <p style="margin: 5px 0 0; color: #1f2937; font-size: 16px; font-weight: 600;">{{ $artClass?->class_date?->format('l, F j, Y') ?? 'N/A' }}</p>
                                        <p style="margin: 2px 0 0; color: #6b7280; font-size: 14px;">{{ $artClass?->class_date?->format('g:i A') ?? '' }} {{ $artClass ? '(' . $artClass->duration_minutes . ' minutes)' : '' }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">Location</p>
                                        <p style="margin: 5px 0 0; color: #1f2937; font-size: 16px; font-weight: 600;">{{ $artClass?->location ?? 'N/A' }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0;">
                                        <p style="margin: 0; color: #6b7280; font-size: 14px;">Amount Paid</p>
                                        <p style="margin: 5px 0 0; color: #9333ea; font-size: 20px; font-weight: bold;">{{ $artClass?->formatted_price ?? '$0.00' }}</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- What to Bring -->
                    @if($artClass?->materials_included)
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <div style="background-color: #f0fdf4; border-radius: 12px; padding: 20px;">
                                <h4 style="margin: 0 0 10px; color: #166534; font-size: 16px;">What's Included</h4>
                                <p style="margin: 0; color: #15803d; font-size: 14px; line-height: 1.6;">{{ $artClass->materials_included }}</p>
                            </div>
                        </td>
                    </tr>
                    @endif

                    <!-- CTA Button -->
                    <tr>
                        <td style="padding: 0 40px 30px; text-align: center;">
                            <a href="{{ route('bookings.index') }}" style="display: inline-block; background: linear-gradient(135deg, #9333ea 0%, #db2777 100%); color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 16px; font-weight: 600;">View My Bookings</a>
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
