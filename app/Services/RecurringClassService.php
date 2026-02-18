<?php

namespace App\Services;

use App\Models\ArtClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecurringClassService
{
    /**
     * Generate recurring classes from a template
     *
     * @param ArtClass $template The template class to copy
     * @param array $options Configuration options
     * @return array Array of created classes
     */
    public function generate(ArtClass $template, array $options): array
    {
        $recurrenceType = $options['recurrence_type']; // weekly, biweekly, custom
        $startDate = Carbon::parse($options['start_date']);
        $endDate = Carbon::parse($options['end_date']);
        $time = $options['time'] ?? $template->class_date->format('H:i');
        $daysOfWeek = $options['days_of_week'] ?? []; // For custom: [1, 3, 5] for Mon, Wed, Fri
        $seriesName = $options['series_name'] ?? null;

        $dates = $this->calculateDates($recurrenceType, $startDate, $endDate, $daysOfWeek);
        $createdClasses = [];

        foreach ($dates as $date) {
            // Combine date with time
            $classDateTime = Carbon::parse($date->format('Y-m-d') . ' ' . $time);

            // Create the new class
            $newClass = $this->createClassFromTemplate($template, $classDateTime, $seriesName);
            $createdClasses[] = $newClass;
        }

        return $createdClasses;
    }

    /**
     * Calculate dates based on recurrence type
     */
    protected function calculateDates(string $recurrenceType, Carbon $startDate, Carbon $endDate, array $daysOfWeek = []): array
    {
        $dates = [];
        $current = $startDate->copy();

        switch ($recurrenceType) {
            case 'weekly':
                // Every week on the same day as start date
                while ($current->lte($endDate)) {
                    $dates[] = $current->copy();
                    $current->addWeek();
                }
                break;

            case 'biweekly':
                // Every two weeks on the same day as start date
                while ($current->lte($endDate)) {
                    $dates[] = $current->copy();
                    $current->addWeeks(2);
                }
                break;

            case 'custom':
                // Specific days of the week (1 = Monday, 7 = Sunday)
                while ($current->lte($endDate)) {
                    if (in_array($current->dayOfWeekIso, $daysOfWeek)) {
                        $dates[] = $current->copy();
                    }
                    $current->addDay();
                }
                break;

            default:
                throw new \InvalidArgumentException("Invalid recurrence type: {$recurrenceType}");
        }

        return $dates;
    }

    /**
     * Create a new class from a template
     */
    protected function createClassFromTemplate(ArtClass $template, Carbon $classDateTime, ?string $seriesName = null): ArtClass
    {
        // Generate unique slug
        $baseSlug = $template->slug;
        $dateSlug = $classDateTime->format('Y-m-d');
        $slug = $this->generateUniqueSlug("{$baseSlug}-{$dateSlug}");

        // Copy the image file so each class owns its own copy.
        // This prevents edits/deletes on one class from breaking another's image.
        $newImagePath = $this->copyFile($template->image_path);
        $newGalleryImages = $this->copyGalleryImages($template->gallery_images);

        return ArtClass::create([
            'title' => $template->title,
            'slug' => $slug,
            'short_description' => $template->short_description,
            'description' => $template->description,
            'class_date' => $classDateTime,
            'duration_minutes' => $template->duration_minutes,
            'location' => $template->location,
            'capacity' => $template->capacity,
            'price_cents' => $template->price_cents,
            'image_path' => $newImagePath,
            'gallery_images' => $newGalleryImages,
            'materials_included' => $template->materials_included,
            'status' => 'draft', // Start as draft so admin can review
            'created_by' => $template->created_by,
        ]);
    }

    /**
     * Copy a file on the public disk, returning the new path (or null).
     */
    protected function copyFile(?string $path): ?string
    {
        if (!$path || !Storage::disk('public')->exists($path)) {
            return $path; // Keep the original path even if file is missing — better than null
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $directory = pathinfo($path, PATHINFO_DIRNAME);
        $newPath = $directory . '/' . Str::random(40) . '.' . $extension;

        Storage::disk('public')->copy($path, $newPath);

        return $newPath;
    }

    /**
     * Copy all gallery images, returning the new JSON string (or null).
     */
    protected function copyGalleryImages(?string $galleryJson): ?string
    {
        if (!$galleryJson) {
            return null;
        }

        $paths = json_decode($galleryJson, true);
        if (!is_array($paths) || empty($paths)) {
            return null;
        }

        $newPaths = [];
        foreach ($paths as $path) {
            $newPaths[] = $this->copyFile($path) ?? $path;
        }

        return json_encode($newPaths);
    }

    /**
     * Generate a unique slug
     */
    protected function generateUniqueSlug(string $baseSlug): string
    {
        $slug = Str::slug($baseSlug);
        $originalSlug = $slug;
        $counter = 1;

        while (ArtClass::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Preview dates that would be generated (without creating classes)
     */
    public function previewDates(array $options): array
    {
        $recurrenceType = $options['recurrence_type'];
        $startDate = Carbon::parse($options['start_date']);
        $endDate = Carbon::parse($options['end_date']);
        $daysOfWeek = $options['days_of_week'] ?? [];
        $time = $options['time'] ?? '18:00';

        $dates = $this->calculateDates($recurrenceType, $startDate, $endDate, $daysOfWeek);

        return array_map(function ($date) use ($time) {
            $dateTime = Carbon::parse($date->format('Y-m-d') . ' ' . $time);
            return [
                'date' => $dateTime->format('Y-m-d'),
                'time' => $dateTime->format('H:i'),
                'formatted' => $dateTime->format('l, F j, Y \a\t g:i A'),
                'day_of_week' => $dateTime->format('l'),
            ];
        }, $dates);
    }
}
