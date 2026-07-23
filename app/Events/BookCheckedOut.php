<?php

namespace App\Events;

use App\Models\Loan;
use Illuminate\Foundation\Events\Dispatchable;

class BookCheckedOut
{
    use Dispatchable;

    public function __construct(public Loan $loan) {}
}
