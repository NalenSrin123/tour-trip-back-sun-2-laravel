<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Idempotency extends Model
{
    protected $fillable=[
        "payment_id",
        "idempotency_key",
        "request_path",
        "request_body_hash",
        "status",
        "response_body",
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
