<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Copy extends Model
{
    use HasUuids;

    protected $fillable = ['book_id', 'barcode', 'condition'];

    protected static function booted()
    {
        static::creating(function (Copy $copy) {
            $copy->barcode ??= strtoupper(str()->random(10));
        });
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function activeLoan()
    {
        return $this->hasOne(Loan::class)->where('status', 'active');
    }
}
