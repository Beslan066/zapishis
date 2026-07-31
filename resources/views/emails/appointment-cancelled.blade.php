@extends('emails.layout')

@section('title', 'Запись отменена')

@section('content')
    <h2>❌ Запись отменена</h2>

    <p>
        Здравствуйте, <strong>{{ $clientName }}</strong>!
    </p>

    <p>
        Ваша запись была отменена:
    </p>

    <div class="info-block">
        <p>
            <strong>📅 Дата:</strong> {{ $date }}<br>
            <strong>⏰ Время:</strong> {{ $time }}<br>
            <strong>💇 Услуга:</strong> {{ $service }}
        </p>
    </div>

    @if($reason)
        <p style="font-size: 14px; color: #6B7280;">
            <strong>Причина отмены:</strong> {{ $reason }}
        </p>
    @endif

    <p style="font-size: 14px; color: #6B7280;">
        Вы можете создать новую запись в любое удобное время.
    </p>

    <div style="text-align: center; margin: 16px 0;">
        <a href="{{ config('app.url') }}" class="btn">
            📋 Записаться снова
        </a>
    </div>

    <hr class="divider">

    <p style="font-size: 14px; color: #6B7280;">
        С уважением,<br>
        <strong>Команда Запишись</strong>
    </p>
@endsection
