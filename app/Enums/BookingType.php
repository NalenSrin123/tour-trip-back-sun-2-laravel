<?php
namespace App\Enums;
use App\Traits\ProvidesDropdownOptions;

enum BookingType: string
{
    use ProvidesDropdownOptions;

    case Individual = 'Individual';
    case Family = 'Family';
    case Team = 'Team';
}