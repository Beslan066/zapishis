@extends('layouts.guest')

@section('title', 'Восстановление пароля')

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
                <p class="text-gray-500 text-sm mt-2">Восстановление пароля</p>
            </div>

            <!-- Форма -->
            <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
                <div class="mb-6 p-4 bg-blue-50/80 backdrop-blur-sm border border-blue-200 rounded-2xl text-blue-700 flex items-start gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm">Введите ваш email и мы отправим ссылку для сброса пароля</span>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-6 p-4 bg-emerald-50/80 backdrop-blur-sm border border-emerald-200 rounded-2xl text-emerald-700 flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm">{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="w-full pl-12 pr-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                                   placeholder="beslan@example.com">
                        </div>
                        @error('email')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full px-6 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-semibold shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                        Отправить ссылку
                    </button>

                    <div class="text-center mt-4">
                        <p class="text-sm text-gray-500">
                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-medium transition">
                                ← Вернуться к входу
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
