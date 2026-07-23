<?php

// routes/console.php
use App\Console\Commands\MarkOverdueLoans;
use Illuminate\Support\Facades\Schedule;

Schedule::command(MarkOverdueLoans::class)->hourly();
