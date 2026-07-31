@extends('layouts.guest')

@section('title', 'Регистрация')

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
                <p class="text-gray-500 text-sm mt-2">Создайте аккаунт и подтвердите номер телефона</p>
            </div>

            <!-- Форма регистрации -->
            <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
                <!-- Шаг 1: Форма регистрации -->
                <div id="stepRegister">
                    <form id="registerForm" class="space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Как к вам обращаться? *</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                                   placeholder="Иван Иванов">
                            <div id="nameError" class="mt-1 text-sm text-rose-600 hidden"></div>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">Номер телефона *</label>
                            <div class="relative">
                                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required
                                       class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                                       placeholder="+7 999 123 45 67">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400">SMS</span>
                            </div>
                            <div id="phoneError" class="mt-1 text-sm text-rose-600 hidden"></div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email *</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                                   placeholder="beslan@example.com">
                            <div id="emailError" class="mt-1 text-sm text-rose-600 hidden"></div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Пароль *</label>
                            <div class="relative">
                                <input id="password" type="password" name="password" required
                                       class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                                       placeholder="Минимум 8 символов">
                                <button type="button" id="togglePassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            <div id="passwordError" class="mt-1 text-sm text-rose-600 hidden"></div>
                            <p class="mt-1 text-xs text-gray-400">Пароль должен содержать минимум 8 символов</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Подтвердите пароль *</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                                   placeholder="Повторите пароль">
                            <div id="passwordConfirmationError" class="mt-1 text-sm text-rose-600 hidden"></div>
                        </div>

                        <div class="flex items-start gap-3">
                            <input id="terms" type="checkbox" required
                                   class="mt-0.5 w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <label for="terms" class="text-sm text-gray-500">
                                Я соглашаюсь с
                                <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">условиями использования</a>
                                и
                                <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">политикой конфиденциальности</a>
                            </label>
                        </div>

                        <button type="button" id="registerBtn" class="w-full px-6 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-semibold shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                            Зарегистрироваться
                        </button>

                        <div class="text-center">
                            <p class="text-sm text-gray-500">
                                Уже есть аккаунт?
                                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-medium transition">
                                    Войти
                                </a>
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Шаг 2: Подтверждение SMS -->
                <div id="stepVerify" class="hidden">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Подтвердите номер телефона</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Мы отправили SMS с кодом на номер <br>
                            <strong id="verifyPhoneDisplay" class="text-gray-900">+7 999 123 45 67</strong>
                        </p>
                    </div>

                    <form id="verifyForm" class="space-y-4">
                        @csrf
                        <input type="hidden" id="verifyPhone" name="phone">
                        <input type="hidden" id="verifyName" name="name">
                        <input type="hidden" id="verifyEmail" name="email">
                        <input type="hidden" id="verifyPassword" name="password">

                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Я хочу</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative flex items-center gap-3 p-4 bg-gray-50/80 rounded-2xl border-2 border-transparent hover:border-indigo-300 cursor-pointer transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50">
                                    <input type="radio" name="role" value="client" checked class="w-4 h-4 text-indigo-600">
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm">Записаться</p>
                                        <p class="text-xs text-gray-400">Искать услуги и записываться</p>
                                    </div>
                                </label>
                                <label class="relative flex items-center gap-3 p-4 bg-gray-50/80 rounded-2xl border-2 border-transparent hover:border-indigo-300 cursor-pointer transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50">
                                    <input type="radio" name="role" value="business" class="w-4 h-4 text-indigo-600">
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm">Управлять бизнесом</p>
                                        <p class="text-xs text-gray-400">Принимать записи и управлять</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="sms_code" class="block text-sm font-semibold text-gray-700 mb-1.5">Введите код из SMS</label>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="flex-1">
                                    <input id="sms_code" type="text" maxlength="6" required
                                           class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none text-center text-2xl font-bold tracking-[0.5em]"
                                           placeholder="••••••">
                                </div>
                                <button type="button" id="resendCodeBtn" class="px-4 py-3 text-indigo-600 hover:text-indigo-700 font-medium text-sm whitespace-nowrap transition border border-indigo-200 rounded-2xl hover:bg-indigo-50">
                                    Отправить повторно
                                </button>
                            </div>
                            <div id="smsError" class="mt-1 text-sm text-rose-600 hidden"></div>
                            <div id="smsSuccess" class="mt-1 text-sm text-emerald-600 hidden"></div>
                        </div>

                        <button type="button" id="verifyBtn" class="w-full px-6 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-2xl font-semibold shadow-lg shadow-emerald-500/25 hover:shadow-xl hover:shadow-emerald-500/30 transition-all hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed">
                            Подтвердить и войти
                        </button>

                        <div class="text-center">
                            <button type="button" id="backToRegister" class="text-sm text-gray-500 hover:text-gray-700 transition">
                                ← Вернуться к регистрации
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
                // ============================================
                // ЭЛЕМЕНТЫ
                // ============================================
                const registerBtn = document.getElementById('registerBtn');
                const verifyBtn = document.getElementById('verifyBtn');
                const resendBtn = document.getElementById('resendCodeBtn');
                const backBtn = document.getElementById('backToRegister');
                const stepRegister = document.getElementById('stepRegister');
                const stepVerify = document.getElementById('stepVerify');
                const smsCode = document.getElementById('sms_code');
                const smsError = document.getElementById('smsError');
                const smsSuccess = document.getElementById('smsSuccess');

                // Поля формы
                const nameInput = document.getElementById('name');
                const phoneInput = document.getElementById('phone');
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');
                const passwordConfirmInput = document.getElementById('password_confirmation');
                const termsInput = document.getElementById('terms');

                // Элементы для ошибок
                const nameError = document.getElementById('nameError');
                const phoneError = document.getElementById('phoneError');
                const emailError = document.getElementById('emailError');
                const passwordError = document.getElementById('passwordError');
                const passwordConfirmationError = document.getElementById('passwordConfirmationError');

                let tempUserData = {};
                let resendTimer = null;
                let resendSeconds = 0;

                // ============================================
                // ПОКАЗАТЬ/СКРЫТЬ ПАРОЛЬ
                // ============================================
                document.getElementById('togglePassword')?.addEventListener('click', function() {
                    const password = document.getElementById('password');
                    const type = password.type === 'password' ? 'text' : 'password';
                    password.type = type;
                    this.querySelector('svg').classList.toggle('text-indigo-600');
                });

                // ============================================
                // ШАГ 1: РЕГИСТРАЦИЯ
                // ============================================
                registerBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Скрываем старые ошибки
                    [nameError, phoneError, emailError, passwordError, passwordConfirmationError].forEach(el => {
                        if (el) el.classList.add('hidden');
                    });

                    // Проверка чекбокса
                    if (!termsInput.checked) {
                        alert('Пожалуйста, примите условия использования');
                        return;
                    }

                    const data = {
                        name: nameInput.value.trim(),
                        phone: phoneInput.value.trim(),
                        email: emailInput.value.trim(),
                        password: passwordInput.value,
                        password_confirmation: passwordConfirmInput.value,
                    };

                    // Валидация
                    if (!data.name) {
                        nameError.textContent = 'Введите имя';
                        nameError.classList.remove('hidden');
                        return;
                    }
                    if (!data.phone) {
                        phoneError.textContent = 'Введите номер телефона';
                        phoneError.classList.remove('hidden');
                        return;
                    }
                    if (!data.email) {
                        emailError.textContent = 'Введите email';
                        emailError.classList.remove('hidden');
                        return;
                    }
                    if (data.password.length < 8) {
                        passwordError.textContent = 'Пароль должен содержать минимум 8 символов';
                        passwordError.classList.remove('hidden');
                        return;
                    }
                    if (data.password !== data.password_confirmation) {
                        passwordConfirmationError.textContent = 'Пароли не совпадают';
                        passwordConfirmationError.classList.remove('hidden');
                        return;
                    }

                    registerBtn.disabled = true;
                    registerBtn.textContent = '⏳ Отправка...';

                    fetch('{{ route("register.phone") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(data)
                    })
                        .then(response => response.json())
                        .then(result => {
                            registerBtn.disabled = false;
                            registerBtn.textContent = 'Зарегистрироваться';

                            if (result.success) {
                                tempUserData = data;
                                document.getElementById('verifyPhone').value = data.phone;
                                document.getElementById('verifyName').value = data.name;
                                document.getElementById('verifyEmail').value = data.email;
                                document.getElementById('verifyPassword').value = data.password;
                                document.getElementById('verifyPhoneDisplay').textContent = data.phone;

                                stepRegister.classList.add('hidden');
                                stepVerify.classList.remove('hidden');
                                setTimeout(() => smsCode.focus(), 300);
                                startResendTimer(60);

                                // Показываем код в консоли для теста
                                if (result.code) {
                                    console.log('📱 Ваш код:', result.code);
                                }
                            } else {
                                if (result.errors) {
                                    for (const [field, messages] of Object.entries(result.errors)) {
                                        const errorEl = document.getElementById(field + 'Error');
                                        if (errorEl) {
                                            errorEl.textContent = messages[0];
                                            errorEl.classList.remove('hidden');
                                        }
                                    }
                                } else {
                                    alert(result.message || 'Ошибка регистрации');
                                }
                            }
                        })
                        .catch(() => {
                            registerBtn.disabled = false;
                            registerBtn.textContent = 'Зарегистрироваться';
                            alert('Ошибка сети. Попробуйте еще раз.');
                        });
                });

                // ============================================
                // ШАГ 2: ПОДТВЕРЖДЕНИЕ SMS
                // ============================================
                verifyBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const code = smsCode.value.trim();

                    if (code.length !== 6) {
                        smsError.textContent = 'Введите 6-значный код';
                        smsError.classList.remove('hidden');
                        return;
                    }

                    verifyBtn.disabled = true;
                    verifyBtn.textContent = '⏳ Проверка...';
                    smsError.classList.add('hidden');
                    smsSuccess.classList.add('hidden');

                    fetch('{{ route("register.verify") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            phone: tempUserData.phone,
                            code: code,
                            name: tempUserData.name,
                            email: tempUserData.email,
                            password: tempUserData.password,
                        })
                    })
                        .then(response => response.json())
                        .then(result => {
                            verifyBtn.disabled = false;
                            verifyBtn.textContent = 'Подтвердить и войти';

                            if (result.success) {
                                smsSuccess.textContent = '✅ Телефон подтвержден! Перенаправление...';
                                smsSuccess.classList.remove('hidden');
                                if (result.redirect) {
                                    window.location.href = result.redirect;
                                }
                            } else {
                                smsError.textContent = result.message || 'Неверный код';
                                smsError.classList.remove('hidden');
                            }
                        })
                        .catch(() => {
                            verifyBtn.disabled = false;
                            verifyBtn.textContent = 'Подтвердить и войти';
                            smsError.textContent = 'Ошибка сети';
                            smsError.classList.remove('hidden');
                        });
                });

                // ============================================
                // ПОВТОРНАЯ ОТПРАВКА КОДА
                // ============================================
                resendBtn.addEventListener('click', function() {
                    if (resendBtn.disabled) return;
                    resendCode();
                });

                function resendCode() {
                    resendBtn.disabled = true;
                    smsError.classList.add('hidden');
                    smsSuccess.classList.add('hidden');

                    fetch('{{ route("register.phone") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            name: tempUserData.name,
                            phone: tempUserData.phone,
                            email: tempUserData.email,
                            password: tempUserData.password,
                            password_confirmation: tempUserData.password,
                        })
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                smsSuccess.textContent = '✅ Код отправлен повторно!';
                                smsSuccess.classList.remove('hidden');
                                startResendTimer(60);
                                if (result.code) {
                                    console.log('📱 Новый код:', result.code);
                                }
                            } else {
                                smsError.textContent = result.message || 'Ошибка отправки';
                                smsError.classList.remove('hidden');
                                resendBtn.disabled = false;
                            }
                        })
                        .catch(() => {
                            smsError.textContent = 'Ошибка сети';
                            smsError.classList.remove('hidden');
                            resendBtn.disabled = false;
                        });
                }

                // ============================================
                // ТАЙМЕР ПОВТОРНОЙ ОТПРАВКИ
                // ============================================
                function startResendTimer(seconds) {
                    resendSeconds = seconds;
                    resendBtn.disabled = true;
                    resendBtn.textContent = `⏳ Повторно через ${seconds}с`;

                    if (resendTimer) clearInterval(resendTimer);

                    resendTimer = setInterval(() => {
                        resendSeconds--;
                        if (resendSeconds <= 0) {
                            clearInterval(resendTimer);
                            resendBtn.disabled = false;
                            resendBtn.textContent = '🔄 Отправить повторно';
                        } else {
                            resendBtn.textContent = `⏳ Повторно через ${resendSeconds}с`;
                        }
                    }, 1000);
                }

                // ============================================
                // АВТО-ОТПРАВКА ПРИ ВВОДЕ 6 ЦИФР
                // ============================================
                smsCode.addEventListener('input', function() {
                    if (this.value.length === 6) {
                        verifyBtn.click();
                    }
                    smsError.classList.add('hidden');
                    smsSuccess.classList.add('hidden');
                });

                // ============================================
                // ВОЗВРАТ К РЕГИСТРАЦИИ
                // ============================================
                backBtn.addEventListener('click', function() {
                    stepVerify.classList.add('hidden');
                    stepRegister.classList.remove('hidden');
                    if (resendTimer) clearInterval(resendTimer);
                    smsCode.value = '';
                });

                // ============================================
                // ENTER КЛАВИША ДЛЯ ФОРМ
                // ============================================
                document.getElementById('registerForm').addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        registerBtn.click();
                    }
                });

                document.getElementById('verifyForm').addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        verifyBtn.click();
                    }
                });
            });
        </script>
    @endpush
@endsection
