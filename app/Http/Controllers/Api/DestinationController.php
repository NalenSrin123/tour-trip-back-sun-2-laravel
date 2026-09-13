<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index()
    {
        $destinations = Destination::all();

        return response()->json([
            'success' => true,
            'message' => 'Destinations retrieved successfully',
            'data' => $destinations
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'status' => 'nullable|in:ACTIVE,INACTIVE',
        ]);

        $destination = Destination::create([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'image' => $request->image,
            'status' => $request->status ?? 'ACTIVE',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Destination created successfully',
            'data' => $destination
        ], 201);
    }

    public function show(string $id)
    {
        $destination = Destination::find($id);

        if (!$destination) {
            return response()->json([
                'success' => false,
                'message' => 'Destination not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Destination retrieved successfully',
            'data' => $destination
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $destination = Destination::find($id);

        if (!$destination) {
            return response()->json([
                'success' => false,
                'message' => 'Destination not found'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|string',
            'status' => 'nullable|in:ACTIVE,INACTIVE',
        ]);

        $destination->update($request->only([
            'name',
            'description',
            'location',
            'image',
            'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Destination updated successfully',
            'data' => $destination
        ], 200);
    }

    public function destroy(string $id)
    {
        $destination = Destination::find($id);

        if (!$destination) {
            return response()->json([
                'success' => false,
                'message' => 'Destination not found'
            ], 404);
        }

        $destination->delete();

        return response()->json([
            'success' => true,
            'message' => 'Destination deleted successfully'
        ], 200);
    }
}