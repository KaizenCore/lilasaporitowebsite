<?php

namespace Database\Seeders;

use App\Models\ArtClass;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed the database with demo data for testing.
     */
    public function run(): void
    {
        $this->createUsers();
        $this->createProductCategories();
        $this->createProducts();
        $this->createArtClasses();
        $this->createBookings();
        $this->createPayments();
    }

    private function createUsers(): void
    {
        $users = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'James Wilson',
                'email' => 'james@example.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Olivia Brown',
                'email' => 'olivia@example.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }

    private function createProductCategories(): void
    {
        $categories = [
            [
                'name' => 'Original Paintings',
                'description' => 'One-of-a-kind original artwork created by Lila Saporito.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Art Prints',
                'description' => 'High-quality prints of popular artworks, perfect for any space.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Digital Downloads',
                'description' => 'Instant digital downloads including tutorials, brushes, and templates.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Art Supplies',
                'description' => 'Curated art supplies and materials recommended by Lila.',
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $categoryData) {
            ProductCategory::firstOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }
    }

    private function createProducts(): void
    {
        $categories = ProductCategory::all()->keyBy('name');
        $admin = User::where('is_admin', true)->first();

        $products = [
            // Original Paintings
            [
                'category_id' => $categories['Original Paintings']->id ?? null,
                'title' => 'Sunset Over the Mountains',
                'description' => 'A breathtaking oil painting capturing the warm golden hues of a sunset cascading over majestic mountain peaks. This piece measures 24x36 inches and is painted on premium stretched canvas with archival quality oils.',
                'short_description' => 'Original oil painting, 24x36 inches',
                'price_cents' => 85000,
                'compare_at_price_cents' => null,
                'product_type' => 'physical',
                'stock_quantity' => 1,
                'sku' => 'OP-001',
                'weight_grams' => 2000,
                'requires_shipping' => true,
                'is_featured' => true,
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            [
                'category_id' => $categories['Original Paintings']->id ?? null,
                'title' => 'Ocean Dreams',
                'description' => 'An abstract interpretation of the ocean at dawn, featuring swirling blues, teals, and hints of gold. Created with acrylic and mixed media on gallery-wrapped canvas. Size: 30x40 inches.',
                'short_description' => 'Abstract acrylic painting, 30x40 inches',
                'price_cents' => 120000,
                'compare_at_price_cents' => null,
                'product_type' => 'physical',
                'stock_quantity' => 1,
                'sku' => 'OP-002',
                'weight_grams' => 2500,
                'requires_shipping' => true,
                'is_featured' => true,
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            [
                'category_id' => $categories['Original Paintings']->id ?? null,
                'title' => 'Spring Garden',
                'description' => 'A vibrant watercolor depicting a lush spring garden in full bloom. Features roses, peonies, and wildflowers in soft pinks and purples. Framed and ready to hang. Size: 16x20 inches.',
                'short_description' => 'Watercolor painting, framed, 16x20 inches',
                'price_cents' => 45000,
                'compare_at_price_cents' => 55000,
                'product_type' => 'physical',
                'stock_quantity' => 1,
                'sku' => 'OP-003',
                'weight_grams' => 1500,
                'requires_shipping' => true,
                'is_featured' => false,
                'status' => 'published',
                'created_by' => $admin?->id,
            ],

            // Art Prints
            [
                'category_id' => $categories['Art Prints']->id ?? null,
                'title' => 'Wildflower Meadow Print',
                'description' => 'Museum-quality giclée print on archival paper. Reproduces the soft colors and delicate details of the original watercolor perfectly. Available unframed, ready for your choice of frame.',
                'short_description' => 'Giclée print on archival paper, 11x14 inches',
                'price_cents' => 3500,
                'compare_at_price_cents' => null,
                'product_type' => 'physical',
                'stock_quantity' => 50,
                'sku' => 'PR-001',
                'weight_grams' => 100,
                'requires_shipping' => true,
                'is_featured' => true,
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            [
                'category_id' => $categories['Art Prints']->id ?? null,
                'title' => 'Abstract Waves Print Set',
                'description' => 'A set of 3 complementary abstract prints featuring ocean-inspired designs. Perfect for creating a gallery wall. Each print is 8x10 inches on premium matte paper.',
                'short_description' => 'Set of 3 prints, 8x10 inches each',
                'price_cents' => 5500,
                'compare_at_price_cents' => 7500,
                'product_type' => 'physical',
                'stock_quantity' => 30,
                'sku' => 'PR-002',
                'weight_grams' => 200,
                'requires_shipping' => true,
                'is_featured' => false,
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            [
                'category_id' => $categories['Art Prints']->id ?? null,
                'title' => 'Botanical Collection Print',
                'description' => 'Elegant botanical illustration featuring vintage-style drawings of herbs and flowers. Printed on textured cotton rag paper for an authentic fine art feel.',
                'short_description' => 'Fine art print on cotton rag paper, 12x16 inches',
                'price_cents' => 4200,
                'compare_at_price_cents' => null,
                'product_type' => 'physical',
                'stock_quantity' => 25,
                'sku' => 'PR-003',
                'weight_grams' => 120,
                'requires_shipping' => true,
                'is_featured' => false,
                'status' => 'published',
                'created_by' => $admin?->id,
            ],

            // Digital Downloads
            [
                'category_id' => $categories['Digital Downloads']->id ?? null,
                'title' => 'Watercolor Basics Video Course',
                'description' => 'A comprehensive 2-hour video course covering watercolor fundamentals. Learn color mixing, brush techniques, wet-on-wet methods, and create 3 complete paintings. Includes downloadable reference photos and supply list.',
                'short_description' => '2-hour video course with resources',
                'price_cents' => 4900,
                'compare_at_price_cents' => null,
                'product_type' => 'digital',
                'stock_quantity' => null,
                'sku' => 'DL-001',
                'weight_grams' => null,
                'requires_shipping' => false,
                'is_featured' => true,
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            [
                'category_id' => $categories['Digital Downloads']->id ?? null,
                'title' => 'Procreate Brush Pack - Oils',
                'description' => '25 custom Procreate brushes that simulate traditional oil painting techniques. Includes palette knives, bristle brushes, blenders, and detail brushes. Compatible with Procreate 5.0+.',
                'short_description' => '25 custom Procreate brushes',
                'price_cents' => 1500,
                'compare_at_price_cents' => 2500,
                'product_type' => 'digital',
                'stock_quantity' => null,
                'sku' => 'DL-002',
                'weight_grams' => null,
                'requires_shipping' => false,
                'is_featured' => false,
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            [
                'category_id' => $categories['Digital Downloads']->id ?? null,
                'title' => 'Color Mixing Guide PDF',
                'description' => 'A comprehensive 30-page PDF guide to color theory and mixing. Includes color wheels, mixing charts, and exercises to improve your color skills. Perfect for beginners and intermediate artists.',
                'short_description' => '30-page PDF guide',
                'price_cents' => 900,
                'compare_at_price_cents' => null,
                'product_type' => 'digital',
                'stock_quantity' => null,
                'sku' => 'DL-003',
                'weight_grams' => null,
                'requires_shipping' => false,
                'is_featured' => false,
                'status' => 'published',
                'created_by' => $admin?->id,
            ],

            // Art Supplies
            [
                'category_id' => $categories['Art Supplies']->id ?? null,
                'title' => 'Professional Watercolor Set',
                'description' => 'Lila\'s recommended 24-color professional watercolor set. Artist-grade pigments with excellent lightfastness. Includes a metal travel tin and mixing palette.',
                'short_description' => '24-color professional watercolor set',
                'price_cents' => 8900,
                'compare_at_price_cents' => 9900,
                'product_type' => 'physical',
                'stock_quantity' => 15,
                'sku' => 'AS-001',
                'weight_grams' => 400,
                'requires_shipping' => true,
                'is_featured' => true,
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            [
                'category_id' => $categories['Art Supplies']->id ?? null,
                'title' => 'Starter Brush Set',
                'description' => 'A curated set of 10 essential brushes for watercolor and acrylic painting. Includes round, flat, and detail brushes in various sizes. Synthetic bristles, wooden handles.',
                'short_description' => '10-piece brush set',
                'price_cents' => 3200,
                'compare_at_price_cents' => null,
                'product_type' => 'physical',
                'stock_quantity' => 40,
                'sku' => 'AS-002',
                'weight_grams' => 150,
                'requires_shipping' => true,
                'is_featured' => false,
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            [
                'category_id' => $categories['Art Supplies']->id ?? null,
                'title' => 'Mixed Media Paper Pad',
                'description' => 'Heavy-weight 140lb mixed media paper pad. Works beautifully with watercolors, acrylics, markers, and pencils. 30 sheets, 9x12 inches, cold press texture.',
                'short_description' => '30-sheet paper pad, 9x12 inches',
                'price_cents' => 1800,
                'compare_at_price_cents' => null,
                'product_type' => 'physical',
                'stock_quantity' => 60,
                'sku' => 'AS-003',
                'weight_grams' => 800,
                'requires_shipping' => true,
                'is_featured' => false,
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['sku' => $productData['sku']],
                $productData
            );
        }
    }

    private function createArtClasses(): void
    {
        $admin = User::where('is_admin', true)->first();

        $classes = [
            [
                'title' => 'Watercolor Basics for Beginners',
                'description' => 'Learn the fundamentals of watercolor painting in this beginner-friendly workshop. We\'ll cover essential techniques including wet-on-wet, wet-on-dry, color mixing, and brush control. By the end of the class, you\'ll have created your own beautiful watercolor painting to take home. All materials included.',
                'short_description' => 'Perfect introduction to watercolor painting for absolute beginners.',
                'materials_included' => 'Watercolor paper, paint set, brushes, palette, reference photos',
                'class_date' => now()->addDays(7)->setHour(10)->setMinute(0),
                'duration_minutes' => 180,
                'price_cents' => 7500,
                'capacity' => 12,
                'location' => 'Lila\'s Art Studio, 123 Creative Lane',
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Abstract Acrylic Workshop',
                'description' => 'Unleash your creativity in this energetic abstract painting workshop! Explore color theory, texture techniques, and intuitive mark-making. No experience necessary - just a willingness to experiment and have fun. You\'ll leave with a unique abstract piece that reflects your personal style.',
                'short_description' => 'Express yourself through bold colors and dynamic compositions.',
                'materials_included' => 'Canvas (16x20), acrylic paints, palette knives, brushes, apron',
                'class_date' => now()->addDays(10)->setHour(14)->setMinute(0),
                'duration_minutes' => 150,
                'price_cents' => 8500,
                'capacity' => 10,
                'location' => 'Lila\'s Art Studio, 123 Creative Lane',
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Landscape Painting Masterclass',
                'description' => 'Take your landscape painting to the next level in this intermediate workshop. We\'ll focus on creating depth, atmospheric perspective, and capturing natural light. Working from reference photos, you\'ll learn professional techniques for painting skies, mountains, trees, and water.',
                'short_description' => 'Intermediate workshop focusing on realistic landscape techniques.',
                'materials_included' => 'Canvas, oil or acrylic paints (your choice), brushes, medium',
                'class_date' => now()->addDays(14)->setHour(10)->setMinute(0),
                'duration_minutes' => 240,
                'price_cents' => 12000,
                'capacity' => 8,
                'location' => 'Lila\'s Art Studio, 123 Creative Lane',
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Paint & Sip Evening',
                'description' => 'Join us for a relaxing evening of painting and socializing! Perfect for date nights, girls\' nights out, or just a fun creative escape. We\'ll guide you step-by-step through creating a beautiful seasonal painting while you enjoy complimentary wine and snacks.',
                'short_description' => 'Fun social painting event with wine and snacks included.',
                'materials_included' => 'Canvas, paints, brushes, wine, cheese board, and good vibes!',
                'class_date' => now()->addDays(5)->setHour(18)->setMinute(30),
                'duration_minutes' => 120,
                'price_cents' => 6500,
                'capacity' => 20,
                'location' => 'The Gallery Lounge, 456 Art District Ave',
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Portrait Drawing Fundamentals',
                'description' => 'Master the art of portrait drawing in this focused workshop. Learn proper proportions, facial features, shading techniques, and how to capture likeness. We\'ll work from live models and photographs to develop your observational skills.',
                'short_description' => 'Learn to draw realistic portraits with confidence.',
                'materials_included' => 'Drawing paper, graphite pencils, erasers, blending tools',
                'class_date' => now()->addDays(21)->setHour(13)->setMinute(0),
                'duration_minutes' => 180,
                'price_cents' => 8000,
                'capacity' => 10,
                'location' => 'Lila\'s Art Studio, 123 Creative Lane',
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Kids Art Adventure (Ages 6-12)',
                'description' => 'A fun-filled art session designed specifically for young artists! Kids will explore different materials and techniques while creating colorful artwork. Projects are designed to encourage creativity, build confidence, and most importantly - have fun!',
                'short_description' => 'Creative art workshop for children ages 6-12.',
                'materials_included' => 'All art supplies, smocks, snacks, take-home artwork',
                'class_date' => now()->addDays(8)->setHour(10)->setMinute(0),
                'duration_minutes' => 90,
                'price_cents' => 4500,
                'capacity' => 15,
                'location' => 'Community Arts Center, 789 Main Street',
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Botanical Illustration Workshop',
                'description' => 'Discover the beautiful art of botanical illustration. Learn to observe and render plants with scientific accuracy while maintaining artistic beauty. We\'ll cover pencil sketching, ink work, and watercolor techniques specific to botanical art.',
                'short_description' => 'Create detailed botanical artwork with traditional techniques.',
                'materials_included' => 'Botanical specimens, watercolor paper, fine brushes, paints, magnifying glass',
                'class_date' => now()->addDays(18)->setHour(10)->setMinute(0),
                'duration_minutes' => 210,
                'price_cents' => 9500,
                'capacity' => 8,
                'location' => 'Lila\'s Art Studio, 123 Creative Lane',
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            [
                'title' => 'Mixed Media Collage',
                'description' => 'Explore the exciting world of mixed media art! Combine paint, paper, fabric, and found objects to create layered, textured artwork. Perfect for artists who love to experiment and think outside the box.',
                'short_description' => 'Experiment with various materials to create unique collages.',
                'materials_included' => 'Canvas board, acrylic paints, collage papers, fabric scraps, gel medium, ephemera',
                'class_date' => now()->addDays(25)->setHour(14)->setMinute(0),
                'duration_minutes' => 150,
                'price_cents' => 7000,
                'capacity' => 12,
                'location' => 'Lila\'s Art Studio, 123 Creative Lane',
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            // Past class for testing
            [
                'title' => 'Oil Painting Intensive',
                'description' => 'An immersive full-day workshop dedicated to oil painting techniques. From color mixing to glazing, learn the methods used by the masters.',
                'short_description' => 'Full-day intensive oil painting workshop.',
                'materials_included' => 'Canvas, oil paints, brushes, medium, palette',
                'class_date' => now()->subDays(5)->setHour(9)->setMinute(0),
                'duration_minutes' => 360,
                'price_cents' => 15000,
                'capacity' => 8,
                'location' => 'Lila\'s Art Studio, 123 Creative Lane',
                'status' => 'published',
                'created_by' => $admin?->id,
            ],
            // Draft class
            [
                'title' => 'Night Sky Painting (Coming Soon)',
                'description' => 'Learn to paint stunning night skies, galaxies, and aurora borealis. This workshop is currently being planned.',
                'short_description' => 'Paint cosmic scenes and starry nights.',
                'materials_included' => 'TBD',
                'class_date' => now()->addDays(45)->setHour(19)->setMinute(0),
                'duration_minutes' => 180,
                'price_cents' => 8500,
                'capacity' => 12,
                'location' => 'TBD',
                'status' => 'draft',
                'created_by' => $admin?->id,
            ],
        ];

        foreach ($classes as $classData) {
            ArtClass::firstOrCreate(
                ['title' => $classData['title']],
                $classData
            );
        }
    }

    private function createBookings(): void
    {
        $users = User::where('is_admin', false)->get();
        $classes = ArtClass::where('status', 'published')->get();

        if ($users->isEmpty() || $classes->isEmpty()) {
            return;
        }

        $bookings = [
            // Completed bookings for past class
            [
                'user_id' => $users[0]->id ?? null,
                'art_class_id' => $classes->where('title', 'Oil Painting Intensive')->first()?->id,
                'payment_status' => 'completed',
                'attendance_status' => 'attended',
                'checked_in_at' => now()->subDays(5)->setHour(9)->setMinute(5),
            ],
            [
                'user_id' => $users[1]->id ?? null,
                'art_class_id' => $classes->where('title', 'Oil Painting Intensive')->first()?->id,
                'payment_status' => 'completed',
                'attendance_status' => 'attended',
                'checked_in_at' => now()->subDays(5)->setHour(9)->setMinute(2),
            ],
            [
                'user_id' => $users[2]->id ?? null,
                'art_class_id' => $classes->where('title', 'Oil Painting Intensive')->first()?->id,
                'payment_status' => 'completed',
                'attendance_status' => 'no_show',
            ],

            // Upcoming bookings
            [
                'user_id' => $users[0]->id ?? null,
                'art_class_id' => $classes->where('title', 'Watercolor Basics for Beginners')->first()?->id,
                'payment_status' => 'completed',
                'attendance_status' => 'booked',
            ],
            [
                'user_id' => $users[1]->id ?? null,
                'art_class_id' => $classes->where('title', 'Watercolor Basics for Beginners')->first()?->id,
                'payment_status' => 'completed',
                'attendance_status' => 'booked',
            ],
            [
                'user_id' => $users[2]->id ?? null,
                'art_class_id' => $classes->where('title', 'Watercolor Basics for Beginners')->first()?->id,
                'payment_status' => 'completed',
                'attendance_status' => 'booked',
            ],
            [
                'user_id' => $users[3]->id ?? null,
                'art_class_id' => $classes->where('title', 'Watercolor Basics for Beginners')->first()?->id,
                'payment_status' => 'pending',
                'attendance_status' => 'booked',
            ],

            [
                'user_id' => $users[0]->id ?? null,
                'art_class_id' => $classes->where('title', 'Abstract Acrylic Workshop')->first()?->id,
                'payment_status' => 'completed',
                'attendance_status' => 'booked',
            ],
            [
                'user_id' => $users[4]->id ?? null,
                'art_class_id' => $classes->where('title', 'Abstract Acrylic Workshop')->first()?->id,
                'payment_status' => 'completed',
                'attendance_status' => 'booked',
            ],

            [
                'user_id' => $users[1]->id ?? null,
                'art_class_id' => $classes->where('title', 'Paint & Sip Evening')->first()?->id,
                'payment_status' => 'completed',
                'attendance_status' => 'booked',
            ],
            [
                'user_id' => $users[2]->id ?? null,
                'art_class_id' => $classes->where('title', 'Paint & Sip Evening')->first()?->id,
                'payment_status' => 'completed',
                'attendance_status' => 'booked',
            ],
            [
                'user_id' => $users[3]->id ?? null,
                'art_class_id' => $classes->where('title', 'Paint & Sip Evening')->first()?->id,
                'payment_status' => 'completed',
                'attendance_status' => 'booked',
            ],
            [
                'user_id' => $users[4]->id ?? null,
                'art_class_id' => $classes->where('title', 'Paint & Sip Evening')->first()?->id,
                'payment_status' => 'completed',
                'attendance_status' => 'booked',
            ],

            // Cancelled booking
            [
                'user_id' => $users[3]->id ?? null,
                'art_class_id' => $classes->where('title', 'Landscape Painting Masterclass')->first()?->id,
                'payment_status' => 'refunded',
                'attendance_status' => 'cancelled',
                'cancelled_at' => now()->subDays(2),
                'cancellation_reason' => 'Schedule conflict',
            ],
        ];

        foreach ($bookings as $bookingData) {
            if (empty($bookingData['user_id']) || empty($bookingData['art_class_id'])) {
                continue;
            }

            Booking::firstOrCreate(
                [
                    'user_id' => $bookingData['user_id'],
                    'art_class_id' => $bookingData['art_class_id'],
                ],
                $bookingData
            );
        }
    }

    private function createPayments(): void
    {
        // Get all completed bookings that don't have payments yet
        $completedBookings = Booking::where('payment_status', 'completed')
            ->whereDoesntHave('payment')
            ->with('artClass')
            ->get();

        foreach ($completedBookings as $booking) {
            if (!$booking->artClass) {
                continue;
            }

            $amountCents = $booking->artClass->price_cents;

            // Calculate Stripe fee (2.9% + $0.30)
            $stripeFee = (int) ($amountCents * 0.029) + 30;
            $netAmount = $amountCents - $stripeFee;

            Payment::create([
                'booking_id' => $booking->id,
                'stripe_payment_intent_id' => 'pi_demo_' . uniqid(),
                'stripe_charge_id' => 'ch_demo_' . uniqid(),
                'amount_cents' => $amountCents,
                'currency' => 'usd',
                'payment_method' => 'card',
                'status' => 'succeeded',
                'stripe_fee_cents' => $stripeFee,
                'net_amount_cents' => $netAmount,
                'created_at' => $booking->created_at,
                'updated_at' => $booking->created_at,
            ]);
        }
    }
}
