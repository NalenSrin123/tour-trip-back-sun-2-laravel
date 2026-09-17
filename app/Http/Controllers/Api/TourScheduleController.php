<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TourSchedule;
use Illuminate\Http\Request;

class TourScheduleController extends Controller
{
    // Display listing of tour schedules
    public function index(Request $request)
    {
        $query = TourSchedule::with('tour');

        if ($request->has('tour_id')) {
            $query->where('tour_id', $request->tour_id);
        }

        if ($request->has('status')) {
            $query->where('status', strtolower($request->status));
        }

        if ($request->has('start_date')) {
            $query->whereDate('start_datetime', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('end_datetime', '<=', $request->end_date);
        }

        if ($request->has('search')) {
            $query->where('notes', 'like', '%' . $request->search . '%');
        }

        $schedules = $query->latest('start_datetime')->get();

        return response()->json([
            'success' => true,
            'message' => 'Tour schedules retrieved successfully.',
            'data'    => $schedules
        ], 200);
    }

    // Store a newly created schedule in storage
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tour_id'                 => 'required|exists:tours,id',
            'start_datetime'          => 'required|date',
            'end_datetime'            => 'required|date|after_or_equal:start_datetime',
            'booking_cutoff_datetime' => 'required|date|before_or_equal:start_datetime',
            'min_capacity'            => 'required|integer|min:1',
            'max_capacity'            => 'required|integer|gte:min_capacity',
            'price_override'          => 'nullable|numeric|min:0',
            'status'                  => 'nullable|in:published,confirmed,canceled,completed',
            'notes'                   => 'nullable|string',
        ]);

        if (!isset($validated['status'])) {
            $validated['status'] = 'published';
        }

        $schedule = TourSchedule::create($validated);
        $schedule->load('tour');

        return response()->json([
            'success' => true,
            'message' => 'Tour schedule created successfully.',
            'data'    => $schedule
        ], 201);
    }

    // Display tour schedule
    public function show(string $id)
    {
        $schedule = TourSchedule::with(['tour', 'guideAssignments'])->find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Tour schedule not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tour schedule retrieved successfully.',
            'data'    => $schedule
        ], 200);
    }

    //Update tour schedule in storage
    public function update(Request $request, string $id)
    {
        $schedule = TourSchedule::find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Tour schedule not found.'
            ], 404);
        }

        $validated = $request->validate([
            'tour_id'                 => 'sometimes|required|exists:tours,id',
            'start_datetime'          => 'sometimes|required|date',
            'end_datetime'            => 'sometimes|required|date|after_or_equal:start_datetime',
            'booking_cutoff_datetime' => 'sometimes|required|date|before_or_equal:start_datetime',
            'min_capacity'            => 'sometimes|required|integer|min:1',
            'max_capacity'            => 'sometimes|required|integer|gte:min_capacity',
            'price_override'          => 'nullable|numeric|min:0',
            'status'                  => 'nullable|in:published,confirmed,canceled,completed',
            'notes'                   => 'nullable|string',
        ]);

        $schedule->update($validated);
        $schedule->load('tour');

        return response()->json([
            'success' => true,
            'message' => 'Tour schedule updated successfully.',
            'data'    => $schedule
        ], 200);
    }

    // Delele the specified tour schedule by id(soft-delete)
    public function destroy(string $id)
    {
        $schedule = TourSchedule::find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Tour schedule not found.'
            ], 404);
        }

        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tour schedule deleted successfully.'
        ], 200);
    }
}
