@extends('layouts.guest')

@section('title', 'Вход')

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
                <p class="text-gray-500 text-sm mt-2">Вход по номеру телефона</p>
            </div>

            <!-- Форма входа -->
            <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
                <!-- Шаг 1: Ввод номера -->
                <div id="stepPhone">
                    <form id="loginPhoneForm" class="space-y-4">
                        @csrf
                        <div>
                            <label for="login_phone" class="block text-sm font-semibold text-gray-700 mb-1.5">Номер телефона</label>
                            <div class="relative">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <input id="login_phone" type="tel" name="phone" required
                                       class="w-full pl-12 pr-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                                       placeholder="+7 999 123 45 67">
                            </div>
                            <div id="loginError" class="mt-1 text-sm text-rose-600 hidden"></div>
                        </div>

                        <button type="submit" id="sendCodeBtn" class="w-full px-6 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-semibold shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                            Получить код
                        </button>

                        <div class="text-center">
                            <p class="text-sm text-gray-500">
                                Нет аккаунта?
                                <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-700 font-medium transition">
                                    Зарегистрироваться
                                </a>
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Шаг 2: Ввод кода -->
                <div id="stepCode" class="hidden">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Введите код из SMS</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Код отправлен на номер <br>
                            <strong id="verifyPhoneDisplay" class="text-gray-900">+7 999 123 45 67</strong>
                        </p>
                    </div>

                    <form id="loginCodeForm" class="space-y-4">
                        @csrf
                        <input type="hidden" id="login_phone_hidden" name="phone">

                        <div>
                            <label for="login_code" class="block text-sm font-semibold text-gray-700 mb-1.5">Код из SMS</label>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="flex-1">
                                    <input id="login_code" type="text" maxlength="6" required
                                           class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none text-center text-2xl font-bold tracking-[0.5em]"
                                           placeholder="••••••">
                                </div>
                                <button type="button" id="resendCodeBtn" class="px-4 py-3 text-indigo-600 hover:text-indigo-700 font-medium text-sm whitespace-nowrap transition border border-indigo-200 rounded-2xl hover:bg-indigo-50">
                                    Отправить повторно
                                </button>
                            </div>
                            <div id="codeError" class="mt-1 text-sm text-rose-600 hidden"></div>
                            <div id="codeSuccess" class="mt-1 text-sm text-emerald-600 hidden"></div>
                        </div>

                        <button type="submit" id="verifyCodeBtn" class="w-full px-6 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-2xl font-semibold shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 transition-all hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed">
                            Войти
                        </button>

                        <div class="text-center">
                            <button type="button" id="backToPhone" class="text-sm text-gray-500 hover:text-gray-700 transition">
                                ← Изменить номер
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const stepPhone = document.getElementById('stepPhone');
                const stepCode = document.getElementById('stepCode');
                const loginPhone = document.getElementById('login_phone');
                const loginPhoneHidden = document.getElementById('login_phone_hidden');
                const loginCode = document.getElementById('login_code');
                const sendCodeBtn = document.getElementById('sendCodeBtn');
                const verifyCodeBtn = document.getElementById('verifyCodeBtn');
                const resendCodeBtn = document.getElementById('resendCodeBtn');
                const backToPhone = document.getElementById('backToPhone');
                const loginError = document.getElementById('loginError');
                const codeError = document.getElementById('codeError');
                const codeSuccess = document.getElementById('codeSuccess');
                const verifyPhoneDisplay = document.getElementById('verifyPhoneDisplay');

                let resendTimer = null;
                let resendSeconds = 0;
                let currentPhone = '';

                // ============================================
                // ШАГ 1: ОТПРАВКА КОДА
                // ============================================
                document.getElementById('loginPhoneForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const phone = loginPhone.value.trim();
                    if (!phone) {
                        loginError.textContent = 'Введите номер телефона';
                        loginError.classList.remove('hidden');
                        return;
                    }

                    loginError.classList.add('hidden');
                    sendCodeBtn.disabled = true;
                    sendCodeBtn.textContent = '⏳ Отправка...';

                    fetch('{{ route("login.send-code") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ phone: phone })
                    })
                        .then(response => response.json())
                        .then(result => {
                            sendCodeBtn.disabled = false;
                            sendCodeBtn.textContent = 'Получить код';

                            if (result.success) {
                                currentPhone = phone;
                                loginPhoneHidden.value = phone;
                                verifyPhoneDisplay.textContent = phone;

                                stepPhone.classList.add('hidden');
                                stepCode.classList.remove('hidden');
                                setTimeout(() => loginCode.focus(), 300);
                                startResendTimer(60);

                                if (result.code) {
                                    console.log('📱 Ваш код:', result.code);
                                }
                                if (result.message) {
                                    // Показываем сообщение если аккаунт создан
                                    if (result.message.includes('Аккаунт создан')) {
                                        codeSuccess.textContent = '✅ ' + result.message;
                                        codeSuccess.classList.remove('hidden');
                                    }
                                }
                            } else {
                                loginError.textContent = result.message || 'Ошибка отправки кода';
                                loginError.classList.remove('hidden');
                            }
                        })
                        .catch(() => {
                            sendCodeBtn.disabled = false;
                            sendCodeBtn.textContent = 'Получить код';
                            loginError.textContent = 'Ошибка сети. Попробуйте еще раз.';
                            loginError.classList.remove('hidden');
                        });
                });

                // ============================================
                // ШАГ 2: ПРОВЕРКА КОДА
                // ============================================
                document.getElementById('loginCodeForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const code = loginCode.value.trim();
                    if (code.length !== 6) {
                        codeError.textContent = 'Введите 6-значный код';
                        codeError.classList.remove('hidden');
                        return;
                    }

                    codeError.classList.add('hidden');
                    codeSuccess.classList.add('hidden');
                    verifyCodeBtn.disabled = true;
                    verifyCodeBtn.textContent = '⏳ Проверка...';

                    fetch('{{ route("login.verify") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            phone: currentPhone,
                            code: code
                        })
                    })
                        .then(response => response.json())
                        .then(result => {
                            verifyCodeBtn.disabled = false;
                            verifyCodeBtn.textContent = 'Войти';

                            if (result.success) {
                                codeSuccess.textContent = '✅ Успешно! Перенаправление...';
                                codeSuccess.classList.remove('hidden');
                                if (result.redirect) {
                                    setTimeout(() => window.location.href = result.redirect, 500);
                                }
                            } else {
                                codeError.textContent = result.message || 'Неверный код';
                                codeError.classList.remove('hidden');
                            }
                        })
                        .catch(() => {
                            verifyCodeBtn.disabled = false;
                            verifyCodeBtn.textContent = 'Войти';
                            codeError.textContent = 'Ошибка сети. Попробуйте еще раз.';
                            codeError.classList.remove('hidden');
                        });
                });

                // ============================================
                // ПОВТОРНАЯ ОТПРАВКА КОДА
                // ============================================
                resendCodeBtn.addEventListener('click', function() {
                    if (resendCodeBtn.disabled) return;
                    resendCode();
                });

                function resendCode() {
                    resendCodeBtn.disabled = true;
                    codeError.classList.add('hidden');
                    codeSuccess.classList.add('hidden');

                    fetch('{{ route("login.send-code") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ phone: currentPhone })
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                codeSuccess.textContent = '✅ Код отправлен повторно!';
                                codeSuccess.classList.remove('hidden');
                                startResendTimer(60);
                                if (result.code) {
                                    console.log('📱 Новый код:', result.code);
                                }
                            } else {
                                codeError.textContent = result.message || 'Ошибка отправки';
                                codeError.classList.remove('hidden');
                                resendCodeBtn.disabled = false;
                            }
                        })
                        .catch(() => {
                            codeError.textContent = 'Ошибка сети';
                            codeError.classList.remove('hidden');
                            resendCodeBtn.disabled = false;
                        });
                }

                // ============================================
                // ТАЙМЕР
                // ============================================
                function startResendTimer(seconds) {
                    resendSeconds = seconds;
                    resendCodeBtn.disabled = true;
                    resendCodeBtn.textContent = `⏳ Повторно через ${seconds}с`;

                    if (resendTimer) clearInterval(resendTimer);

                    resendTimer = setInterval(() => {
                        resendSeconds--;
                        if (resendSeconds <= 0) {
                            clearInterval(resendTimer);
                            resendCodeBtn.disabled = false;
                            resendCodeBtn.textContent = '🔄 Отправить повторно';
                        } else {
                            resendCodeBtn.textContent = `⏳ Повторно через ${resendSeconds}с`;
                        }
                    }, 1000);
                }

                // ============================================
                // АВТО-ОТПРАВКА ПРИ ВВОДЕ 6 ЦИФР
                // ============================================
                loginCode.addEventListener('input', function() {
                    if (this.value.length === 6) {
                        document.getElementById('loginCodeForm').dispatchEvent(new Event('submit'));
                    }
                    codeError.classList.add('hidden');
                    codeSuccess.classList.add('hidden');
                });

                // ============================================
                // ВОЗВРАТ К НОМЕРУ
                // ============================================
                backToPhone.addEventListener('click', function() {
                    stepCode.classList.add('hidden');
                    stepPhone.classList.remove('hidden');
                    if (resendTimer) clearInterval(resendTimer);
                    loginCode.value = '';
                    loginPhone.value = currentPhone;
                    loginPhone.focus();
                });

                // ============================================
                // ENTER КЛАВИША
                // ============================================
                document.getElementById('loginPhoneForm').addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        sendCodeBtn.click();
                    }
                });

                document.getElementById('loginCodeForm').addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        verifyCodeBtn.click();
                    }
                });
            });
        </script>
    @endpush
@endsection
