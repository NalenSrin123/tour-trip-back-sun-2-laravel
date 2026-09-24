<?php
namespace App\Enums;

use App\Traits\ProvidesDropdownOptions;

enum BookingStatus: string
{
    use ProvidesDropdownOptions;
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
}