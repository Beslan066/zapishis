@extends('emails.layout')

@section('title', 'Напоминание о записи')

@section('content')
    <h2>🔔 Напоминание о записи</h2>

    <p>
        Здравствуйте, <strong>{{ $clientName }}</strong>!
    </p>

    <p>
        Напоминаем, что у вас запись уже завтра:
    </p>

    <div class="info-block">
        <p>
            <strong>📅 Дата:</strong> {{ $date }}<br>
            <strong>⏰ Время:</strong> {{ $time }}<br>
            <strong>💇 Услуга:</strong> {{ $service }}<br>
            <strong>👤 Специалист:</strong> {{ $employee }}<br>
            <strong>📍 Адрес:</strong> {{ $address ?? 'Не указан' }}
        </p>
    </div>

    <div style="text-align: center; margin: 16px 0;">
        <a href="{{ $bookingUrl }}" class="btn" style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);">
            📅 Управление записью
        </a>
    </div>

    <hr class="divider">

    <p style="font-size: 14px; color: #6B7280;">
        С уважением,<br>
        <strong>Команда Запишись</strong>
    </p>
@endsection
