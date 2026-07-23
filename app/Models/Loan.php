<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasUuids;

    protected $fillable = ['copy_id', 'user_id', 'status', 'due_date', 'checked_in_at'];

    protected $casts = [
        'due_date' => 'datetime',
        'checked_out_at' => 'datetime',
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

    public function isOverdue(): bool
    {
        return $this->status === 'active' && $this->due_date->isPast();
    }
}
