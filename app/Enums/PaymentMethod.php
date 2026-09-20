<?php
namespace App\Enums;
use App\Traits\ProvidesDropdownOptions;

enum PaymentMethod: string{
    use ProvidesDropdownOptions;
    case Cash ='cash';
    case BankTransfer='bank_transfer';
    case ABAPay ='aba_pay';

    public function label(): string
    {
        return match ($this) {
            self::Cash         => 'Cash',
            self::BankTransfer => 'Bank Transfer',
            self::ABAPay       => 'ABA Pay',
        };
    }
}