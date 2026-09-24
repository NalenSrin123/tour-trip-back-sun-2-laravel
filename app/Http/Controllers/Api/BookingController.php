<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\TourSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings.
     * GET /api/bookings
     */
    public function index(Request $request)
    {
        $query = Booking::with([
            'tourSchedule.tour',
            'user:id,name,email',
            'participants',
            'invoice',
        ]);

        // If authenticated user is customer, show only their bookings
        $user = auth('sanctum')->user();
        if ($user && $user->roles && $user->roles->contains('name', 'customer')) {
            $query->where('user_id', $user->id);
        } elseif ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by tour_schedule_id
        if ($request->filled('tour_schedule_id')) {
            $query->where('tour_schedule_id', $request->tour_schedule_id);
        }

        // Filter by status (pending, confirmed, cancelled, completed)
        if ($request->filled('status')) {
            $query->where('status', strtolower($request->status));
        }

        // Filter by type (Individual, Family, Team)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $perPage = $request->input('per_page', 10);
        $bookings = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Bookings retrieved successfully.',
            'data'    => $bookings,
        ], 200);
    }

    /**
     * Store a newly created booking with price & capacity calculation.
     * POST /api/bookings
     */
    public function store(StoreBookingRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // 1. Fetch TourSchedule with its Tour
            $schedule = TourSchedule::with('tour')->findOrFail($request->tour_schedule_id);

            // 2. Calculate members_count
            $membersCount = ($request->has('participants') && count($request->participants) > 0)
                ? count($request->participants)
                : (int) $request->input('members_count', 1);

            // 3. Seat Capacity Check
            $availableSeats = $schedule->max_capacity - $schedule->current_booked;
            if ($membersCount > $availableSeats) {
                return response()->json([
                    'success' => false,
                    'message' => "Not enough seats available! Only {$availableSeats} seat(s) remaining.",
                ], 422);
            }

            // 4. Booking Cutoff Date Check
            if ($schedule->booking_cutoff_datetime && now()->greaterThan($schedule->booking_cutoff_datetime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking for this tour schedule has already closed.',
                ], 422);
            }

            // 5. Calculate Unit Price & Total Price
            $unitPrice = $schedule->price_override ?? ($schedule->tour ? $schedule->tour->base_price : 0);
            $totalPrice = (float) $unitPrice * $membersCount;

            // 6. Determine user_id (authenticated user or passed in request)
            $userId = auth('sanctum')->id() ?? $request->input('user_id', 1);

            // 7. Create Booking
            $booking = Booking::create([
                'user_id'          => $userId,
                'tour_schedule_id' => $schedule->id,
                'total_price'      => $totalPrice,
                'special_requests' => $request->special_requests,
                'status'           => 'pending',
                'type'             => $request->type,
                'members_count'    => $membersCount,
            ]);

            // 8. Create Participants if provided
            if ($request->has('participants')) {
                foreach ($request->participants as $person) {
                    $booking->participants()->create([
                        'name'      => $person['name'],
                        'sex'       => $person['sex'] ?? null,
                        'age_group' => $person['age_group'] ?? null,
                    ]);
                }
            }

            // 9. Increment current_booked count in tour_schedules
            $schedule->increment('current_booked', $membersCount);

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully.',
                'data'    => $booking->load(['tourSchedule.tour', 'participants', 'user:id,name,email']),
            ], 201);
        });
    }

    /**
     * Display the specified booking.
     * GET /api/bookings/{id}
     */
    public function show(string $id)
    {
        $booking = Booking::with([
            'tourSchedule.tour',
            'user:id,name,email',
            'participants',
            'invoice',
        ])->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking retrieved successfully.',
            'data'    => $booking,
        ], 200);
    }

    /**
     * Cancel a booking and release booked seats.
     * PATCH /api/bookings/{id}/cancel
     */
    public function cancel(string $id)
    {
        return DB::transaction(function () use ($id) {
            $booking = Booking::find($id);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found.',
                ], 404);
            }

            if ($booking->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'This booking is already cancelled.',
                ], 400);
            }

            // Update booking status
            $booking->update(['status' => 'cancelled']);

            // Decrement current_booked count in tour_schedules
            if ($booking->tour_schedule_id) {
                $schedule = TourSchedule::find($booking->tour_schedule_id);
                if ($schedule) {
                    $newBooked = max(0, $schedule->current_booked - $booking->members_count);
                    $schedule->update(['current_booked' => $newBooked]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully. Seats have been released.',
                'data'    => $booking->fresh(['tourSchedule']),
            ], 200);
        });
    }
}
