<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    // GET /api/guides
    public function index()
    {
        $guides = Guide::with('user')->get();

        return response()->json([
            'success' => true,
            'message' => 'Guides retrieved successfully',
            'data' => $guides
        ], 200);
    }

    // POST /api/guides
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'full_name' => 'required|string|max:255',
            'license_number' => 'required|string|max:255|unique:guides,license_number',
            'email' => 'required|email|max:255|unique:guides,email',
            'phone_number' => 'nullable|string|max:20',
            'languages' => 'nullable|string',
            'specialties' => 'nullable|string',
            'bio' => 'nullable|string',
            'profile_image_url' => 'nullable|string|max:255',
            'status' => 'required|in:ACTIVE,INACTIVE',
        ]);

        $guide = Guide::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Guide created successfully',
            'data' => $guide
        ], 201);
    }

    // GET /api/guides/{id}
    public function show(string $id)
    {
        $guide = Guide::with('user')->find($id);

        if (!$guide) {
            return response()->json([
                'success' => false,
                'message' => 'Guide not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Guide retrieved successfully',
            'data' => $guide
        ], 200);
    }

    // PUT/PATCH /api/guides/{id}
    public function update(Request $request, string $id)
    {
        $guide = Guide::find($id);

        if (!$guide) {
            return response()->json([
                'success' => false,
                'message' => 'Guide not found'
            ], 404);
        }

        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'full_name' => 'sometimes|required|string|max:255',
            'license_number' => 'sometimes|required|string|max:255|unique:guides,license_number,' . $id,
            'email' => 'sometimes|required|email|max:255|unique:guides,email,' . $id,
            'phone_number' => 'nullable|string|max:20',
            'languages' => 'nullable|string',
            'specialties' => 'nullable|string',
            'bio' => 'nullable|string',
            'profile_image_url' => 'nullable|string|max:255',
            'status' => 'sometimes|required|in:ACTIVE,INACTIVE',
        ]);

        $guide->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Guide updated successfully',
            'data' => $guide
        ], 200);
    }

    // DELETE /api/guides/{id}
    public function destroy(string $id)
    {
        $guide = Guide::find($id);

        if (!$guide) {
            return response()->json([
                'success' => false,
                'message' => 'Guide not found'
            ], 404);
        }

        $guide->delete();

        return response()->json([
            'success' => true,
            'message' => 'Guide deleted successfully'
        ], 200);
    }
}