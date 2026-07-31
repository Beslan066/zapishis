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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('period'); // daily, weekly, monthly, quarterly, yearly
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('data');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'period']);
            $table->index(['business_id', 'period_start']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
