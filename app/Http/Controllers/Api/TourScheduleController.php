<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourScheduleRequest;
use App\Models\TourSchedule;
use Illuminate\Http\Request;

class TourScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TourSchedule::paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTourScheduleRequest $request)
    {
        $validated = $request->validated();

        return TourSchedule::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tourSchedule = TourSchedule::find($id);

        if (!$tourSchedule) {
            return response()->json([
                'success' => false,
                'message' => 'Tour Schedule not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tourSchedule,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tourSchedule = TourSchedule::find($id);

        if (!$tourSchedule) {
            return response()->json([
                'success' => false,
                'message' => 'Tour Schedule not found.',
            ], 404);
        }

        $validated = $request->validate([
            'tour_id' => ['sometimes', 'exists:tours,id'],
            'start_datetime' => ['sometimes', 'date', 'after:now'],
            'end_datetime' => ['sometimes', 'date', 'after:start_datetime'],
            'booking_cutoff_datetime' => ['sometimes', 'date', 'after:now', 'before:start_datetime'],
            'min_capacity' => ['sometimes', 'integer', 'min:1'],
            'max_capacity' => ['sometimes', 'integer', 'gte:min_capacity'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
            'status' => ['sometimes', 'in:published,confirmed,canceled,completed'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $tourSchedule->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tour Schedule updated successfully.',
            'data' => $tourSchedule,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tourSchedule = TourSchedule::find($id);

        if (!$tourSchedule) {
            return response()->json([
                'success' => false,
                'message' => 'Tour Schedule not found.',
            ], 404);
        }

        $tourSchedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tour Schedule deleted successfully.',
        ], 200);
    }

    public function restore(string $id)
    {
        $tourSchedule = TourSchedule::withTrashed()->find($id);

        if (!$tourSchedule) {
            return response()->json([
                'success' => false,
                'message' => 'Tour Schedule not found.',
            ], 404);
        }

        $tourSchedule->restore();

        return response()->json([
            'success' => true,
            'message' => 'Tour Schedule restored successfully.',
            'data' => $tourSchedule,
        ], 200);
    }
}
