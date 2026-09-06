<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\UpdateTourRequest;
use App\Http\Resources\TourResource;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Return a list of all tours
        $tours = Tour::with([
            'tourImages',
            // 'destination',
            // 'category',
            // 'includeExclude',
            // 'tourItinerary',
            // 'tourSchedule',
        ])->where('status', 'published')->paginate(10); // Paginate the results, 10 per page

        // Return the collection directly and append your custom message
        return TourResource::collection($tours)->additional([
            'message' => 'Tours retrieved successfully'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTourRequest $storeTourRequest)
    {

        $tour = Tour::create($storeTourRequest->all());

        return [
            'message' => 'Tour created successfully',
            'data' => new TourResource($tour)
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tour = Tour::with([
            'tourImages',
            'destination',
            'category',
            'includeExclude',
            'tourItinerary',
            'tourSchedule',
        ])->findOrFail($id);

        return [
            'message' => 'Tour retrieved successfully',
            'data' => new TourResource($tour)
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTourRequest $updateTourRequest, string $id)
    {
        // 2. Manually fetch the real database record
        $tour = Tour::findOrFail($id);

        // 3. Update it using the strictly validated data
        $tour->update($updateTourRequest->validated());

        return [
            'message' => 'Tour updated successfully',
            'data' => new TourResource($tour) // Pass $tour here, not $id
        ];
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tour = Tour::findOrFail($id);
        $tour->delete(); // Soft delete the tour

        return [
            'message' => 'Tour deleted successfully'
        ];
    }

    public function restore(string $id)
    {
        $tour = Tour::withTrashed()->findOrFail($id);
        $tour->restore(); // Restore the soft-deleted tour

        return [
            'message' => 'Tour restored successfully',
            'data' => new TourResource($tour)
        ];
    }

}
