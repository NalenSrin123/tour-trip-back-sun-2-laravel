<?php

namespace App\Enums;

use App\Traits\ProvidesDropdownOptions;

enum TourStatus: string
{
    use ProvidesDropdownOptions;

    case Draft = 'DRAFT';
    case Published = 'PUBLISHED';
    case Archived = 'ARCHIVED';
}
