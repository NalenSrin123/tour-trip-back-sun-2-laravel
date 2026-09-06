<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Tour_images;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TourImageController extends Controller
{
    /**
     * Display a listing of the tour images.
     * GET /api/tour-images
     */
    public function index(Request $request)
    {
        $query = Tour_images::with('tour');

        // Filter by tour_id if provided
        if ($request->filled('tour_id')) {
            $query->where('tour_id', $request->tour_id);
        }

        // Filter by status if provided (ACTIVE / INACTIVE)
        if ($request->filled('status')) {
            $query->where('status', strtoupper($request->status));
        }

        // Filter by is_primary if provided
        if ($request->has('is_primary')) {
            $isPrimary = filter_var($request->is_primary, FILTER_VALIDATE_BOOLEAN);
            $query->where('is_primary', $isPrimary);
        }

        // Sort primary first, then newest
        $images = $query->orderBy('is_primary', 'desc')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Tour images retrieved successfully.',
            'data'    => $images,
        ], 200);
    }

    /**
     * Store newly created tour image(s) in storage.
     * POST /api/tour-images
     */
    public function store(Request $request)
    {
        $request->validate([
            'tour_id'    => 'required|exists:tours,id',
            'image'      => 'required_without:images|image|mimes:jpeg,png,jpg,webp|max:5120',
            'images'     => 'required_without:image|array',
            'images.*'   => 'image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_primary' => 'nullable|boolean',
            'status'     => 'nullable|in:ACTIVE,INACTIVE',
        ]);

        $tourId = $request->tour_id;
        $isPrimary = $request->boolean('is_primary', false);
        $status = $request->input('status', 'ACTIVE');

        return DB::transaction(function () use ($request, $tourId, $isPrimary, $status) {
            // If marked as primary, reset previous primary images for this tour
            if ($isPrimary) {
                Tour_images::where('tour_id', $tourId)->update(['is_primary' => false]);
            }

            // Case 1: Multiple images upload
            if ($request->hasFile('images')) {
                $createdImages = [];
                $files = $request->file('images');

                foreach ($files as $index => $file) {
                    $path = $file->store('tour_images', 'public');

                    // Set primary only for the first uploaded image if requested
                    $primaryFlag = ($index === 0 && $isPrimary);

                    $createdImages[] = Tour_images::create([
                        'tour_id'    => $tourId,
                        'image_url'  => $path,
                        'is_primary' => $primaryFlag,
                        'status'     => $status,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => count($createdImages) . ' tour images uploaded successfully.',
                    'data'    => $createdImages,
                ], 201);
            }

            // Case 2: Single image upload
            $file = $request->file('image');
            $path = $file->store('tour_images', 'public');

            // If this is the tour's first image, make it primary automatically unless specified
            $hasExistingImages = Tour_images::where('tour_id', $tourId)->exists();
            if (!$hasExistingImages && !$request->has('is_primary')) {
                $isPrimary = true;
            }

            $tourImage = Tour_images::create([
                'tour_id'    => $tourId,
                'image_url'  => $path,
                'is_primary' => $isPrimary,
                'status'     => $status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tour image uploaded successfully.',
                'data'    => $tourImage->load('tour'),
            ], 201);
        });
    }

    /**
     * Display the specified tour image.
     * GET /api/tour-images/{id}
     */
    public function show(string $id)
    {
        $tourImage = Tour_images::with('tour')->find($id);

        if (!$tourImage) {
            return response()->json([
                'success' => false,
                'message' => 'Tour image not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tour image retrieved successfully.',
            'data'    => $tourImage,
        ], 200);
    }

    /**
     * Update the specified tour image.
     * PUT/PATCH /api/tour-images/{id} or POST with _method=PUT for multipart
     */
    public function update(Request $request, string $id)
    {
        $tourImage = Tour_images::find($id);

        if (!$tourImage) {
            return response()->json([
                'success' => false,
                'message' => 'Tour image not found.',
            ], 404);
        }

        $validated = $request->validate([
            'is_primary' => 'nullable|boolean',
            'status'     => 'nullable|in:ACTIVE,INACTIVE',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        return DB::transaction(function () use ($request, $tourImage, $validated) {
            // If new file provided, store new and delete old
            if ($request->hasFile('image')) {
                // Delete old file if stored locally
                if ($tourImage->image_url && Storage::disk('public')->exists($tourImage->image_url)) {
                    Storage::disk('public')->delete($tourImage->image_url);
                }

                $validated['image_url'] = $request->file('image')->store('tour_images', 'public');
            }

            // If updating to primary, unset others in the same tour
            if (isset($validated['is_primary']) && $validated['is_primary']) {
                Tour_images::where('tour_id', $tourImage->tour_id)
                    ->where('id', '!=', $tourImage->id)
                    ->update(['is_primary' => false]);
            }

            $tourImage->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tour image updated successfully.',
                'data'    => $tourImage->fresh('tour'),
            ], 200);
        });
    }

    /**
     * Remove the specified tour image from storage and database.
     * DELETE /api/tour-images/{id}
     */
    public function destroy(string $id)
    {
        $tourImage = Tour_images::find($id);

        if (!$tourImage) {
            return response()->json([
                'success' => false,
                'message' => 'Tour image not found.',
            ], 404);
        }

        // Delete physical file from public disk
        if ($tourImage->image_url && Storage::disk('public')->exists($tourImage->image_url)) {
            Storage::disk('public')->delete($tourImage->image_url);
        }

        $tourId = $tourImage->tour_id;
        $wasPrimary = $tourImage->is_primary;

        $tourImage->delete();

        // If primary image was deleted, promote another image if available
        if ($wasPrimary) {
            $nextImage = Tour_images::where('tour_id', $tourId)->latest()->first();
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tour image deleted successfully.',
        ], 200);
    }

    /**
     * Set an image as primary cover for its tour.
     * PATCH /api/tour-images/{id}/primary
     */
    public function setPrimary(string $id)
    {
        $tourImage = Tour_images::find($id);

        if (!$tourImage) {
            return response()->json([
                'success' => false,
                'message' => 'Tour image not found.',
            ], 404);
        }

        DB::transaction(function () use ($tourImage) {
            // Reset all sibling images for this tour
            Tour_images::where('tour_id', $tourImage->tour_id)
                ->where('id', '!=', $tourImage->id)
                ->update(['is_primary' => false]);

            $tourImage->update(['is_primary' => true]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Tour image set as primary successfully.',
            'data'    => $tourImage->fresh('tour'),
        ], 200);
    }
}
