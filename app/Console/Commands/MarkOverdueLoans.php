<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Illuminate\Console\Command;

class MarkOverdueLoans extends Command
{
    protected $signature = 'loans:mark-overdue';
    protected $description = 'Marca como vencidos los préstamos activos cuya fecha límite ya pasó';

    public function handle(): void
    {
        $count = Loan::where('status', 'active')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        $this->info("{$count} préstamo(s) marcado(s) como vencidos.");
    }
}
