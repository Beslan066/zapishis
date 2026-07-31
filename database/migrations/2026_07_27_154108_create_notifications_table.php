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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // Связи
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Кому отправлено
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('cascade'); // Связанная запись
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('cascade'); // Связанный клиент

            // Основная информация
            $table->string('type'); // appointment_confirmation, appointment_reminder, appointment_cancellation, birthday_greeting, promotion, system, etc.
            $table->string('channel')->default('system'); // system, sms, email, telegram, push

            // Содержимое
            $table->string('title')->nullable();
            $table->text('message');
            $table->json('data')->nullable(); // Дополнительные данные (ссылки, кнопки, и т.д.)

            // Статусы
            $table->string('status')->default('pending'); // pending, sent, delivered, read, failed, cancelled
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('failed_at')->nullable();

            // Для SMS/Email
            $table->string('recipient')->nullable(); // Телефон или email получателя
            $table->string('provider')->nullable(); // smsc, nexmo, infobip, sendgrid, etc.
            $table->string('provider_message_id')->nullable(); // ID сообщения у провайдера
            $table->text('provider_response')->nullable(); // Ответ от провайдера

            // Ошибки
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('next_retry_at')->nullable();

            // Приоритет и настройки
            $table->integer('priority')->default(0); // 0 - низкий, 1 - средний, 2 - высокий
            $table->boolean('is_urgent')->default(false);
            $table->boolean('requires_action')->default(false);

            // Метаданные
            $table->json('metadata')->nullable();

            // Временные метки
            $table->timestamps();
            $table->softDeletes();

            // Индексы для оптимизации
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'type']);
            $table->index(['business_id', 'created_at']);
            $table->index(['user_id', 'read_at']);
            $table->index(['client_id', 'read_at']);
            $table->index(['status', 'sent_at']);
            $table->index(['status', 'next_retry_at']);
            $table->index(['provider_message_id']);
            $table->index(['type', 'channel']);
            $table->index(['appointment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
