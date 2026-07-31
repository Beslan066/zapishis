@extends('layouts.app')

@section('title', 'Клиенты')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                        <span class="text-3xl">👤</span>
                        Клиенты
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">Управляйте клиентами вашего бизнеса</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('clients.export') }}" class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100/80 transition">
                        📥 Экспорт
                    </a>
                    <a href="{{ route('clients.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Добавить клиента
                    </a>
                </div>
            </div>
        </div>

        <!-- Статистика -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-5 border border-white/50 shadow-lg shadow-gray-100/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Всего клиентов</p>
                        <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500/20 to-purple-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-5 border border-white/50 shadow-lg shadow-gray-100/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Новые (за месяц)</p>
                        <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['new_this_month'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-5 border border-white/50 shadow-lg shadow-gray-100/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Постоянные</p>
                        <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['returning'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500/20 to-cyan-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-xl rounded-2xl p-5 border border-white/50 shadow-lg shadow-gray-100/50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Средний чек</p>
                        <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($stats['average_check'] ?? 0, 0) }} ₽</p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0-1V5m0 5v1"/></svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Топ клиентов -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- По записям -->
            <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    🏆 Топ клиентов по записям
                </h3>
                <div class="space-y-3">
                    @forelse($topByVisits ?? [] as $client)
                        <div class="flex items-center justify-between p-3 bg-gray-50/50 rounded-xl hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center text-xs font-bold text-indigo-700">
                                    {{ $loop->iteration }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $client->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $client->phone }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-indigo-600">{{ $client->appointments_count }} записей</span>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 py-4">Нет данных</p>
                    @endforelse
                </div>
            </div>

            <!-- По тратам -->
            <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    💰 Топ клиентов по тратам
                </h3>
                <div class="space-y-3">
                    @forelse($topBySpent ?? [] as $client)
                        <div class="flex items-center justify-between p-3 bg-gray-50/50 rounded-xl hover:bg-gray-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full flex items-center justify-center text-xs font-bold text-emerald-700">
                                    {{ $loop->iteration }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $client->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $client->phone }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-emerald-600">{{ number_format($client->total_spent ?? 0, 0) }} ₽</span>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 py-4">Нет данных</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Список клиентов -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Все клиенты</h3>
                <div class="flex items-center gap-3">
                    <input type="text" placeholder="Поиск..." class="px-4 py-2 bg-gray-50/80 border border-gray-200/80 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                    <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                        <th class="pb-3">Клиент</th>
                        <th class="pb-3">Контакты</th>
                        <th class="pb-3 text-center">Записи</th>
                        <th class="pb-3 text-right">Траты</th>
                        <th class="pb-3 text-right">Действия</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($clients as $client)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center text-sm font-bold text-indigo-700">
                                        {{ $client->initials }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $client->full_name }}</p>
                                        @if($client->last_visit_at)
                                            <p class="text-xs text-gray-400">Последний визит: {{ $client->last_visit_at->format('d.m.Y') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="space-y-0.5">
                                    @if($client->phone)
                                        <p class="text-sm text-gray-600">{{ $client->phone }}</p>
                                    @endif
                                    @if($client->email)
                                        <p class="text-xs text-gray-400">{{ $client->email }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 text-center">
                                <span class="text-sm font-semibold text-gray-900">{{ $client->appointments_count ?? 0 }}</span>
                            </td>
                            <td class="py-3 text-right">
                                <span class="text-sm font-semibold text-gray-900">{{ number_format($client->total_spent ?? 0, 0) }} ₽</span>
                            </td>
                            <td class="py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('clients.history', $client) }}" class="p-2 rounded-xl text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition" title="История">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </a>
                                    <a href="{{ route('clients.edit', $client) }}" class="p-2 rounded-xl text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Редактировать">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Удалить" onclick="return confirm('Удалить клиента?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-gray-400 text-sm">
                                Нет клиентов
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Пагинация -->
            <div class="mt-6">
                {{ $clients->links() }}
            </div>
        </div>
    </div>
@endsection
