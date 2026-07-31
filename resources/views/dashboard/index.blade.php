@extends('layouts.app')

@section('title', 'Панель управления')

@section('content')
    <div class="space-y-6">
        <!-- Приветствие -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900">
                        Добро пожаловать, {{ auth()->user()->name }}! 👋
                    </h1>
                    <p class="text-gray-500 mt-1">Вот что происходит в вашем бизнесе сегодня</p>
                </div>
                <a href="{{ route('appointments.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02] whitespace-nowrap">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Новая запись
                </a>
            </div>
        </div>

        <!-- Статистика -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-6 border border-white/50 shadow-xl shadow-gray-100/50 hover:shadow-2xl hover:shadow-indigo-100/30 transition-all group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Всего записей</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $stats['total_appointments'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-2xl flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-6 border border-white/50 shadow-xl shadow-gray-100/50 hover:shadow-2xl hover:shadow-emerald-100/30 transition-all group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Выручка</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['revenue'] ?? 0, 0) }} ₽</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-2xl flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0-1V5m0 5v1"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-6 border border-white/50 shadow-xl shadow-gray-100/50 hover:shadow-2xl hover:shadow-blue-100/30 transition-all group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Клиенты</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $stats['total_clients'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-2xl flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-6 border border-white/50 shadow-xl shadow-gray-100/50 hover:shadow-2xl hover:shadow-purple-100/30 transition-all group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Сотрудники</p>
                        <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $stats['total_employees'] ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500/20 to-cyan-500/20 rounded-2xl flex items-center justify-center group-hover:scale-110 transition">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Список записей -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">📅 Записи на сегодня</h3>
                <a href="{{ route('appointments.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition">Все записи →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <th class="pb-3">Время</th>
                        <th class="pb-3">Клиент</th>
                        <th class="pb-3">Услуга</th>
                        <th class="pb-3">Сотрудник</th>
                        <th class="pb-3">Статус</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($todayAppointments ?? [] as $appointment)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3 text-sm font-medium text-gray-900">{{ $appointment->start_time->format('H:i') }}</td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center text-xs font-bold text-indigo-700">
                                        {{ $appointment->client->initials ?? '?' }}
                                    </div>
                                    <span class="text-sm text-gray-700">{{ $appointment->client->first_name ?? '' }} {{ $appointment->client->last_name ?? '' }}</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-gray-600">{{ $appointment->service->name ?? '' }}</td>
                            <td class="py-3 text-sm text-gray-600">{{ $appointment->employee->name ?? '' }}</td>
                            <td class="py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    @if($appointment->status === 'confirmed') bg-emerald-100 text-emerald-700
                                    @elseif($appointment->status === 'pending') bg-amber-100 text-amber-700
                                    @elseif($appointment->status === 'completed') bg-blue-100 text-blue-700
                                    @elseif($appointment->status === 'cancelled') bg-rose-100 text-rose-700
                                    @else bg-gray-100 text-gray-600
                                    @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 text-sm">
                                Нет записей на сегодня 🎉
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(!isset($stats))
            <!-- Если нет бизнеса -->
            <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-12 text-center border border-white/50 shadow-xl shadow-gray-100/50">
                <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">У вас пока нет бизнеса</h3>
                <p class="text-gray-500 mb-6">Создайте свой первый бизнес и начните принимать записи</p>
                <a href="{{ route('businesses.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Создать бизнес
                </a>
            </div>
        @endif
    </div>
@endsection
