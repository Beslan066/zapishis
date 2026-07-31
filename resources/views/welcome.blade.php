<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Запишись') }} - Сервис онлайн-записи клиентов</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css'])

    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            box-sizing: border-box;
        }

        :root {
            --primary-start: #4F46E5;
            --primary-end: #7C3AED;
            --primary-light: #EEF2FF;
            --primary-dark: #1E1B4B;
            --gray-50: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-300: #CBD5E1;
            --gray-400: #94A3B8;
            --gray-500: #64748B;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1E293B;
            --gray-900: #0F172A;
        }

        .gradient-primary {
            background: linear-gradient(135deg, var(--primary-start), var(--primary-end));
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--primary-start), var(--primary-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Навигация */
        .nav-link {
            position: relative;
            color: #374151;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .nav-link:hover {
            color: var(--primary-start);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, var(--primary-start), var(--primary-end));
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-start), var(--primary-end));
            color: white;
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.9);
            color: #1f2937;
            padding: 12px 28px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.08);
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        /* Контейнер шире */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 32px;
        }

        /* ========================================================= */
        /* ГЕРОЙ СО СЛАЙДЕРОМ                                        */
        /* ========================================================= */
        .hero-section {
            padding: 20px 0 40px;
        }

        .hero-inner {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
            background: linear-gradient(135deg, #1E1B4B, #312E81);
            border-radius: 32px;
            padding: 48px 56px;
            position: relative;
            overflow: hidden;
            min-height: 440px;
        }

        .hero-blob {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-blob-1 {
            width: 400px;
            height: 400px;
            top: -140px;
            right: -80px;
            background: rgba(124, 58, 237, 0.08);
        }

        .hero-blob-2 {
            width: 450px;
            height: 450px;
            bottom: -200px;
            left: -140px;
            background: rgba(79, 70, 229, 0.05);
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-content h1 {
            font-size: 48px;
            font-weight: 800;
            color: white;
            line-height: 1.1;
            margin-bottom: 16px;
        }

        .hero-content h1 span {
            background: linear-gradient(135deg, #818CF8, #A78BFA);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-content p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 17px;
            line-height: 1.7;
            max-width: 440px;
            margin-bottom: 28px;
        }

        .hero-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        /* Слайдер в герое */
        .hero-slider {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        .hero-slider .slide-container {
            width: 100%;
            max-width: 380px;
            aspect-ratio: 16/10;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            overflow: hidden;
            position: relative;
            padding: 8px;
        }

        .hero-slider .slide-container .slide {
            width: 100%;
            height: 100%;
            border-radius: 18px;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: rgba(255, 255, 255, 0.2);
            font-size: 13px;
            gap: 8px;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.1), rgba(124, 58, 237, 0.05));
        }

        .hero-slider .slide-container .slide.active {
            display: flex;
        }

        .hero-slider .slide-container .slide .slide-icon {
            font-size: 44px;
            opacity: 0.5;
        }

        .hero-slider .slide-container .slide .slide-label {
            font-size: 14px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.4);
        }

        .hero-slider .slide-container .slide .slide-desc {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.2);
        }

        .slider-dots {
            display: flex;
            gap: 8px;
        }

        .slider-dots .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .slider-dots .dot.active {
            background: white;
            width: 28px;
            border-radius: 4px;
        }

        /* Карточки сервисов */
        .service-card {
            background: white;
            border-radius: 16px;
            padding: 16px 20px;
            transition: all 0.3s ease;
            border: 1px solid var(--gray-100);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
        }

        .service-card:hover {
            border-color: var(--primary-start);
            box-shadow: 0 8px 24px rgba(79, 70, 229, 0.06);
            transform: translateY(-2px);
        }

        .service-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .service-icon.gradient {
            background: linear-gradient(135deg, var(--primary-start), var(--primary-end));
            color: white;
        }

        .service-icon.light {
            background: var(--primary-light);
            color: var(--primary-start);
        }

        .service-icon.green {
            background: #ECFDF5;
            color: #059669;
        }

        .service-icon.amber {
            background: #FFFBEB;
            color: #D97706;
        }

        .service-icon.rose {
            background: #FFF1F2;
            color: #E11D48;
        }

        .service-icon svg {
            width: 20px;
            height: 20px;
        }

        .service-label {
            font-weight: 600;
            color: var(--gray-800);
            font-size: 14px;
        }

        .service-badge {
            margin-left: auto;
            padding: 2px 12px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 500;
            background: var(--gray-100);
            color: var(--gray-500);
        }

        /* Карточки целей */
        .goal-card {
            border-radius: 20px;
            padding: 32px;
            transition: all 0.4s ease;
            display: flex;
            flex-direction: column;
        }

        .goal-card:hover {
            transform: translateY(-6px);
        }

        .goal-card.white {
            background: white;
            border: 1px solid var(--gray-100);
        }

        .goal-card.gradient {
            background: linear-gradient(135deg, var(--primary-start), var(--primary-end));
            color: white;
        }

        .goal-card .icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            flex-shrink: 0;
        }

        .goal-card .icon-wrap svg {
            width: 28px;
            height: 28px;
        }

        .goal-card.white .icon-wrap {
            background: var(--primary-light);
            color: var(--primary-start);
        }

        .goal-card.gradient .icon-wrap {
            background: rgba(255, 255, 255, 0.12);
            color: white;
        }

        .goal-card h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .goal-card.white h3 {
            color: var(--gray-900);
        }

        .goal-card.gradient h3 {
            color: white;
        }

        .goal-card p {
            font-size: 14px;
            line-height: 1.7;
            flex: 1;
        }

        .goal-card.white p {
            color: var(--gray-500);
        }

        .goal-card.gradient p {
            color: rgba(255, 255, 255, 0.75);
        }

        .goal-card .image-placeholder {
            margin-top: 20px;
            border-radius: 12px;
            height: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            color: var(--gray-400);
            overflow: hidden;
        }

        .goal-card.white .image-placeholder {
            background: var(--gray-50);
        }

        .goal-card.gradient .image-placeholder {
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.25);
        }

        /* Категории */
        .category-item {
            padding: 14px 18px;
            border-radius: 12px;
            background: white;
            border: 1px solid var(--gray-100);
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            color: var(--gray-700);
            font-size: 14px;
        }

        .category-item:hover {
            border-color: var(--primary-start);
            box-shadow: 0 4px 16px rgba(79, 70, 229, 0.06);
            transform: translateX(4px);
        }

        .category-item .emoji {
            font-size: 20px;
            width: 32px;
            text-align: center;
        }

        /* Отзывы */
        .review-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid var(--gray-100);
            transition: all 0.3s ease;
            min-width: 280px;
        }

        .review-card:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.04);
            transform: translateY(-4px);
        }

        .review-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
        }

        /* Тарифы */
        .pricing-card {
            background: white;
            border-radius: 20px;
            padding: 28px;
            border: 1px solid var(--gray-100);
            transition: all 0.4s ease;
            position: relative;
        }

        .pricing-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(79, 70, 229, 0.06);
        }

        .pricing-card.popular {
            border: 2px solid var(--primary-start);
        }

        .pricing-card.popular::before {
            content: 'Самый выгодный';
            position: absolute;
            top: -11px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--primary-start), var(--primary-end));
            color: white;
            padding: 4px 18px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }

        .pricing-price {
            font-size: 42px;
            font-weight: 800;
            color: var(--gray-900);
        }

        .pricing-price span {
            font-size: 18px;
            font-weight: 600;
        }

        .pricing-feature {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--gray-700);
            font-size: 14px;
        }

        .pricing-feature svg {
            flex-shrink: 0;
            color: #059669;
        }

        /* Tab buttons */
        .tab-btn {
            padding: 8px 18px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 1px solid var(--gray-200);
            cursor: pointer;
            background: white;
            color: var(--gray-500);
        }

        .tab-btn:hover {
            border-color: var(--primary-start);
            color: var(--primary-start);
        }

        .tab-btn.active {
            background: linear-gradient(135deg, var(--primary-start), var(--primary-end));
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.25);
        }

        /* Общие утилиты */
        .section-padding {
            padding: 48px 0;
        }

        .tabs-container {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            justify-content: center;
        }

        .scrollbooster-viewport {
            overflow: auto;
            cursor: grab;
            padding-bottom: 16px;
        }

        .scrollbooster-viewport:active {
            cursor: grabbing;
        }

        .scrollbooster-content {
            display: flex;
            gap: 20px;
        }

        .arrow-left,
        .arrow-right {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: white;
            border: 1px solid var(--gray-200);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .arrow-left:hover,
        .arrow-right:hover {
            background: var(--primary-light);
            border-color: var(--primary-start);
        }

        /* Бургер-меню */
        .header-burger {
            display: none;
        }

        @media (max-width: 991.92px) {
            .header-burger {
                display: block;
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                width: 280px;
                background: white;
                z-index: 100;
                padding: 24px;
                transform: translateX(100%);
                transition: transform 0.3s ease;
                box-shadow: -4px 0 24px rgba(0, 0, 0, 0.06);
            }

            .header.is-menu-open .header-burger {
                transform: translateX(0);
            }

            .header__overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.3);
                backdrop-filter: blur(4px);
                z-index: 99;
            }

            .header.is-menu-open .header__overlay {
                display: block;
            }

            .header__burger-icon {
                display: flex !important;
                cursor: pointer;
                padding: 8px;
                border: none;
                background: none;
            }

            .desktop-menu {
                display: none !important;
            }
        }

        @media (min-width: 992px) {
            .header__burger-icon {
                display: none !important;
            }
        }

        @media (max-width: 1024px) {
            .hero-inner {
                grid-template-columns: 1fr;
                text-align: center;
                padding: 32px 28px;
                min-height: auto;
            }

            .hero-content p {
                margin: 0 auto 24px;
            }

            .hero-buttons {
                justify-content: center;
            }

            .hero-slider .slide-container {
                max-width: 300px;
            }

            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .pricing-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .goal-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .category-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .companies-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 16px;
            }

            .hero-content h1 {
                font-size: 32px;
            }

            .hero-inner {
                padding: 24px 16px;
            }

            .hero-slider .slide-container {
                max-width: 260px;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .pricing-grid {
                grid-template-columns: 1fr;
            }

            .goal-grid {
                grid-template-columns: 1fr;
            }

            .category-grid {
                grid-template-columns: 1fr;
            }

            .companies-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .section-padding {
                padding: 32px 0;
            }

            .cta-card {
                padding: 32px 20px;
            }

            .cta-card h2 {
                font-size: 26px;
            }
        }

        @media (max-width: 480px) {
            .hero-content h1 {
                font-size: 26px;
            }

            .hero-buttons .btn-primary,
            .hero-buttons .btn-secondary {
                width: 100%;
                justify-content: center;
            }

            .hero-slider .slide-container {
                max-width: 220px;
            }

            .pricing-price {
                font-size: 34px;
            }

            .goal-card {
                padding: 24px;
            }
        }

        /* CTA */
        .cta-card {
            padding: 48px 40px;
            border-radius: 28px;
            background: linear-gradient(135deg, #1E1B4B, #312E81);
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .cta-card .cta-blob {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .cta-card .cta-blob-1 {
            width: 350px;
            height: 350px;
            top: -180px;
            right: -80px;
            background: rgba(124, 58, 237, 0.06);
        }

        .cta-card .cta-blob-2 {
            width: 280px;
            height: 280px;
            bottom: -130px;
            left: -80px;
            background: rgba(79, 70, 229, 0.04);
        }

        .cta-card .cta-content {
            position: relative;
            z-index: 1;
            max-width: 640px;
            margin: 0 auto;
        }

        .cta-card h2 {
            font-size: 34px;
            font-weight: 800;
            color: white;
            margin-bottom: 12px;
        }

        .cta-card p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 17px;
            line-height: 1.7;
            margin-bottom: 28px;
        }

        .cta-card .btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 40px;
            border-radius: 14px;
            background: white;
            color: var(--primary-start);
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 8px 32px rgba(79, 70, 229, 0.25);
        }

        .cta-card .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(79, 70, 229, 0.35);
        }

        /* ========================================================= */
        /* КОМПАНИИ — увеличенные, солидные карточки                 */
        /* ========================================================= */
        .companies-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .company-card {
            padding: 28px;
            background: white;
            border-radius: 20px;
            border: 1px solid var(--gray-100);
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
        }

        .company-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.06);
            border-color: var(--primary-start);
        }

        .company-card .company-top {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 14px;
        }

        .company-card .company-logo {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-start);
        }

        .company-card .company-rating {
            padding: 4px 14px;
            border-radius: 100px;
            background: #ECFDF5;
            color: #059669;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .company-card .company-name {
            font-weight: 700;
            color: var(--gray-900);
            font-size: 17px;
            margin-bottom: 4px;
        }

        .company-card .company-location {
            color: var(--gray-500);
            font-size: 14px;
        }

        .company-card .company-stats {
            display: flex;
            gap: 16px;
            margin-top: 14px;
            font-size: 13px;
            color: var(--gray-400);
        }

        .company-card .company-stats span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .company-card .company-footer {
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid var(--gray-100);
            color: var(--primary-start);
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: gap 0.3s ease;
        }

        .company-card:hover .company-footer {
            gap: 12px;
        }

        .footer {
            padding: 32px 0;
            border-top: 1px solid var(--gray-100);
        }

        .footer-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .footer-links {
            display: flex;
            gap: 20px;
        }

        .footer-links a {
            color: var(--gray-400);
            font-size: 13px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--gray-600);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feature-card {
            padding: 28px 24px;
            background: white;
            border-radius: 16px;
            border: 1px solid var(--gray-100);
            text-align: center;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
        }

        .feature-card .icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .feature-card .icon svg {
            width: 24px;
            height: 24px;
        }

        .feature-card .icon.primary {
            background: var(--primary-light);
            color: var(--primary-start);
        }

        .feature-card .icon.green {
            background: #ECFDF5;
            color: #059669;
        }

        .feature-card .icon.blue {
            background: #EFF6FF;
            color: #2563EB;
        }

        .feature-card h3 {
            font-size: 17px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 4px;
        }

        .feature-card p {
            color: var(--gray-500);
            font-size: 14px;
            line-height: 1.6;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .goal-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .category-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .category-grid-inner {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 28px;
            text-align: center;
            border: 1px solid var(--gray-100);
        }

        .stat-card .stat-number {
            font-size: 36px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-start), var(--primary-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-card .stat-label {
            color: var(--gray-500);
            font-size: 14px;
            margin-top: 2px;
        }

        @media (max-width: 768px) {
            .category-grid {
                grid-template-columns: 1fr;
            }

            .category-grid-inner {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .category-grid-inner {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">

<!-- ФОН -->
<div class="fixed inset-0 -z-10 overflow-hidden">
    <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-full blur-3xl animate-pulse"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-tr from-blue-500/20 to-indigo-500/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-r from-indigo-500/10 to-purple-500/10 rounded-full blur-3xl"></div>
</div>

<!-- ========================================================= -->
<!-- НАВИГАЦИЯ                                                 -->
<!-- ========================================================= -->
<header class="header" style="position: sticky; top: 0; z-index: 50; background: rgba(255,255,255,0.85); backdrop-filter: blur(16px); border-bottom: 1px solid rgba(0,0,0,0.04);">
    <div class="container" style="padding: 0 32px;">
        <div style="display: flex; justify-content: space-between; align-items: center; height: 68px;">

            <button class="header__burger-icon" style="display: none; background: none; border: none; padding: 8px;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round"/>
                </svg>
            </button>

            <a href="/" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #4F46E5, #7C3AED); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(79,70,229,0.3);">
                    <span style="color: white; font-weight: 900; font-size: 18px;">З</span>
                </div>
                <span style="font-size: 20px; font-weight: 800; background: linear-gradient(135deg, #4F46E5, #7C3AED); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Запишись
                </span>
            </a>

            <div class="desktop-menu" style="display: flex; align-items: center; gap: 28px;">
                <nav style="display: flex; align-items: center; gap: 4px;">
                    <a href="#" class="nav-link" style="font-size: 14px;">Возможности</a>
                    <a href="#" class="nav-link" style="font-size: 14px;">Для кого</a>
                    <a href="#" class="nav-link" style="font-size: 14px;">Цены</a>
                    <a href="#" class="nav-link" style="font-size: 14px;">Блог</a>
                </nav>

                <div style="display: flex; align-items: center; gap: 8px;">
                    <a href="{{ route('login') }}" style="color: #374151; font-weight: 500; font-size: 14px; padding: 8px 14px; transition: color 0.3s; text-decoration: none;">
                        Войти
                    </a>
                    <a href="{{ route('register') }}" class="btn-primary" style="padding: 8px 20px; font-size: 13px;">
                        Начать
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="header__overlay"></div>

    <div class="header-burger">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px;">
            <span style="font-size: 18px; font-weight: 800; background: linear-gradient(135deg, #4F46E5, #7C3AED); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Запишись</span>
            <button class="header-burger__close" style="background: none; border: none; font-size: 26px; cursor: pointer; color: #6b7280;">✕</button>
        </div>
        <nav style="display: flex; flex-direction: column; gap: 16px;">
            <a href="#" style="color: #1f2937; font-weight: 500; font-size: 16px; text-decoration: none;">Возможности</a>
            <a href="#" style="color: #1f2937; font-weight: 500; font-size: 16px; text-decoration: none;">Для кого</a>
            <a href="#" style="color: #1f2937; font-weight: 500; font-size: 16px; text-decoration: none;">Цены</a>
            <a href="#" style="color: #1f2937; font-weight: 500; font-size: 16px; text-decoration: none;">Блог</a>
            <div style="border-top: 1px solid #f3f4f6; padding-top: 16px; margin-top: 4px; display: flex; flex-direction: column; gap: 10px;">
                <a href="{{ route('login') }}" style="color: #374151; font-weight: 500; text-decoration: none;">Войти</a>
                <a href="{{ route('register') }}" class="btn-primary" style="justify-content: center; text-align: center;">Регистрация</a>
            </div>
        </nav>
    </div>
</header>

<!-- ========================================================= -->
<!-- ОСНОВНОЙ КОНТЕНТ                                          -->
<!-- ========================================================= -->
<main class="container">

    <!-- ========================================================= -->
    <!-- ГЕРОЙ СО СЛАЙДЕРОМ                                        -->
    <!-- ========================================================= -->
    <section class="hero-section">
        <div class="hero-inner">
            <div class="hero-blob hero-blob-1"></div>
            <div class="hero-blob hero-blob-2"></div>

            <div class="hero-content">
                <h1>
                    Записывайте клиентов<br>
                    <span>легко и быстро</span>
                </h1>
                <p>
                    Сервис онлайн-записи для салонов красоты, барбершопов, стоматологий
                    и других сфер услуг на Кавказе
                </p>
                <div class="hero-buttons">
                    <a href="{{ route('register') }}" class="btn-primary" style="padding: 14px 32px; font-size: 15px;">
                        Попробовать бесплатно
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="#pricing" class="btn-secondary" style="background: rgba(255,255,255,0.06); color: white; border-color: rgba(255,255,255,0.1); padding: 14px 32px; font-size: 15px;">
                        Смотреть тарифы
                    </a>
                </div>
            </div>

            <!-- Слайдер -->
            <div class="hero-slider">
                <div class="slide-container">
                    <!-- Слайд 1: Мобильное приложение -->
                    <div class="slide active" data-slide="0">
                        <div class="slide-icon">📱</div>
                        <div class="slide-label">Мобильное приложение</div>
                        <div class="slide-desc">Управляйте бизнесом с телефона</div>
                    </div>
                    <!-- Слайд 2: Веб-версия -->
                    <div class="slide" data-slide="1">
                        <div class="slide-icon">💻</div>
                        <div class="slide-label">Веб-версия</div>
                        <div class="slide-desc">Работайте с компьютера</div>
                    </div>
                    <!-- Слайд 3: Аналитика -->
                    <div class="slide" data-slide="2">
                        <div class="slide-icon">📊</div>
                        <div class="slide-label">Аналитика в реальном времени</div>
                        <div class="slide-desc">Все показатели на одном экране</div>
                    </div>
                </div>
                <div class="slider-dots">
                    <span class="dot active" data-slide="0"></span>
                    <span class="dot" data-slide="1"></span>
                    <span class="dot" data-slide="2"></span>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================================= -->
    <!-- ПРЕИМУЩЕСТВА                                              -->
    <!-- ========================================================= -->
    <section style="padding: 20px 0 40px;">
        <div class="features-grid">
            <div class="feature-card">
                <div class="icon primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <h3>Удобная запись</h3>
                <p>Клиенты записываются онлайн 24/7</p>
            </div>

            <div class="feature-card">
                <div class="icon green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h3>Учёт клиентов</h3>
                <p>База и история посещений</p>
            </div>

            <div class="feature-card">
                <div class="icon blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <h3>Автонапоминания</h3>
                <p>SMS и Telegram уведомления</p>
            </div>
        </div>
    </section>

    <!-- "СОТНИ ЗАДАЧ. ОДНА ЭКОСИСТЕМА"                            -->
    <section style="padding: 40px 0 30px;">
        <div style="text-align: center; margin-bottom: 36px;">
            <h2 style="font-size: 38px; font-weight: 800; color: #1f2937;">Сотни задач. Одна экосистема</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">

            <!-- Колонка 1 -->
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <div class="service-card">
                    <div class="service-icon gradient">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <span class="service-label">Онлайн-запись</span>
                    <span class="service-badge">популярно</span>
                </div>
                <div class="service-card">
                    <div class="service-icon light">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>
                    <span class="service-label">Уведомления</span>
                    <span class="service-badge">авто</span>
                </div>
                <div class="service-card">
                    <div class="service-icon green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 6v6l4 2"/>
                        </svg>
                    </div>
                    <span class="service-label">Финансовый учет</span>
                </div>
                <div class="service-card">
                    <div class="service-icon amber">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12v-2a5 5 0 0 0-5-5H8a5 5 0 0 0-5 5v2"/>
                            <circle cx="12" cy="16" r="5"/>
                            <path d="M12 11v5"/>
                        </svg>
                    </div>
                    <span class="service-label">Статистика</span>
                </div>
                <div class="service-card">
                    <div class="service-icon rose">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <span class="service-label">Клиентская база</span>
                </div>
            </div>

            <!-- Колонка 2 -->
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <div class="service-card">
                    <div class="service-icon gradient">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                        </svg>
                    </div>
                    <span class="service-label">Расчет зарплат</span>
                </div>
                <div class="service-card">
                    <div class="service-icon light">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5"/>
                            <path d="M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <span class="service-label">Программы лояльности</span>
                </div>
                <div class="service-card">
                    <div class="service-icon green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                            <line x1="12" y1="18" x2="12.01" y2="18"/>
                        </svg>
                    </div>
                    <span class="service-label">Бренд-приложение</span>
                </div>
                <div class="service-card">
                    <div class="service-icon amber">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                            <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/>
                        </svg>
                    </div>
                    <span class="service-label">Электронный журнал</span>
                </div>
                <div class="service-card">
                    <div class="service-icon rose">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <span class="service-label">Телефония</span>
                </div>
            </div>

            <!-- Колонка 3 -->
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <div class="service-card">
                    <div class="service-icon gradient">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <path d="M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                    </div>
                    <span class="service-label">Складской учет</span>
                </div>
                <div class="service-card">
                    <div class="service-icon light">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                        </svg>
                    </div>
                    <span class="service-label">Управление сетью</span>
                </div>
                <div class="service-card">
                    <div class="service-icon green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                            <line x1="12" y1="18" x2="12.01" y2="18"/>
                        </svg>
                    </div>
                    <span class="service-label">Мобильный доступ</span>
                </div>
                <div class="service-card">
                    <div class="service-icon amber">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="2" y1="12" x2="22" y2="12"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        </svg>
                    </div>
                    <span class="service-label">Интеграции</span>
                    <span class="service-badge">100+</span>
                </div>
                <div class="service-card" style="opacity: 0.5; cursor: default;">
                    <div class="service-icon" style="background: var(--gray-100); color: var(--gray-400);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="16"/>
                            <line x1="8" y1="12" x2="16" y2="12"/>
                        </svg>
                    </div>
                    <span style="font-weight: 400; color: var(--gray-400); font-size: 14px;">и многое другое</span>
                </div>
            </div>

        </div>
    </section>

    <!-- НАШИ КЛИЕНТЫ -->
    <section style="padding: 40px 0 60px;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
            <div>
                <h2 style="font-size: 32px; font-weight: 800; color: #0F172A; letter-spacing: -0.5px;">Наши клиенты</h2>
                <p style="color: #64748B; font-size: 16px; margin-top: 4px;">Более 55 000 компаний доверяют нам</p>
            </div>
            <a href="{{ route('login') }}" style="color: #4F46E5; font-weight: 600; text-decoration: none; font-size: 15px; display: inline-flex; align-items: center; gap: 6px; padding: 8px 0; border-bottom: 2px solid transparent; transition: border-color 0.3s;">
                Все компании
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
            @forelse($businesses ?? [] as $business)
                <a href="{{ route('public.booking', $business->slug) }}"
                   style="display: block; background: white; border-radius: 20px; padding: 24px 24px 20px; border: 1px solid #F1F5F9; text-decoration: none; transition: all 0.3s ease; position: relative; overflow: hidden;">

                    <!-- Верхняя часть с логотипом и рейтингом -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                        <div style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #EEF2FF, #EDE9FE); display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 700; color: #4F46E5; flex-shrink: 0;">
                            {{ strtoupper(substr($business->name, 0, 1)) }}
                        </div>
                        <div style="display: flex; align-items: center; gap: 4px; padding: 4px 12px; border-radius: 100px; background: #ECFDF5; color: #059669; font-size: 13px; font-weight: 600;">
                            <span>★</span> 4.8
                        </div>
                    </div>

                    <!-- Название и локация -->
                    <div style="font-weight: 700; font-size: 17px; color: #0F172A; margin-bottom: 2px;">{{ $business->name }}</div>
                    <div style="color: #64748B; font-size: 14px;">
                        @if($business->city) {{ $business->city }}, @endif
                        @if($business->region) {{ $business->region }} @endif
                    </div>

                    <!-- Статистика -->
                    <div style="display: flex; gap: 16px; margin-top: 14px; font-size: 13px; color: #94A3B8;">
                        <span style="display: flex; align-items: center; gap: 4px;">👤 {{ $business->clients_count ?? 0 }} клиентов</span>
                        <span style="display: flex; align-items: center; gap: 4px;">📅 {{ $business->appointments_count ?? 0 }} записей</span>
                    </div>

                    <!-- Кнопка записи -->
                    <div style="margin-top: 18px; padding-top: 16px; border-top: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between;">
                    <span style="color: #4F46E5; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 6px; transition: gap 0.3s;">
                        Записаться
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="transition: transform 0.3s;">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </span>
                        <span style="font-size: 12px; color: #94A3B8;">свободно</span>
                    </div>
                </a>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: white; border-radius: 20px; border: 1px solid #F1F5F9;">
                    <div style="font-size: 48px; margin-bottom: 12px;">🏢</div>
                    <h3 style="font-size: 20px; font-weight: 700; color: #0F172A; margin-bottom: 6px;">Пока нет компаний</h3>
                    <p style="color: #94A3B8; font-size: 15px;">Станьте первым, кто присоединится к платформе</p>
                    @auth
                        <a href="{{ route('businesses.create') }}" style="display: inline-block; margin-top: 16px; padding: 12px 32px; background: linear-gradient(135deg, #4F46E5, #7C3AED); color: white; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.3s;">
                            Создать компанию
                        </a>
                    @endauth
                </div>
            @endforelse
        </div>

        <!-- Кнопка "Показать еще" (опционально) -->
        @if(($businesses ?? collect())->count() > 4)
            <div style="text-align: center; margin-top: 32px;">
                <button style="padding: 12px 40px; border-radius: 12px; border: 1px solid #E2E8F0; background: white; color: #1E293B; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.3s;">
                    Показать еще компании
                </button>
            </div>
        @endif
    </section>

    <!-- БЛОК: ЗАПИШИСЬ ПОМОЖЕТ ДОСТИЧЬ ЛЮБЫХ БИЗНЕС-ЦЕЛЕЙ        -->
    <section style="padding: 80px 0 60px; background: #ffffff;">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 32px;">

            <!-- Заголовок -->
            <div style="text-align: center; margin-bottom: 60px;">
                <h2 style="font-size: 44px; font-weight: 700; color: #0F172A; letter-spacing: -0.02em;">
                    Запишись поможет достичь <span style="background: linear-gradient(135deg, #4F46E5, #7C3AED); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">любых бизнес-целей</span>
                </h2>
            </div>

            <!-- Верхний ряд: 3 карточки -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; margin-bottom: 30px;">

                <!-- Карточка 1: Привлекайте клиентов -->
                <div style="background: #F7F7F9; border-radius: 30px; padding: 36px 32px 0; display: flex; flex-direction: column; min-height: 420px; position: relative; overflow: hidden;">

                    <!-- Иконка -->
                    <div style="width: 64px; height: 64px; background: #EEF2FF; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 20px; flex-shrink: 0;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#4F46E5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 6.5L12 13 2 6.5M22 6.5v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-10M12 13v8"/>
                        </svg>
                    </div>

                    <h3 style="font-size: 22px; font-weight: 600; color: #0F172A; margin-bottom: 12px; line-height: 1.3;">
                        Привлекайте клиентов бесплатно 24/7
                    </h3>

                    <p style="font-size: 15px; line-height: 1.6; color: #475569; flex: 1; margin-bottom: 24px;">
                        Разместите онлайн-запись на сайте и на 15 партнерских площадках. Получайте больше посетителей через популярные соцсети.
                    </p>

                    <!-- Изображение -->
                    <div style="margin: 0 -32px 0; height: 180px; background: #E8EAF0; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                        <!-- ЗАМЕНИТЕ src НА СВОЮ КАРТИНКУ -->
                        <img src="/images/yclients-attract.png" alt="Привлечение клиентов" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>

                <!-- Карточка 2: Не тратьте время на рутину -->
                <div style="background: #F7F7F9; border-radius: 30px; padding: 36px 32px 0; display: flex; flex-direction: column; min-height: 420px; position: relative; overflow: hidden;">

                    <!-- Иконка -->
                    <div style="width: 64px; height: 64px; background: #ECFDF5; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 20px; flex-shrink: 0;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>

                    <h3 style="font-size: 22px; font-weight: 600; color: #0F172A; margin-bottom: 12px; line-height: 1.3;">
                        Не тратьте время на рутину
                    </h3>

                    <p style="font-size: 15px; line-height: 1.6; color: #475569; flex: 1; margin-bottom: 24px;">
                        Все рутинные задачи автоматизированы. Складской учет, расчет зарплат, аналитика — всё в одном месте.
                    </p>

                    <!-- Изображение -->
                    <div style="margin: 0 -32px 0; height: 180px; background: #E8EAF0; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                        <!-- ЗАМЕНИТЕ src НА СВОЮ КАРТИНКУ -->
                        <img src="/images/yclients-automation.png" alt="Автоматизация" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>

                <!-- Карточка 3: Управляйте бизнесом откуда угодно (темная) -->
                <div style="background: #1E293B; border-radius: 30px; padding: 36px 32px 0; display: flex; flex-direction: column; min-height: 420px; position: relative; overflow: hidden;">

                    <!-- Иконка -->
                    <div style="width: 64px; height: 64px; background: rgba(255,255,255,0.08); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 20px; flex-shrink: 0;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
                            <line x1="12" y1="18" x2="12.01" y2="18"/>
                        </svg>
                    </div>

                    <h3 style="font-size: 22px; font-weight: 600; color: #FFFFFF; margin-bottom: 12px; line-height: 1.3;">
                        Управляйте бизнесом откуда угодно
                    </h3>

                    <p style="font-size: 15px; line-height: 1.6; color: rgba(255,255,255,0.7); flex: 1; margin-bottom: 24px;">
                        Отслеживайте прибыль, управляйте расписанием и настраивайте права доступа через мобильное приложение.
                    </p>

                    <!-- Изображение -->
                    <div style="margin: 0 -32px 0; height: 180px; background: rgba(255,255,255,0.04); display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                        <!-- ЗАМЕНИТЕ src НА СВОЮ КАРТИНКУ -->
                        <img src="/images/yclients-mobile.png" alt="Мобильное управление" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>

            </div>

            <!-- Нижний ряд: 3 карточки -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">

                <!-- Карточка 4: Автоматические уведомления -->
                <div style="background: #F7F7F9; border-radius: 30px; padding: 36px 32px 0; display: flex; flex-direction: column; min-height: 420px; position: relative; overflow: hidden;">

                    <!-- Иконка -->
                    <div style="width: 64px; height: 64px; background: #EFF6FF; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 20px; flex-shrink: 0;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>

                    <h3 style="font-size: 22px; font-weight: 600; color: #0F172A; margin-bottom: 12px; line-height: 1.3;">
                        Автоматические уведомления
                    </h3>

                    <p style="font-size: 15px; line-height: 1.6; color: #475569; flex: 1; margin-bottom: 24px;">
                        Используйте бесплатные уведомления в YPLACES и ВКонтакте, выберите подходящую интеграцию среди 40+ чат-ботов и смс-провайдеров.
                    </p>

                    <!-- Изображение -->
                    <div style="margin: 0 -32px 0; height: 180px; background: #E8EAF0; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                        <!-- ЗАМЕНИТЕ src НА СВОЮ КАРТИНКУ -->
                        <img src="/images/yclients-notifications.png" alt="Уведомления" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>

                <!-- Карточка 5: Финансовый учет -->
                <div style="background: #F7F7F9; border-radius: 30px; padding: 36px 32px 0; display: flex; flex-direction: column; min-height: 420px; position: relative; overflow: hidden;">

                    <!-- Иконка -->
                    <div style="width: 64px; height: 64px; background: #FFFBEB; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 20px; flex-shrink: 0;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 6v6l4 2"/>
                        </svg>
                    </div>

                    <h3 style="font-size: 22px; font-weight: 600; color: #0F172A; margin-bottom: 12px; line-height: 1.3;">
                        Финансовый учет и аналитика
                    </h3>

                    <p style="font-size: 15px; line-height: 1.6; color: #475569; flex: 1; margin-bottom: 24px;">
                        Отслеживайте финансовые показатели, анализируйте прибыль и управляйте бюджетом в реальном времени.
                    </p>

                    <!-- Изображение -->
                    <div style="margin: 0 -32px 0; height: 180px; background: #E8EAF0; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                        <!-- ЗАМЕНИТЕ src НА СВОЮ КАРТИНКУ -->
                        <img src="/images/yclients-finance.png" alt="Финансовый учет" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>

                <!-- Карточка 6: Клиентская база (темная) -->
                <div style="background: #1E293B; border-radius: 30px; padding: 36px 32px 0; display: flex; flex-direction: column; min-height: 420px; position: relative; overflow: hidden;">

                    <!-- Иконка -->
                    <div style="width: 64px; height: 64px; background: rgba(255,255,255,0.08); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 20px; flex-shrink: 0;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>

                    <h3 style="font-size: 22px; font-weight: 600; color: #FFFFFF; margin-bottom: 12px; line-height: 1.3;">
                        Клиентская база и лояльность
                    </h3>

                    <p style="font-size: 15px; line-height: 1.6; color: rgba(255,255,255,0.7); flex: 1; margin-bottom: 24px;">
                        Ведите полную историю клиентов, создавайте программы лояльности и увеличивайте возвращаемость.
                    </p>

                    <!-- Изображение -->
                    <div style="margin: 0 -32px 0; height: 180px; background: rgba(255,255,255,0.04); display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                        <!-- ЗАМЕНИТЕ src НА СВОЮ КАРТИНКУ -->
                        <img src="/images/yclients-clients.png" alt="Клиентская база" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ========================================================= -->
    <!-- "ПОДХОДИТ ДЛЯ ЛЮБОГО БИЗНЕСА"                             -->
    <!-- ========================================================= -->
    <section style="padding: 40px 0; background: #f8fafc; border-radius: 32px; margin: 10px 0;">
        <div style="text-align: center; max-width: 720px; margin: 0 auto 32px;">
            <h2 style="font-size: 34px; font-weight: 800; color: #1f2937;">
                Работаем с малым, средним и крупным бизнесом<br>
                <span class="gradient-text">в любой сфере</span>
            </h2>
        </div>

        <div class="category-grid">
            <div class="category-grid-inner">
                <div class="category-item">
                    <span class="emoji">💄</span> Красота
                </div>
                <div class="category-item">
                    <span class="emoji">🏥</span> Медицина
                </div>
                <div class="category-item">
                    <span class="emoji">🏋️</span> Спорт
                </div>
                <div class="category-item">
                    <span class="emoji">🎭</span> Досуг и отдых
                </div>
                <div class="category-item">
                    <span class="emoji">📚</span> Образование
                </div>
                <div class="category-item">
                    <span class="emoji">🚗</span> Авто
                </div>
                <div class="category-item">
                    <span class="emoji">🏪</span> Бытовые услуги
                </div>
                <div class="category-item">
                    <span class="emoji">🛍️</span> Розница
                </div>
            </div>

            <div style="background: white; border-radius: 20px; padding: 28px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; border: 1px solid var(--gray-100);">
                <h4 style="font-size: 18px; font-weight: 700; color: #1f2937;">Подходит для любого бизнеса</h4>
                <p style="color: #6b7280; font-size: 14px; margin-top: 6px; max-width: 260px;">
                    Автоматизируйте работу, привлекайте клиентов и управляйте бизнесом эффективно
                </p>
                <a href="#" style="margin-top: 16px; padding: 10px 28px; background: linear-gradient(135deg, #4F46E5, #7C3AED); color: white; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.3s;">
                    Все типы бизнеса →
                </a>
            </div>

        </div>
    </section>

    <!-- ОТЗЫВЫ                                                    -->
    <section style="padding: 40px 0;">
        <div style="text-align: center; margin-bottom: 36px;">
            <h2 style="font-size: 38px; font-weight: 800; color: #1f2937;">Более 55 000 компаний доверяют нам</h2>
            <p style="color: #6b7280; font-size: 17px; margin-top: 4px;">Реальные результаты наших клиентов</p>
        </div>

        <div class="scrollbooster-viewport" style="overflow: auto; cursor: grab; padding: 8px 4px 16px;">
            <div class="scrollbooster-content" style="display: flex; gap: 20px;">

                <div class="review-card">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                        <div class="review-avatar" style="background: linear-gradient(135deg, #EEF2FF, #EDE9FE); color: #4F46E5;">ТШ</div>
                        <div>
                            <div style="font-weight: 600; color: #1f2937; font-size: 15px;">Татьяна Шутова</div>
                            <div style="font-size: 12px; color: #6b7280;">Основатель сети 4hands</div>
                        </div>
                    </div>
                    <p style="color: #374151; font-size: 14px; line-height: 1.6;">
                        "Управляем 200+ салонами. За год увеличили выручку в 10 раз, средний чек в 3 раза."
                    </p>
                    <div style="margin-top: 10px; color: #f59e0b; font-size: 13px; letter-spacing: 2px;">★★★★★</div>
                </div>

                <div class="review-card">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                        <div class="review-avatar" style="background: linear-gradient(135deg, #ECFDF5, #D1FAE5); color: #059669;">АБ</div>
                        <div>
                            <div style="font-weight: 600; color: #1f2937; font-size: 15px;">Армен Багдасарян</div>
                            <div style="font-size: 12px; color: #6b7280;">Предприниматель</div>
                        </div>
                    </div>
                    <p style="color: #374151; font-size: 14px; line-height: 1.6;">
                        "Масштабировали клинику. Экономим от 100 000 ₽ по платежам через СБП."
                    </p>
                    <div style="margin-top: 10px; color: #f59e0b; font-size: 13px; letter-spacing: 2px;">★★★★★</div>
                </div>

                <div class="review-card">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                        <div class="review-avatar" style="background: linear-gradient(135deg, #FFFBEB, #FEF3C7); color: #D97706;">АЯ</div>
                        <div>
                            <div style="font-weight: 600; color: #1f2937; font-size: 15px;">Анастасия Якушева</div>
                            <div style="font-size: 12px; color: #6b7280;">Сооснователь Lay Back</div>
                        </div>
                    </div>
                    <p style="color: #374151; font-size: 14px; line-height: 1.6;">
                        "Освободили 3+ часов в неделю. Сократили издержки на 15%."
                    </p>
                    <div style="margin-top: 10px; color: #f59e0b; font-size: 13px; letter-spacing: 2px;">★★★★★</div>
                </div>

                <div class="review-card">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                        <div class="review-avatar" style="background: linear-gradient(135deg, #EFF6FF, #DBEAFE); color: #2563EB;">ДР</div>
                        <div>
                            <div style="font-weight: 600; color: #1f2937; font-size: 15px;">Данил Рындевич</div>
                            <div style="font-size: 12px; color: #6b7280;">Сооснователь «Блэкгрумер»</div>
                        </div>
                    </div>
                    <p style="color: #374151; font-size: 14px; line-height: 1.6;">
                        "Выросли до 20 точек. Экономим от 13 000 ₽ на СМС в каждом филиале."
                    </p>
                    <div style="margin-top: 10px; color: #f59e0b; font-size: 13px; letter-spacing: 2px;">★★★★★</div>
                </div>

            </div>
        </div>

        <div style="display: flex; justify-content: center; gap: 10px; margin-top: 12px;">
            <button class="arrow-left">←</button>
            <button class="arrow-right">→</button>
        </div>
    </section>

    <!-- CTA                                                       -->
    <section style="padding: 30px 0 40px;">
        <div class="cta-card">
            <div class="cta-blob cta-blob-1"></div>
            <div class="cta-blob cta-blob-2"></div>

            <div class="cta-content">
                <h2>Увеличьте доход уже сейчас</h2>
                <p>Освободите свое время от рутины, сотрудников — от дополнительных задач, а бизнес — от ошибок</p>
                <a href="{{ route('register') }}" class="btn-cta">
                    Попробовать 7 дней бесплатно
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- ТАРИФЫ -->
    <section id="pricing" style="padding: 20px 0 40px;">
        <div style="text-align: center; margin-bottom: 36px;">
            <h2 style="font-size: 38px; font-weight: 800; color: #1f2937;">Выберите тариф</h2>
            <p style="color: #6b7280; font-size: 17px; margin-top: 6px;">Подберите оптимальный план для вашего бизнеса</p>
        </div>

        <div style="margin-bottom: 32px;">
            <div style="text-align: center; margin-bottom: 12px;">
                <h3 style="font-size: 16px; font-weight: 600; color: #1f2937;">Количество сотрудников</h3>
                <p style="color: #6b7280; font-size: 13px;">Увеличить число сотрудников можно в любой момент</p>
            </div>
            <div class="tabs-container">
                <button class="tab-btn active" data-employees="3">До 3</button>
                <button class="tab-btn" data-employees="4">4</button>
                <button class="tab-btn" data-employees="5">5</button>
                <button class="tab-btn" data-employees="6">6</button>
                <button class="tab-btn" data-employees="7">7</button>
                <button class="tab-btn" data-employees="8">8</button>
                <button class="tab-btn" data-employees="9">9</button>
                <button class="tab-btn" data-employees="10">10</button>
                <button class="tab-btn" data-employees="11">11</button>
                <button class="tab-btn" data-employees="12">12</button>
                <button class="tab-btn" data-employees="13">13</button>
                <button class="tab-btn" data-employees="14">14</button>
                <button class="tab-btn" data-employees="15">15</button>
                <button class="tab-btn" data-employees="16">16 и более</button>
            </div>
        </div>

        <div class="pricing-grid">
            <!-- Тариф 1 -->
            <div class="pricing-card popular">
                <div style="text-align: center;">
                    <p style="font-weight: 500; color: #6b7280; font-size: 13px;">1 год</p>
                    <div style="margin-top: 10px;">
                        <span class="pricing-price">890</span>
                        <span style="font-size: 18px; font-weight: 600; color: #1f2937;">₽</span>
                    </div>
                    <p style="color: #6b7280; font-size: 13px;">в месяц</p>
                    <p style="color: #9ca3af; font-size: 12px; margin-top: 2px;">от 10 680 ₽ за 1 год</p>
                </div>

                <ul style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px; list-style: none; padding: 0;">
                    <li class="pricing-feature">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        Все функции платформы
                    </li>
                    <li class="pricing-feature">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        Безлимитные записи
                    </li>
                    <li class="pricing-feature">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        SMS уведомления
                    </li>
                    <li class="pricing-feature">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        Поддержка 24/7
                    </li>
                </ul>

                <div style="margin-top: 24px;">
                    <a href="{{ route('register') }}" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px; font-size: 14px;">
                        Попробовать бесплатно
                    </a>
                </div>
            </div>

            <!-- Тариф 2 -->
            <div class="pricing-card">
                <div style="text-align: center;">
                    <p style="font-weight: 500; color: #6b7280; font-size: 13px;">8 месяцев</p>
                    <div style="margin-top: 10px;">
                        <span class="pricing-price">1 190</span>
                        <span style="font-size: 18px; font-weight: 600; color: #1f2937;">₽</span>
                    </div>
                    <p style="color: #6b7280; font-size: 13px;">в месяц</p>
                    <p style="color: #9ca3af; font-size: 12px; margin-top: 2px;">от 9 520 ₽ за 8 мес.</p>
                </div>

                <ul style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px; list-style: none; padding: 0;">
                    <li class="pricing-feature">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        Все функции платформы
                    </li>
                    <li class="pricing-feature">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        Безлимитные записи
                    </li>
                    <li class="pricing-feature">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        SMS уведомления
                    </li>
                </ul>

                <div style="margin-top: 24px;">
                    <a href="{{ route('register') }}" class="btn-secondary" style="width: 100%; justify-content: center; padding: 12px; font-size: 14px;">
                        Попробовать бесплатно
                    </a>
                </div>
            </div>

            <!-- Тариф 3 -->
            <div class="pricing-card">
                <div style="text-align: center;">
                    <p style="font-weight: 500; color: #6b7280; font-size: 13px;">4 месяца</p>
                    <div style="margin-top: 10px;">
                        <span class="pricing-price">1 590</span>
                        <span style="font-size: 18px; font-weight: 600; color: #1f2937;">₽</span>
                    </div>
                    <p style="color: #6b7280; font-size: 13px;">в месяц</p>
                    <p style="color: #9ca3af; font-size: 12px; margin-top: 2px;">от 6 360 ₽ за 4 мес.</p>
                </div>

                <ul style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px; list-style: none; padding: 0;">
                    <li class="pricing-feature">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        Все функции платформы
                    </li>
                    <li class="pricing-feature">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        Безлимитные записи
                    </li>
                </ul>

                <div style="margin-top: 24px;">
                    <a href="{{ route('register') }}" class="btn-secondary" style="width: 100%; justify-content: center; padding: 12px; font-size: 14px;">
                        Попробовать бесплатно
                    </a>
                </div>
            </div>
        </div>

        <!-- Для компаний -->
        <div class="glass-card" style="border-radius: 20px; padding: 28px; text-align: center; margin-top: 28px;">
            <h3 style="font-size: 18px; font-weight: 700; color: #1f2937;">Оставьте заявку для персонального расчета</h3>
            <p style="color: #6b7280; font-size: 14px; max-width: 540px; margin: 6px auto 0;">
                Для компаний на 16+ сотрудников и сетей стоимость рассчитывается персонально.
            </p>
            <button style="margin-top: 14px; padding: 10px 28px; border-radius: 12px; background: var(--primary-light); color: var(--primary-start); font-weight: 600; border: none; cursor: pointer; transition: all 0.3s;">
                Получить расчет
            </button>
        </div>

        <!-- Баннер для сетей -->
        <div style="margin-top: 14px; padding: 20px 28px; border-radius: 20px; background: linear-gradient(135deg, #4F46E5, #7C3AED); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
            <div>
                <p style="font-weight: 600; color: white; font-size: 15px;">Для сетей действуют специальные условия.</p>
                <p style="color: rgba(255,255,255,0.7); font-size: 13px;">Свяжитесь с нами для консультации</p>
            </div>
            <button style="padding: 10px 24px; border-radius: 12px; background: rgba(255,255,255,0.12); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2); color: white; font-weight: 500; cursor: pointer; transition: all 0.3s;">
                Оставить заявку →
            </button>
        </div>
    </section>



</main>

<!-- ========================================================= -->
<!-- ФУТЕР                                                     -->
<!-- ========================================================= -->
<footer class="footer">
    <div class="container">
        <div class="footer-inner">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 34px; height: 34px; border-radius: 10px; background: linear-gradient(135deg, #4F46E5, #7C3AED); display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-weight: 900; font-size: 15px;">З</span>
                </div>
                <span style="font-size: 17px; font-weight: 800; background: linear-gradient(135deg, #4F46E5, #7C3AED); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Запишись</span>
                <span style="color: #9ca3af; font-size: 12px;">© 2026</span>
            </div>

            <div class="footer-links">
                <a href="#">Политика конфиденциальности</a>
                <a href="#">Пользовательское соглашение</a>
                <a href="#">Контакты</a>
            </div>
        </div>
    </div>
</footer>

@vite(['resources/js/app.js'])

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // === Табы тарифов ===
        const tabs = document.querySelectorAll('.tab-btn');
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // === Слайдер в герое ===
        const slides = document.querySelectorAll('.hero-slider .slide');
        const dots = document.querySelectorAll('.hero-slider .dot');
        let currentSlide = 0;
        let slideInterval;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === index);
            });
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
            currentSlide = index;
        }

        function nextSlide() {
            showSlide((currentSlide + 1) % slides.length);
        }

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                clearInterval(slideInterval);
                showSlide(index);
                slideInterval = setInterval(nextSlide, 5000);
            });
        });

        slideInterval = setInterval(nextSlide, 5000);

        // === Мобильное меню ===
        const burgerIcon = document.querySelector('.header__burger-icon');
        const burgerMenu = document.querySelector('.header-burger');
        const burgerClose = document.querySelector('.header-burger__close');
        const overlay = document.querySelector('.header__overlay');
        const header = document.querySelector('.header');

        if (burgerIcon && burgerMenu && burgerClose && overlay && header) {
            const openMenu = () => {
                burgerMenu.style.transform = 'translateX(0)';
                overlay.style.display = 'block';
                header.classList.add('is-menu-open');
                document.body.style.overflow = 'hidden';
            };

            const closeMenu = () => {
                burgerMenu.style.transform = 'translateX(100%)';
                overlay.style.display = 'none';
                header.classList.remove('is-menu-open');
                document.body.style.overflow = '';
            };

            burgerIcon.addEventListener('click', openMenu);
            burgerClose.addEventListener('click', closeMenu);
            overlay.addEventListener('click', closeMenu);
        }

        // === NPS Tooltip ===
        const npsTrigger = document.querySelector('[style*="cursor: help;"]');
        if (npsTrigger) {
            const popup = npsTrigger.querySelector('div:last-child');
            if (popup) {
                npsTrigger.addEventListener('mouseenter', () => popup.style.display = 'block');
                npsTrigger.addEventListener('mouseleave', () => popup.style.display = 'none');
            }
        }

        // === Карусель отзывов ===
        const viewport = document.querySelector('.scrollbooster-viewport');
        const leftArrow = document.querySelector('.arrow-left');
        const rightArrow = document.querySelector('.arrow-right');

        if (viewport && leftArrow && rightArrow) {
            const scrollAmount = 320;

            leftArrow.addEventListener('click', () => {
                viewport.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });

            rightArrow.addEventListener('click', () => {
                viewport.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });

            let isDown = false;
            let startX;
            let scrollLeft;

            viewport.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - viewport.offsetLeft;
                scrollLeft = viewport.scrollLeft;
                viewport.style.cursor = 'grabbing';
            });

            viewport.addEventListener('mouseleave', () => {
                isDown = false;
                viewport.style.cursor = 'grab';
            });

            viewport.addEventListener('mouseup', () => {
                isDown = false;
                viewport.style.cursor = 'grab';
            });

            viewport.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - viewport.offsetLeft;
                const walk = (x - startX) * 1.5;
                viewport.scrollLeft = scrollLeft - walk;
            });
        }

    });
</script>

</body>
</html>
