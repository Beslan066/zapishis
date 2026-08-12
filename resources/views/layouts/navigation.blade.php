@auth
    <nav class="bg-white/80 backdrop-blur-xl border-b border-gray-200/50 shadow-sm sticky top-0 z-50">
        <div class="max-w-[80%] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <div class="w-9 h-9 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition">
                            <span class="text-white font-extrabold text-sm">З</span>
                        </div>
                        <span class="text-xl font-extrabold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            Запишись
                        </span>
                    </a>

                    <!-- Навигация в зависимости от роли -->
                    @if(auth()->user()->isBusiness())
                        <!-- БИЗНЕС-НАВИГАЦИЯ -->
                        <div class="hidden md:flex items-center gap-1">
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl text-md font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition">
                                Дашборд
                            </a>
                            <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded-xl text-md font-medium {{ request()->routeIs('appointments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition">
                                Записи
                            </a>
                            <a href="{{ route('services.index') }}" class="px-4 py-2 rounded-xl text-md font-medium {{ request()->routeIs('services.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition">
                                Услуги
                            </a>
                            <a href="{{ route('employees.index') }}" class="px-4 py-2 rounded-xl text-md font-medium {{ request()->routeIs('employees.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition">
                                Сотрудники
                            </a>
                            <a href="{{ route('clients.index') }}" class="px-4 py-2 rounded-xl text-md font-medium {{ request()->routeIs('clients.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition">
                                Клиенты
                            </a>
                        </div>
                    @else
                        <!-- КЛИЕНТСКАЯ НАВИГАЦИЯ -->
                        <div class="hidden md:flex items-center gap-1">
                            <a href="{{ route('client.dashboard') }}" class="px-4 py-2 rounded-xl text-md font-medium {{ request()->routeIs('client.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition">
                                Главная
                            </a>
                            <a href="{{ route('client.search') }}" class="px-4 py-2 rounded-xl text-md font-medium {{ request()->routeIs('client.search') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition">
                                Поиск
                            </a>
                            <a href="{{ route('public.companies') }}" class="px-4 py-2 rounded-xl text-md font-medium {{ request()->routeIs('public.companies') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition">
                                Компании
                            </a>
                            <a href="{{ route('client.appointments') }}" class="px-4 py-2 rounded-xl text-md font-medium {{ request()->routeIs('client.appointments') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition">
                                Мои записи
                            </a>
                            <a href="{{ route('client.history') }}" class="px-4 py-2 rounded-xl text-md font-medium {{ request()->routeIs('client.history') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100/80 hover:text-gray-900' }} transition">
                                История
                            </a>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <!-- Уведомления -->
                    <button class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100/80 transition relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- Профиль -->
                    <div class="relative group">
                        <button class="flex items-center gap-2 p-1.5 pr-3 rounded-xl hover:bg-gray-100/80 transition">
                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-lg shadow-indigo-500/25">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden sm:block text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div class="absolute right-0 mt-2 w-56 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-100/50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <div class="p-2">
                                <div class="px-3 py-2 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        @if(auth()->user()->isBusiness())
                                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full text-[10px]">Бизнес</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full text-[10px]">Клиент</span>
                                        @endif
                                    </p>
                                </div>

                                @if(auth()->user()->isBusiness())
                                    <a href="{{ route('businesses.create') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        Мой бизнес
                                    </a>
                                @endif

                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Профиль
                                </a>

                                <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-gray-100 pt-1">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm text-red-600 hover:bg-red-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Выйти
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

@else
    <!-- Навигация для гостей -->
    <nav class="bg-white/80 backdrop-blur-xl border-b border-gray-200/50 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <div class="w-9 h-9 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition">
                        <span class="text-white font-extrabold text-sm">З</span>
                    </div>
                    <span class="text-xl font-extrabold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Запишись
                    </span>
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ route('public.companies') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100/80 transition">
                        🏢 Компании
                    </a>
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100/80 transition">
                        Войти
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:shadow-lg hover:shadow-indigo-500/25 transition shadow-md">
                            Начать
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>
@endauth
