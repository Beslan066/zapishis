@extends('layouts.app')

@section('title', 'Мой кабинет')

@section('content')
    <div class="space-y-6">
        <!-- Приветствие -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                        👋 Добро пожаловать, {{ auth()->user()->name }}
                    </h1>
                    <p class="text-gray-500 mt-1">Вот ваши последние записи и активность</p>
                </div>
                <a href="{{ route('public.companies') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Найти компанию
                </a>
            </div>
        </div>

        <!-- Статистика -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-5 border border-white/50 shadow-lg shadow-gray-100/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Всего записей</p>
                        <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-5 border border-white/50 shadow-lg shadow-gray-100/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Предстоящие</p>
                        <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['upcoming'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-5 border border-white/50 shadow-lg shadow-gray-100/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Завершенные</p>
                        <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['completed'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500/20 to-cyan-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-5 border border-white/50 shadow-lg shadow-gray-100/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Компании</p>
                        <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['companies'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Предстоящие записи -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">📅 Предстоящие записи</h3>
                <a href="{{ route('client.appointments') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">Все записи →</a>
            </div>

            @if($upcomingAppointments->count() > 0)
                <div class="space-y-3">
                    @foreach($upcomingAppointments as $appointment)
                        <div class="flex items-center justify-between p-4 bg-gray-50/50 rounded-2xl hover:bg-gray-50 transition">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <span class="text-xl">{{ strtoupper(substr($appointment->business->name ?? '', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $appointment->service->name ?? 'Услуга' }}</p>
                                    <p class="text-sm text-gray-500">{{ $appointment->business->name ?? '' }}</p>
                                    <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $appointment->start_time->format('d.m.Y H:i') }}
                                    </span>
                                        <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ $appointment->employee->name ?? '' }}
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if($appointment->status === 'confirmed') bg-emerald-100 text-emerald-700
                                @elseif($appointment->status === 'pending') bg-amber-100 text-amber-700
                                @elseif($appointment->status === 'completed') bg-blue-100 text-blue-700
                                @else bg-gray-100 text-gray-600
                                @endif">
                                {{ $appointment->status_label }}
                            </span>
                                @if($appointment->canBeCancelled())
                                    <form action="{{ route('client.appointments.cancel', $appointment) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 rounded-xl text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Отменить" onclick="return confirm('Отменить запись?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-400">У вас нет предстоящих записей</p>
                    <a href="{{ route('public.companies') }}" class="inline-block mt-4 text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">
                        Найти компанию →
                    </a>
                </div>
            @endif
        </div>

        <!-- История записей -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">📋 История записей</h3>
                <a href="{{ route('client.history') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">Вся история →</a>
            </div>

            @if($historyAppointments->count() > 0)
                <div class="space-y-2">
                    @foreach($historyAppointments as $appointment)
                        <div class="flex items-center justify-between p-3 bg-gray-50/50 rounded-xl hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-gray-500">{{ strtoupper(substr($appointment->business->name ?? '', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">{{ $appointment->service->name ?? 'Услуга' }}</p>
                                    <p class="text-xs text-gray-400">{{ $appointment->start_time->format('d.m.Y H:i') }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                            {{ $appointment->status_label }}
                        </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-400 py-4">История записей пуста</p>
            @endif
        </div>
    </div>
@endsection
