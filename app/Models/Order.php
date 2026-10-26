<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Tentukan kolom mana saja yang boleh diisi data
    protected $fillable = [
        'user_id',
        'tracking_number',
        'recipient_name',
        'destination',
        'status',
        'ai_risk_prediction'
    ];

    // Relasi: Order ini milik siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}