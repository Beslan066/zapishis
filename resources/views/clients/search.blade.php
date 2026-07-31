@extends('layouts.app')

@section('title', 'Поиск услуг')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <h1 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                <span class="text-3xl">🔍</span>
                Поиск услуг
            </h1>
            <p class="text-gray-500 text-sm mt-1">Найдите нужную услугу и запишитесь</p>
        </div>

        <!-- Поисковая форма -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <form action="{{ route('client.search') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Поиск</label>
                    <input type="text" name="q" value="{{ request('q') }}"
                           class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                           placeholder="Название услуги или компании">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Категория</label>
                    <select name="category" class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none appearance-none">
                        <option value="">Все категории</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Регион</label>
                    <select name="region" class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none appearance-none">
                        <option value="">Все регионы</option>
                        @foreach($regions as $reg)
                            <option value="{{ $reg }}" {{ request('region') == $reg ? 'selected' : '' }}>{{ $reg }}</option>
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

        <!-- Результаты -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            @if($services->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($services as $service)
                        <a href="{{ route('public.booking', $service->business->slug) }}"
                           class="group bg-white/50 rounded-2xl p-4 border border-gray-100/80 hover:shadow-lg hover:shadow-indigo-100/30 transition-all hover:scale-[1.02]">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900 group-hover:text-indigo-600 transition">{{ $service->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $service->business->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $service->business->city }}, {{ $service->business->region }}</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                                {{ number_format($service->price, 0) }} ₽
                            </span>
                            </div>
                            <div class="mt-2 flex items-center gap-4 text-xs text-gray-400">
                                <span>⏱ {{ $service->duration_minutes }} мин</span>
                                @if($service->category)
                                    <span>📂 {{ $service->category }}</span>
                                @endif
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
                    {{ $services->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Ничего не найдено</h3>
                    <p class="text-gray-500">Попробуйте изменить параметры поиска</p>
                </div>
            @endif
        </div>
    </div>
@endsection
