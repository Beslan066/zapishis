@extends('layouts.app')

@section('title', 'Добавить сотрудника')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900">Добавить сотрудника</h1>
            </div>

            <form action="{{ route('employees.store') }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">ФИО *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                               placeholder="Иванов Иван Иванович">
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
                        <label for="position" class="block text-sm font-semibold text-gray-700 mb-1.5">Должность</label>
                        <input type="text" name="position" id="position" value="{{ old('position') }}"
                               class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                               placeholder="Парикмахер">
                        @error('position')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="commission_percent" class="block text-sm font-semibold text-gray-700 mb-1.5">Комиссия (%)</label>
                        <input type="number" name="commission_percent" id="commission_percent" value="{{ old('commission_percent', 0) }}" step="0.01"
                               class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                               placeholder="40">
                        @error('commission_percent')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" checked
                                   class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-700">Активен</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100/80">
                    <a href="{{ route('employees.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100/80 transition">
                        Отмена
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                        Добавить
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
