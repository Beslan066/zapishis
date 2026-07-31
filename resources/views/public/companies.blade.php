@extends('layouts.app')

@section('title', 'Компании')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                        <span class="text-3xl">🏢</span>
                        Компании
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">Найдите и запишитесь в лучшие компании на Кавказе</p>
                </div>
            </div>
        </div>

        <!-- Фильтры -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <form action="{{ route('public.companies') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Поиск</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                           placeholder="Название или город">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Регион</label>
                    <select name="region" class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none appearance-none">
                        <option value="">Все регионы</option>
                        @foreach($regions ?? [] as $region)
                            <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>{{ $region }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                        Найти
                    </button>
                </div>
            </form>
        </div>

        <!-- Список компаний -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            @if($businesses->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($businesses as $business)
                        <a href="{{ route('public.company', $business->slug) }}"
                           class="group bg-white/50 rounded-2xl p-5 border border-gray-100/80 hover:shadow-lg hover:shadow-indigo-100/30 transition-all hover:scale-[1.02]">
                            <div class="flex items-start gap-4">
                                <div class="w-14 h-14 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <span class="text-2xl font-bold text-indigo-600">
                                    {{ strtoupper(substr($business->name, 0, 1)) }}
                                </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-gray-900 group-hover:text-indigo-600 transition truncate">
                                        {{ $business->name }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        @if($business->city) {{ $business->city }}, @endif
                                        @if($business->region) {{ $business->region }} @endif
                                    </p>
                                    <div class="mt-2 flex items-center gap-3 text-xs text-gray-400">
                                        <span class="flex items-center gap-1">👤 {{ $business->clients_count ?? 0 }}</span>
                                        <span class="flex items-center gap-1">📅 {{ $business->appointments_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100/80">
                            <span class="text-sm font-medium text-indigo-600 group-hover:text-indigo-700 transition">
                                Записаться →
                            </span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $businesses->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Компании не найдены</h3>
                    <p class="text-gray-500">Попробуйте изменить параметры поиска</p>
                </div>
            @endif
        </div>
    </div>
@endsection
