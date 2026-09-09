<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Included_excluded;
use Illuminate\Http\Request;

class TourInclusionController extends Controller
{
    /**
     * Display a listing of tour inclusions/exclusions.
     * GET /api/tour-inclusions
     */
    public function index(Request $request)
    {
        $query = Included_excluded::with('tour');

        // Filter by tour_id if provided
        if ($request->has('tour_id')) {
            $query->where('tour_id', $request->tour_id);
        }

        // Filter by type ('INCLUDED' or 'EXCLUDED')
        if ($request->has('type')) {
            $query->where('type', strtoupper($request->type));
        }

        // Filter by status ('ACTIVE' or 'INACTIVE')
        if ($request->has('status')) {
            $query->where('status', strtoupper($request->status));
        }

        // Search by description
        if ($request->has('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $inclusions = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Tour inclusions retrieved successfully.',
            'data' => $inclusions
        ], 200);
    }

    /**
     * Store a newly created inclusion/exclusion.
     * POST /api/tour-inclusions
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tour_id'     => 'required|exists:tours,id',
            'type'        => 'required|in:INCLUDED,EXCLUDED',
            'description' => 'required|string',
            'status'      => 'nullable|in:ACTIVE,INACTIVE',
        ]);

        if (!isset($validated['status'])) {
            $validated['status'] = 'ACTIVE';
        }

        $inclusion = Included_excluded::create($validated);
        $inclusion->load('tour');

        return response()->json([
            'success' => true,
            'message' => 'Tour inclusion created successfully.',
            'data' => $inclusion
        ], 201);
    }

    /**
     * Display the specified inclusion/exclusion item.
     * GET /api/tour-inclusions/{id}
     */
    public function show(string $id)
    {
        $inclusion = Included_excluded::with('tour')->find($id);

        if (!$inclusion) {
            return response()->json([
                'success' => false,
                'message' => 'Tour inclusion not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tour inclusion retrieved successfully.',
            'data' => $inclusion
        ], 200);
    }

    /**
     * Update the specified inclusion/exclusion item.
     * PUT/PATCH /api/tour-inclusions/{id}
     */
    public function update(Request $request, string $id)
    {
        $inclusion = Included_excluded::find($id);

        if (!$inclusion) {
            return response()->json([
                'success' => false,
                'message' => 'Tour inclusion not found.'
            ], 404);
        }

        $validated = $request->validate([
            'tour_id'     => 'sometimes|required|exists:tours,id',
            'type'        => 'sometimes|required|in:INCLUDED,EXCLUDED',
            'description' => 'sometimes|required|string',
            'status'      => 'nullable|in:ACTIVE,INACTIVE',
        ]);

        $inclusion->update($validated);
        $inclusion->load('tour');

        return response()->json([
            'success' => true,
            'message' => 'Tour inclusion updated successfully.',
            'data' => $inclusion
        ], 200);
    }

    /**
     * Remove the specified inclusion/exclusion item.
     * DELETE /api/tour-inclusions/{id}
     */
    public function destroy(string $id)
    {
        $inclusion = Included_excluded::find($id);

        if (!$inclusion) {
            return response()->json([
                'success' => false,
                'message' => 'Tour inclusion not found.'
            ], 404);
        }

        $inclusion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tour inclusion deleted successfully.'
        ], 200);
    }
}
