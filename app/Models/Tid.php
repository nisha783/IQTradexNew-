<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tid extends Model
{
    //
    protected $fillable = [
        'user_id',
        'amount',
        'transaction_number',
        'status',
        'screenshot',
    ];

    // Relationship: A transaction belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
