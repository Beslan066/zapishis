@extends('emails.layout')

@section('title', 'Сброс пароля')

@section('content')
    <h2>Сброс пароля</h2>

    <p>
        Здравствуйте! Мы получили запрос на сброс пароля для вашей учетной записи.
    </p>

    <p>
        Для создания нового пароля нажмите на кнопку ниже:
    </p>

    <div style="text-align: center;">
        <a href="{{ $url }}" class="btn">
            Сбросить пароль
        </a>
    </div>

    <div class="info-block">
        <p>
            <strong>ℹ️ Ссылка действительна 60 минут.</strong><br>
            Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.
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
