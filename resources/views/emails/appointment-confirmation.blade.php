@extends('emails.layout')

@section('title', 'Подтверждение записи')

@section('content')
    <h2>✅ Запись подтверждена!</h2>

    <p>
        Здравствуйте, <strong>{{ $clientName }}</strong>!
    </p>

    <p>
        Ваша запись успешно создана. Вот детали:
    </p>

    <div class="info-block">
        <p>
            <strong>📅 Дата:</strong> {{ $date }}<br>
            <strong>⏰ Время:</strong> {{ $time }}<br>
            <strong>💇 Услуга:</strong> {{ $service }}<br>
            <strong>👤 Специалист:</strong> {{ $employee }}<br>
            <strong>💰 Стоимость:</strong> {{ number_format($price, 0, '.', ' ') }} ₽<br>
            <strong>📍 Адрес:</strong> {{ $address ?? 'Не указан' }}
        </p>
    </div>

    <p style="font-size: 14px; color: #6B7280;">
        Если вам нужно изменить или отменить запись, свяжитесь с нами.
    </p>

    <div style="text-align: center; margin: 16px 0;">
        <a href="{{ $bookingUrl }}" class="btn" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);">
            📅 Мои записи
        </a>
    </div>

    <hr class="divider">

    <p style="font-size: 14px; color: #6B7280;">
        С уважением,<br>
        <strong>Команда Запишись</strong>
    </p>
@endsection
