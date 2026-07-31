<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'Запишись'))</title>
    <style>
        /* Reset */
        body, table, td, p, a, div, span {
            margin: 0;
            padding: 0;
            border: 0;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background-color: #f8fafc;
            padding: 40px 20px;
        }

        .container {
            max-width: 580px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            padding: 32px 40px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .header .subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            margin-top: 4px;
        }

        .body {
            padding: 40px;
            background: #ffffff;
        }

        .body h2 {
            color: #111827;
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 16px 0;
            letter-spacing: -0.3px;
        }

        .body p {
            color: #4B5563;
            font-size: 15px;
            line-height: 1.7;
            margin: 0 0 16px 0;
        }

        .body .btn {
            display: inline-block;
            padding: 12px 32px;
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            margin: 8px 0 16px 0;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
            transition: all 0.2s;
        }

        .body .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.45);
        }

        .body .divider {
            border: none;
            height: 1px;
            background: #e5e7eb;
            margin: 24px 0;
        }

        .body .info-block {
            background: #f9fafb;
            border-radius: 12px;
            padding: 16px 20px;
            margin: 16px 0;
            border-left: 4px solid #4F46E5;
        }

        .body .info-block p {
            margin: 0;
            color: #374151;
            font-size: 14px;
        }

        .footer {
            padding: 24px 40px;
            background: #f9fafb;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }

        .footer p {
            color: #9CA3AF;
            font-size: 13px;
            margin: 0 0 8px 0;
            line-height: 1.6;
        }

        .footer .links a {
            color: #4F46E5;
            text-decoration: none;
            font-size: 13px;
            margin: 0 12px;
        }

        .footer .links a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .header {
                padding: 24px 20px;
            }
            .header h1 {
                font-size: 20px;
            }
            .body {
                padding: 24px 20px;
            }
            .footer {
                padding: 20px 20px;
            }
            .body .btn {
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>📋 Запишись</h1>
        <div class="subtitle">Сервис онлайн-записи</div>
    </div>

    <!-- Body -->
    <div class="body">
        @yield('content')
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>
            © {{ date('Y') }} Запишись. Все права защищены.
        </p>
        <p style="font-size: 12px; color: #d1d5db;">
            Это автоматическое письмо, пожалуйста, не отвечайте на него.
        </p>
        <div class="links">
            <a href="{{ config('app.url') }}">Главная</a>
            <a href="{{ config('app.url') . '/login' }}">Войти</a>
            <a href="{{ config('app.url') . '/register' }}">Регистрация</a>
        </div>
    </div>
</div>
</body>
</html>
