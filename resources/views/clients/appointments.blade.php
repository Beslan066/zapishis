@extends('layouts.app')

@section('title', 'Мои записи')

@section('content')
    <div class="space-y-6">
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                        <span class="text-3xl">📅</span>
                        Мои записи
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">Все ваши записи в одном месте</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('client.dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100/80 transition">
                        ← Назад
                    </a>
                    <a href="{{ route('public.companies') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Новая запись
                    </a>
                </div>
            </div>
        </div>

        <!-- Фильтры -->
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('client.appointments') }}" class="px-4 py-2 rounded-xl text-sm font-medium {{ !request('status') ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100/80 text-gray-600 hover:bg-gray-200/80' }} transition">
                Все
            </a>
            <a href="{{ route('client.appointments', ['status' => 'pending']) }}" class="px-4 py-2 rounded-xl text-sm font-medium {{ request('status') == 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100/80 text-gray-600 hover:bg-gray-200/80' }} transition">
                Ожидают
            </a>
            <a href="{{ route('client.appointments', ['status' => 'confirmed']) }}" class="px-4 py-2 rounded-xl text-sm font-medium {{ request('status') == 'confirmed' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100/80 text-gray-600 hover:bg-gray-200/80' }} transition">
                Подтверждены
            </a>
            <a href="{{ route('client.appointments', ['status' => 'completed']) }}" class="px-4 py-2 rounded-xl text-sm font-medium {{ request('status') == 'completed' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100/80 text-gray-600 hover:bg-gray-200/80' }} transition">
                Завершены
            </a>
            <a href="{{ route('client.appointments', ['status' => 'cancelled']) }}" class="px-4 py-2 rounded-xl text-sm font-medium {{ request('status') == 'cancelled' ? 'bg-rose-100 text-rose-700' : 'bg-gray-100/80 text-gray-600 hover:bg-gray-200/80' }} transition">
                Отменены
            </a>
        </div>

        <!-- Список записей -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            @if($appointments->count() > 0)
                <div class="space-y-3">
                    @foreach($appointments as $appointment)
                        <div class="flex items-center justify-between p-4 bg-gray-50/50 rounded-2xl hover:bg-gray-50 transition">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <span class="text-xl font-bold text-indigo-600">
                                    {{ strtoupper(substr($appointment->business->name ?? '', 0, 1)) }}
                                </span>
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
                                        <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0-1V5m0 5v1"/></svg>
                                        {{ number_format($appointment->price, 0) }} ₽
                                    </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if($appointment->status === 'confirmed') bg-emerald-100 text-emerald-700
                                @elseif($appointment->status === 'pending') bg-amber-100 text-amber-700
                                @elseif($appointment->status === 'completed') bg-blue-100 text-blue-700
                                @elseif($appointment->status === 'cancelled') bg-rose-100 text-rose-700
                                @else bg-gray-100 text-gray-600
                                @endif">
                                {{ $appointment->status_label }}
                            </span>
                                <a href="{{ route('client.appointments.show', $appointment) }}" class="p-2 rounded-xl text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
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

                <div class="mt-6">
                    {{ $appointments->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Нет записей</h3>
                    <p class="text-gray-500 mb-6">У вас пока нет записей</p>
                    <a href="{{ route('public.companies') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Найти компанию
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
