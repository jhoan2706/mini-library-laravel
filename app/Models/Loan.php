<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasUuids, HasFactory;

    protected $fillable = [
        'copy_id',
        'user_id',
        'status',
        'checked_out_at',
        'due_date',
        'checked_in_at',
    ];

    protected $casts = [
        'checked_out_at' => 'datetime',
        'due_date' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    public function copy()
    {
        return $this->belongsTo(Copy::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}