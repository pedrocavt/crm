<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'scheduled_at', 'notes'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
