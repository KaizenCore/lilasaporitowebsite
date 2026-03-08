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
     * Add class to cart with quantity support
     */
    public function add($artClassId, $quantity = 1, $ticketTypeIndex = null)
    {
        $quantity = max(1, (int) $quantity);

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

        // Determine pricing based on ticket type
        $priceCents = $artClass->price_cents;
        $spotsPerTicket = 1;
        $ticketTypeName = null;
        $ticketTypes = $artClass->ticket_types ?? [];

        if ($ticketTypeIndex !== null && isset($ticketTypes[$ticketTypeIndex])) {
            $ticketType = $ticketTypes[$ticketTypeIndex];
            $priceCents = $ticketType['price_cents'];
            $spotsPerTicket = max(1, (int) ($ticketType['spots'] ?? 1));
            $ticketTypeName = $ticketType['name'];
        }

        // Check spots available (accounting for spots per ticket)
        $totalSpotsNeeded = $quantity * $spotsPerTicket;
        if ($artClass->spots_available < $totalSpotsNeeded) {
            throw new \Exception("Only {$artClass->spots_available} spots available.");
        }

        $cart = $this->get();

        // If already in cart, update quantity instead of rejecting
        if (isset($cart[$artClassId])) {
            $existingSpotsPerTicket = $cart[$artClassId]['spots_per_ticket'] ?? 1;
            $newQty = ($cart[$artClassId]['quantity'] ?? 1) + $quantity;
            $newSpotsNeeded = $newQty * $existingSpotsPerTicket;
            if ($artClass->spots_available < $newSpotsNeeded) {
                throw new \Exception("Only {$artClass->spots_available} spots available (you already have " . ($cart[$artClassId]['quantity'] ?? 1) . " in cart).");
            }
            $cart[$artClassId]['quantity'] = $newQty;
            Session::put(self::CART_SESSION_KEY, $cart);
            return $cart[$artClassId];
        }

        $cart[$artClassId] = [
            'art_class_id' => $artClass->id,
            'title' => $artClass->title,
            'slug' => $artClass->slug,
            'class_date' => $artClass->class_date->toIso8601String(),
            'class_date_formatted' => $artClass->class_date->format('l, M d, Y \a\t g:i A'),
            'duration_minutes' => $artClass->duration_minutes,
            'location' => $artClass->location,
            'price_cents' => $priceCents,
            'image_path' => $artClass->image_path,
            'quantity' => $quantity,
            'ticket_type_index' => $ticketTypeIndex,
            'ticket_type_name' => $ticketTypeName,
            'spots_per_ticket' => $spotsPerTicket,
        ];

        Session::put(self::CART_SESSION_KEY, $cart);

        return $cart[$artClassId];
    }

    /**
     * Update quantity for a class in cart
     */
    public function updateQuantity($artClassId, $quantity)
    {
        $quantity = max(1, (int) $quantity);
        $cart = $this->get();

        if (!isset($cart[$artClassId])) {
            throw new \Exception('Class not found in cart.');
        }

        $artClass = ArtClass::findOrFail($artClassId);
        if ($artClass->spots_available < $quantity) {
            throw new \Exception("Only {$artClass->spots_available} spots available.");
        }

        $cart[$artClassId]['quantity'] = $quantity;
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
            // Ensure quantity exists (backward compat with old cart entries)
            if (!isset($item['quantity'])) {
                $item['quantity'] = 1;
            }

            if (isset($classes[$classId])) {
                $artClass = $classes[$classId];
                $item['art_class'] = $artClass;

                // Update price - use ticket type price if set, else standard price
                $ticketTypeIndex = $item['ticket_type_index'] ?? null;
                $ticketTypes = $artClass->ticket_types ?? [];
                if ($ticketTypeIndex !== null && isset($ticketTypes[$ticketTypeIndex])) {
                    $item['price_cents'] = $ticketTypes[$ticketTypeIndex]['price_cents'];
                    $item['spots_per_ticket'] = max(1, (int) ($ticketTypes[$ticketTypeIndex]['spots'] ?? 1));
                    $item['ticket_type_name'] = $ticketTypes[$ticketTypeIndex]['name'];
                } else {
                    $item['price_cents'] = $artClass->price_cents;
                }

                // Update date info
                $item['class_date'] = $artClass->class_date->toIso8601String();
                $item['class_date_formatted'] = $artClass->class_date->format('l, M d, Y \a\t g:i A');

                // Check availability
                $item['is_available'] = $artClass->status === 'published'
                    && !$artClass->is_full
                    && !$artClass->is_past;

                $item['spots_available'] = $artClass->spots_available;

                // Cap quantity to available spots
                if ($item['quantity'] > $artClass->spots_available) {
                    $item['quantity'] = max(1, $artClass->spots_available);
                }
            } else {
                $item['is_available'] = false;
            }
        }

        Session::put(self::CART_SESSION_KEY, $cart);

        return $cart;
    }

    /**
     * Get total ticket count (sum of all quantities)
     */
    public function count()
    {
        $cart = $this->get();
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['quantity'] ?? 1;
        }
        return $total;
    }

    /**
     * Get subtotal in cents
     */
    public function subtotal()
    {
        $cart = $this->get();
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price_cents'] * ($item['quantity'] ?? 1);
        }
        return $total;
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
