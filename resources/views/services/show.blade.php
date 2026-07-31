@extends('layouts.app')

@section('title', $service->name)

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center">
                    <span class="text-2xl">💇</span>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900">{{ $service->name }}</h1>
                    <p class="text-gray-500 text-sm">Информация об услуге</p>
                </div>
                <div class="ml-auto flex gap-2">
                    <a href="{{ route('services.edit', $service) }}" class="p-2 rounded-xl text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <a href="{{ route('services.index') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                </div>
            </div>

            <div class="space-y-4">
                @if($service->description)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Описание</label>
                        <p class="text-gray-900 mt-1">{{ $service->description }}</p>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Цена</label>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($service->price, 0, '.', ' ') }} ₽</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Длительность</label>
                        <p class="text-2xl font-bold text-gray-900">{{ $service->duration_minutes }} мин</p>
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-500">Статус</label>
                    <div class="mt-1">
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($service->is_active) bg-emerald-100 text-emerald-700
                        @else bg-gray-100 text-gray-500 @endif">
                        {{ $service->is_active ? 'Активна' : 'Неактивна' }}
                    </span>
                    </div>
                </div>

                @if($service->created_at)
                    <div class="text-xs text-gray-400 pt-4 border-t border-gray-100">
                        Создана: {{ $service->created_at->format('d.m.Y H:i') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
