@extends('layouts.app')

@section('title', 'Создать бизнес')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900">Создать бизнес</h1>
            </div>

            <form action="{{ route('businesses.store') }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Название бизнеса *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                               placeholder="Например: Салон красоты «Грация»">
                        @error('name')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">Телефон</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                                   placeholder="+7 999 123 45 67">
                            @error('phone')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                                   placeholder="example@mail.com">
                            @error('email')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-1.5">Адрес</label>
                        <input type="text" name="address" id="address" value="{{ old('address') }}"
                               class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                               placeholder="г. Назрань, ул. Московская, д. 1">
                        @error('address')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-semibold text-gray-700 mb-1.5">Город</label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}"
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                                   placeholder="Назрань">
                            @error('city')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="region" class="block text-sm font-semibold text-gray-700 mb-1.5">Регион</label>
                            <select name="region" id="region"
                                    class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none appearance-none">
                                <option value="">Выберите регион</option>
                                <option value="Ингушетия" {{ old('region') == 'Ингушетия' ? 'selected' : '' }}>🇮🇳 Ингушетия</option>
                                <option value="Чечня" {{ old('region') == 'Чечня' ? 'selected' : '' }}>🇨🇼 Чечня</option>
                                <option value="Дагестан" {{ old('region') == 'Дагестан' ? 'selected' : '' }}>🇩🇬 Дагестан</option>
                                <option value="Северная Осетия" {{ old('region') == 'Северная Осетия' ? 'selected' : '' }}>🇴🇸 Северная Осетия</option>
                                <option value="Кабардино-Балкария" {{ old('region') == 'Кабардино-Балкария' ? 'selected' : '' }}>🇰🇧 Кабардино-Балкария</option>
                                <option value="Карачаево-Черкесия" {{ old('region') == 'Карачаево-Черкесия' ? 'selected' : '' }}>🇰🇨 Карачаево-Черкесия</option>
                                <option value="Адыгея" {{ old('region') == 'Адыгея' ? 'selected' : '' }}>🇦🇩 Адыгея</option>
                                <option value="Краснодарский край" {{ old('region') == 'Краснодарский край' ? 'selected' : '' }}>🇰🇷 Краснодарский край</option>
                                <option value="Ставропольский край" {{ old('region') == 'Ставропольский край' ? 'selected' : '' }}>🇸🇹 Ставропольский край</option>
                            </select>
                            @error('region')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">Описание</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none resize-none"
                                  placeholder="Расскажите о вашем бизнесе...">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100/80">
                    <a href="{{ route('dashboard') }}" class="px-6 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100/80 transition">
                        Отмена
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                        Создать бизнес
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
