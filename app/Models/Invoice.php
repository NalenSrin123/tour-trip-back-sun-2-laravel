<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_id',
        'invoice_no',
        'sub_total',
        'tax_amount',
        'total_amount',
        'issued_at',
        'pdf_url',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }
}