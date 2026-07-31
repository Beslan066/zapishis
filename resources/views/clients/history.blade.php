@extends('layouts.app')

@section('title', 'История записей')

@section('content')
    <div class="space-y-6">
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                        <span class="text-3xl">📋</span>
                        История записей
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">Все ваши завершенные и отмененные записи</p>
                </div>
                <a href="{{ route('client.dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100/80 transition">
                    ← Назад
                </a>
            </div>
        </div>

        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            @if($appointments->count() > 0)
                <div class="space-y-3">
                    @foreach($appointments as $appointment)
                        <div class="flex items-center justify-between p-4 bg-gray-50/50 rounded-2xl hover:bg-gray-50 transition">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <span class="text-xl font-bold text-gray-500">
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
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                            @if($appointment->status === 'completed') bg-blue-100 text-blue-700
                            @elseif($appointment->status === 'cancelled') bg-rose-100 text-rose-700
                            @else bg-gray-100 text-gray-600
                            @endif">
                            {{ $appointment->status_label }}
                        </span>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $appointments->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">История пуста</h3>
                    <p class="text-gray-500">У вас пока нет завершенных записей</p>
                </div>
            @endif
        </div>
    </div>
@endsection
