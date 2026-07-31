@extends('layouts.guest')

@section('title', 'Подтверждение email')

@section('content')
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Логотип -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center gap-2 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition">
                        <span class="text-white font-extrabold text-xl">З</span>
                    </div>
                    <span class="text-2xl font-extrabold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Запишись
                </span>
                </a>
                <p class="text-gray-500 text-sm mt-2">Подтверждение email</p>
            </div>

            <!-- Карточка -->
            <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
                <!-- Иконка -->
                <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>

                <!-- Текст -->
                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Подтвердите ваш email</h2>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Спасибо за регистрацию! Прежде чем продолжить, пожалуйста, подтвердите ваш email,
                        перейдя по ссылке, которую мы отправили на вашу почту.
                    </p>
                </div>

                <!-- Статус -->
                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 p-4 bg-emerald-50/80 backdrop-blur-sm border border-emerald-200 rounded-2xl text-emerald-700 flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium">Новая ссылка для подтверждения отправлена на ваш email</span>
                    </div>
                @endif

                <!-- Кнопки -->
                <div class="space-y-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-semibold shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                            Отправить ссылку повторно
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full px-6 py-3.5 bg-gray-100/80 text-gray-700 rounded-2xl font-medium hover:bg-gray-200/80 transition">
                            Выйти
                        </button>
                    </form>
                </div>

                <!-- Подсказка -->
                <div class="mt-6 p-4 bg-blue-50/80 backdrop-blur-sm border border-blue-200 rounded-2xl text-blue-700 flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm">Не нашли письмо? Проверьте папку <strong>Спам</strong> или <strong>Промоакции</strong></span>
                </div>
            </div>

            <!-- Преимущества -->
            <div class="mt-6 grid grid-cols-3 gap-3">
                <div class="text-center">
                    <div class="text-2xl mb-1">📧</div>
                    <p class="text-xs text-gray-400">Проверьте почту</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl mb-1">🔒</div>
                    <p class="text-xs text-gray-400">Безопасно</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl mb-1">⚡</div>
                    <p class="text-xs text-gray-400">1 минута</p>
                </div>
            </div>
        </div>
    </div>
@endsection
