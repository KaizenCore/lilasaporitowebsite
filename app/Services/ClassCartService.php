<?php

namespace App\Services;

use App\Models\ArtClass;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ClassCartService
{
    const CART_SESSION_KEY = 'class_cart';

    /**
     * Add class to cart (1 ticket per class, no quantity)
     */
    public function add($artClassId)
    {
        $artClass = ArtClass::published()
            ->upcoming()
            ->findOrFail($artClassId);

        // Validate class is available
        if ($artClass->is_full) {
            throw new \Exception('This class is full.');
        }

        if ($artClass->is_past) {
            throw new \Exception('This class has already occurred.');
        }

        // Check if user already has a booking for this class
        if (Auth::check()) {
            $existingBooking = Booking::where('user_id', Auth::id())
                ->where('art_class_id', $artClassId)
                ->whereIn('payment_status', ['pending', 'completed'])
                ->whereIn('attendance_status', ['booked', 'attended'])
                ->exists();

            if ($existingBooking) {
                throw new \Exception('You already have a booking for this class.');
            }
        }

        $cart = $this->get();

        // Prevent duplicate in cart
        if (isset($cart[$artClassId])) {
            throw new \Exception('This class is already in your cart.');
        }

        $cart[$artClassId] = [
            'art_class_id' => $artClass->id,
            'title' => $artClass->title,
            'slug' => $artClass->slug,
            'class_date' => $artClass->class_date->toIso8601String(),
            'class_date_formatted' => $artClass->class_date->format('l, M d, Y \a\t g:i A'),
            'duration_minutes' => $artClass->duration_minutes,
            'location' => $artClass->location,
            'price_cents' => $artClass->price_cents,
            'image_path' => $artClass->image_path,
        ];

        Session::put(self::CART_SESSION_KEY, $cart);

        return $cart[$artClassId];
    }

    /**
     * Remove class from cart
     */
    public function remove($artClassId)
    {
        $cart = $this->get();
        unset($cart[$artClassId]);
        Session::put(self::CART_SESSION_KEY, $cart);

        return true;
    }

    /**
     * Get cart contents
     */
    public function get()
    {
        return Session::get(self::CART_SESSION_KEY, []);
    }

    /**
     * Get cart with fresh class data
     */
    public function getWithClasses()
    {
        $cart = $this->get();
        $classIds = array_keys($cart);

        if (empty($classIds)) {
            return [];
        }

        $classes = ArtClass::whereIn('id', $classIds)->get()->keyBy('id');

        foreach ($cart as $classId => &$item) {
            if (isset($classes[$classId])) {
                $artClass = $classes[$classId];
                $item['art_class'] = $artClass;

                // Update price if it changed
                $item['price_cents'] = $artClass->price_cents;

                // Update date info
                $item['class_date'] = $artClass->class_date->toIso8601String();
                $item['class_date_formatted'] = $artClass->class_date->format('l, M d, Y \a\t g:i A');

                // Check availability
                $item['is_available'] = $artClass->status === 'published'
                    && !$artClass->is_full
                    && !$artClass->is_past;

                $item['spots_available'] = $artClass->spots_available;
            } else {
                $item['is_available'] = false;
            }
        }

        Session::put(self::CART_SESSION_KEY, $cart);

        return $cart;
    }

    /**
     * Get total item count (number of classes)
     */
    public function count()
    {
        return count($this->get());
    }

    /**
     * Get subtotal in cents
     */
    public function subtotal()
    {
        $cart = $this->get();
        return array_sum(array_column($cart, 'price_cents'));
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        Session::forget(self::CART_SESSION_KEY);
    }

    /**
     * Validate cart availability and remove invalid items
     */
    public function validate()
    {
        $cart = $this->getWithClasses();
        $errors = [];

        foreach ($cart as $classId => $item) {
            if (!$item['is_available']) {
                $errors[] = "{$item['title']} is no longer available";
                $this->remove($classId);
                continue;
            }

            // Check if user already booked (in case they booked elsewhere)
            if (Auth::check()) {
                $existingBooking = Booking::where('user_id', Auth::id())
                    ->where('art_class_id', $classId)
                    ->whereIn('payment_status', ['pending', 'completed'])
                    ->whereIn('attendance_status', ['booked', 'attended'])
                    ->exists();

                if ($existingBooking) {
                    $errors[] = "You already have a booking for {$item['title']}";
                    $this->remove($classId);
                }
            }
        }

        return $errors;
    }

    /**
     * Get formatted subtotal
     */
    public function formattedSubtotal()
    {
        return '$' . number_format($this->subtotal() / 100, 2);
    }
}
