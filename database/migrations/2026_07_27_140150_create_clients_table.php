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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->date('birthday')->nullable();
            $table->string('instagram')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('total_visits')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->timestamp('last_visit_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['business_id', 'phone']);
            $table->index(['business_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
