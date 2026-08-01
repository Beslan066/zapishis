<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            // Делаем client_id nullable (для незарегистрированных клиентов)
            $table->foreignId('client_id')->nullable()->change();

            // Сохраняем телефон клиента для привязки позже
            $table->string('client_phone')->nullable()->after('client_id');
            $table->string('client_name')->nullable()->after('client_phone');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable(false)->change();
            $table->dropColumn(['client_phone', 'client_name']);
        });
    }
};
