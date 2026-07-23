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
        Schema::table('loans', function (Blueprint $table) {
            $table->text('review')->nullable()->after('checked_in_at');
            $table->text('observation')->nullable()->after('review');
            $table->integer('rating')->nullable()->after('observation');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['review', 'observation', 'rating']);
        });
    }
};
