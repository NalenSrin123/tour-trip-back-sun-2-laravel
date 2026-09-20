<?php
namespace App\Enums;
use App\Traits\ProvidesDropdownOptions;

enum UserStatus: string
{
    use ProvidesDropdownOptions;

    case Active = 'active';
    case Inactive = 'inactive';
}