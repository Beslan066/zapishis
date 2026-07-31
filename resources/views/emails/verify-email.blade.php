@extends('emails.layout')

@section('title', 'Подтверждение email')

@section('content')
    <h2>Подтвердите ваш email</h2>

    <p>
        Здравствуйте! Спасибо за регистрацию на платформе <strong>Запишись</strong>.
    </p>

    <p>
        Для завершения регистрации и начала работы подтвердите ваш email адрес,
        нажав на кнопку ниже:
    </p>

    <div style="text-align: center;">
        <a href="{{ $url }}" class="btn">
            Подтвердить email
        </a>
    </div>

    <div class="info-block">
        <p>
            <strong>ℹ️ Ссылка действительна 60 минут.</strong><br>
            Если вы не регистрировались на нашем сайте, просто проигнорируйте это письмо.
        </p>
    </div>

    <p style="font-size: 14px; color: #6B7280;">
        Если кнопка не работает, скопируйте ссылку в браузер:<br>
        <span style="word-break: break-all; color: #4F46E5; font-size: 13px;">
            {{ $url }}
        </span>
    </p>

    <hr class="divider">

    <p style="font-size: 14px; color: #6B7280;">
        С уважением,<br>
        <strong>Команда Запишись</strong>
    </p>
@endsection
