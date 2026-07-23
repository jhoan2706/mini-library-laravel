<?php

namespace App\Listeners;

use App\Events\BookCheckedOut;

class LogLoanActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookCheckedOut $event): void
    {
        //
    }
}
