<?php
namespace App\Enums;
use App\Traits\ProvidesDropdownOptions;

enum ParticipantSex: string
{
    use ProvidesDropdownOptions;

    case Male = 'Male';
    case Female = 'Female';
    case Other = 'Other';
}