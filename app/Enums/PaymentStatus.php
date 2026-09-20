<?php
namespace App\Enums;
use App\Traits\ProvidesDropdownOptions;

enum PaymentStatus: string
{
    use ProvidesDropdownOptions;

    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';
}