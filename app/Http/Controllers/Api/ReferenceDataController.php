<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Enums\TourStatus;
use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Enums\ParticipantSex;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\UserStatus;
use Illuminate\Http\Request;

class ReferenceDataController extends Controller
{
    public function index(Request $request)
{
    // 1. Define all available reference data
    $allData = [
        'tour_statuses'    => TourStatus::getDropdownOptions(),
        'booking_statuses' => BookingStatus::getDropdownOptions(),
        'booking_types'    => BookingType::getDropdownOptions(),
        'participant_sex'  => ParticipantSex::getDropdownOptions(),
        'payment_methods'  => PaymentMethod::getDropdownOptions(),
        'payment_statuses' => PaymentStatus::getDropdownOptions(),
        'user_statuses'    => UserStatus::getDropdownOptions(),
    ];

    // 2. If the frontend requested specific keys, only return those
    if ($request->has('keys')) {
        $requestedKeys = explode(',', $request->query('keys'));
        // array_intersect_key filters the $allData array to only include the requested keys
        return response()->json(array_intersect_key($allData, array_flip($requestedKeys)));
    }

    // 3. Otherwise, return everything
    return response()->json($allData);
}
}