<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('copy_id')->constrained();
            $table->foreignUuid('user_id')->constrained();
            $table->enum('status', ['active', 'returned', 'overdue'])->default('active');
            $table->timestamp('checked_out_at')->useCurrent();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();

            $table->index(['copy_id', 'status']); // la query más frecuente del sistema
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
