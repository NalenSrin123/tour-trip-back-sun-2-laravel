<?php

namespace App\Traits;

trait ProvidesDropdownOptions
{


    public static function getDropdownOptions(): array
    {
        return array_map(function ($case) {
            return [
                'value' => $case->value,
                // Converts "bank_transfer" to "Bank transfer" for the UI label
                'label' => ucfirst(str_replace('_', ' ', strtolower($case->value))) 
            ];
        }, self::cases());
    }
}
