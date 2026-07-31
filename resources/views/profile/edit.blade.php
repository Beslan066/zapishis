@extends('layouts.app')

@section('title', 'Профиль')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                        <span class="text-3xl">👤</span>
                        Профиль
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">Управление личными данными и безопасность</p>
                </div>
                <div class="flex items-center gap-3">
                <span class="px-3 py-1.5 rounded-full text-xs font-medium {{ auth()->user()->hasVerifiedEmail() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ auth()->user()->hasVerifiedEmail() ? '✓ Email подтвержден' : '⚠️ Email не подтвержден' }}
                </span>
                    <span class="px-3 py-1.5 rounded-full text-xs font-medium {{ auth()->user()->hasVerifiedPhone() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ auth()->user()->hasVerifiedPhone() ? '✓ Телефон подтвержден' : '⚠️ Телефон не подтвержден' }}
                </span>
                </div>
            </div>
        </div>

        <!-- Статус верификации -->
        @if(!auth()->user()->hasVerifiedEmail() || !auth()->user()->hasVerifiedPhone())
            <div class="bg-amber-50/80 backdrop-blur-sm border border-amber-200 rounded-3xl p-6 shadow-lg shadow-amber-100/30">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-amber-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900">Завершите регистрацию</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Для полного доступа к платформе необходимо:
                        </p>
                        <ul class="mt-2 space-y-2 text-sm">
                            @if(!auth()->user()->hasVerifiedEmail())
                                <li class="flex items-center gap-2 text-amber-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Подтвердите email
                                    <a href="{{ route('verification.notice') }}" class="text-indigo-600 hover:text-indigo-700 font-medium ml-1">Отправить ссылку</a>
                                </li>
                            @endif
                            @if(!auth()->user()->hasVerifiedPhone())
                                <li class="flex items-center gap-2 text-amber-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Подтвердите номер телефона (через SMS код ниже)
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Информация о пользователе -->
            <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Личные данные
                </h3>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Имя *</label>
                        <input id="name" name="name" type="text" value="{{ old('name', auth()->user()->name) }}" required
                               class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none">
                        @error('name')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <div class="relative">
                            <input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none pr-12">
                            @if(auth()->user()->hasVerifiedEmail())
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-500 text-sm font-medium">✓</span>
                            @endif
                        </div>
                        @error('email')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                        @if(!auth()->user()->hasVerifiedEmail())
                            <p class="mt-1 text-sm text-amber-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Не подтвержден
                                <a href="{{ route('verification.notice') }}" class="text-indigo-600 hover:text-indigo-700 ml-1 font-medium">Подтвердить</a>
                            </p>
                        @endif
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">Телефон</label>
                        <div class="relative">
                            <input id="phone" name="phone" type="tel" value="{{ old('phone', auth()->user()->phone) }}"
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none pr-12">
                            @if(auth()->user()->hasVerifiedPhone())
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-500 text-sm font-medium">✓</span>
                            @endif
                        </div>
                        @error('phone')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                        Сохранить изменения
                    </button>
                </form>
            </div>

            <!-- Безопасность -->
            <div class="space-y-6">
                <!-- Смена пароля -->
                <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Смена пароля
                    </h3>

                    <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="current_password" class="block text-sm font-semibold text-gray-700 mb-1.5">Текущий пароль</label>
                            <input id="current_password" name="current_password" type="password" required
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none">
                            @error('current_password')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Новый пароль</label>
                            <input id="password" name="password" type="password" required
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none">
                            @error('password')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Подтвердите пароль</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none">
                        </div>

                        <button type="submit" class="w-full px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                            Сменить пароль
                        </button>
                    </form>
                </div>

                <!-- Верификация телефона -->
                @if(!auth()->user()->hasVerifiedPhone())
                    <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
                        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Подтверждение телефона
                        </h3>

                        <div class="space-y-4">
                            <p class="text-sm text-gray-500">Для подтверждения номера телефона введите код, который мы отправим вам по SMS</p>

                            <div id="phoneVerification" class="space-y-3">
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <button type="button" id="sendPhoneCode" class="px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl text-sm font-medium hover:shadow-lg hover:shadow-indigo-500/25 transition whitespace-nowrap">
                                        📱 Отправить код
                                    </button>
                                    <div class="flex-1 flex gap-2">
                                        <input type="text" id="phoneCode" placeholder="6-значный код" maxlength="6"
                                               class="flex-1 px-4 py-2.5 bg-gray-50/80 border border-gray-200/80 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none text-center text-lg font-bold tracking-widest">
                                        <button type="button" id="verifyPhoneCode" class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-medium hover:bg-emerald-700 transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                            Проверить
                                        </button>
                                    </div>
                                </div>
                                <div id="phoneStatus" class="text-sm hidden"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Удаление аккаунта -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-rose-200/50 shadow-xl shadow-gray-100/50">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-rose-600 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Удаление аккаунта
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">После удаления аккаунта все данные будут потеряны безвозвратно</p>
                </div>
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-2.5 bg-rose-100 text-rose-700 rounded-2xl font-medium hover:bg-rose-200 transition" onclick="return confirm('Вы уверены? Это действие необратимо!')">
                        Удалить аккаунт
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sendBtn = document.getElementById('sendPhoneCode');
                const codeInput = document.getElementById('phoneCode');
                const verifyBtn = document.getElementById('verifyPhoneCode');
                const statusDiv = document.getElementById('phoneStatus');
                const phoneInput = document.getElementById('phone');

                if (sendBtn) {
                    sendBtn.addEventListener('click', function() {
                        const phone = phoneInput.value.trim();
                        if (!phone) {
                            statusDiv.className = 'mt-2 text-sm text-rose-600';
                            statusDiv.textContent = '⚠️ Введите номер телефона';
                            statusDiv.classList.remove('hidden');
                            return;
                        }

                        statusDiv.className = 'mt-2 text-sm text-amber-600';
                        statusDiv.textContent = '⏳ Отправка кода...';
                        statusDiv.classList.remove('hidden');
                        sendBtn.disabled = true;
                        sendBtn.textContent = '⏳ Отправка...';

                        fetch('{{ route("profile.phone.send-code") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ phone: phone })
                        })
                            .then(response => response.json())
                            .then(data => {
                                sendBtn.disabled = false;
                                sendBtn.textContent = '📱 Отправить код';

                                if (data.success) {
                                    statusDiv.className = 'mt-2 text-sm text-emerald-600';
                                    statusDiv.textContent = '✅ Код отправлен! Проверьте телефон';
                                    verifyBtn.disabled = false;
                                    codeInput.focus();
                                    if (data.code) {
                                        console.log('Ваш код:', data.code);
                                    }
                                    // Авто-отправка через 60 секунд
                                    let seconds = 60;
                                    const timer = setInterval(() => {
                                        seconds--;
                                        if (seconds <= 0) {
                                            clearInterval(timer);
                                            sendBtn.disabled = false;
                                            sendBtn.textContent = '📱 Отправить код повторно';
                                        } else {
                                            sendBtn.disabled = true;
                                            sendBtn.textContent = `⏳ Повторно через ${seconds}с`;
                                        }
                                    }, 1000);
                                } else {
                                    statusDiv.className = 'mt-2 text-sm text-rose-600';
                                    statusDiv.textContent = data.message || 'Ошибка отправки';
                                    verifyBtn.disabled = true;
                                }
                            })
                            .catch(() => {
                                sendBtn.disabled = false;
                                sendBtn.textContent = '📱 Отправить код';
                                statusDiv.className = 'mt-2 text-sm text-rose-600';
                                statusDiv.textContent = '⚠️ Ошибка сети';
                                verifyBtn.disabled = true;
                            });
                    });

                    verifyBtn.addEventListener('click', function() {
                        const code = codeInput.value.trim();
                        if (code.length !== 6) {
                            statusDiv.className = 'mt-2 text-sm text-rose-600';
                            statusDiv.textContent = '⚠️ Введите 6-значный код';
                            statusDiv.classList.remove('hidden');
                            return;
                        }

                        statusDiv.className = 'mt-2 text-sm text-amber-600';
                        statusDiv.textContent = '⏳ Проверка кода...';
                        statusDiv.classList.remove('hidden');
                        verifyBtn.disabled = true;
                        verifyBtn.textContent = '⏳ Проверка...';

                        const phone = phoneInput.value.trim();
                        fetch('{{ route("profile.phone.verify") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ phone: phone, code: code })
                        })
                            .then(response => response.json())
                            .then(data => {
                                verifyBtn.disabled = false;
                                verifyBtn.textContent = 'Проверить';

                                if (data.success) {
                                    statusDiv.className = 'mt-2 text-sm text-emerald-600';
                                    statusDiv.textContent = '✅ Телефон подтвержден! Страница обновится...';
                                    sendBtn.disabled = true;
                                    sendBtn.textContent = '✅ Подтвержден';
                                    setTimeout(() => location.reload(), 1500);
                                } else {
                                    statusDiv.className = 'mt-2 text-sm text-rose-600';
                                    statusDiv.textContent = '❌ ' + (data.message || 'Неверный код');
                                }
                            })
                            .catch(() => {
                                verifyBtn.disabled = false;
                                verifyBtn.textContent = 'Проверить';
                                statusDiv.className = 'mt-2 text-sm text-rose-600';
                                statusDiv.textContent = '⚠️ Ошибка сети';
                            });
                    });

                    codeInput.addEventListener('input', function() {
                        if (this.value.length === 6) {
                            verifyBtn.click();
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
