<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_id',
        'receipt_no',
        'issued_at',
        'tour_title',
        'tour_date',
        'num_travelers',
        'sub_total',
        'tax_amount',
        'total_paid',
        'payment_method',
        'pdf_url',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'tour_date' => 'date',
        'sub_total' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_paid' => 'decimal:2',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
