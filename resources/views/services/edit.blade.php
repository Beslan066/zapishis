@extends('layouts.app')

@section('title', 'Редактировать услугу')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900">Редактировать услугу</h1>
            </div>

            <form action="{{ route('services.update', $service) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Название *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $service->name) }}" required
                               class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                               placeholder="Например: Стрижка">
                        @error('name')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">Описание</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none resize-none"
                                  placeholder="Описание услуги...">{{ old('description', $service->description) }}</textarea>
                        @error('description')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="price" class="block text-sm font-semibold text-gray-700 mb-1.5">Цена *</label>
                            <input type="number" name="price" id="price" value="{{ old('price', $service->price) }}" required step="0.01"
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                                   placeholder="1000">
                            @error('price')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 mb-1.5">Длительность (мин) *</label>
                            <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes) }}" required
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                                   placeholder="60">
                            @error('duration_minutes')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                            <span class="text-sm font-medium text-gray-700">Активна</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100/80">
                    <a href="{{ route('services.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100/80 transition">
                        Отмена
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
