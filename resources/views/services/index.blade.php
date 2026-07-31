@extends('layouts.app')

@section('title', 'Услуги')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                        <span class="text-3xl">💇</span>
                        Услуги
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">Управляйте услугами вашего бизнеса</p>
                </div>
                <a href="{{ route('services.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Добавить услугу
                </a>
            </div>
        </div>

        <!-- Список услуг -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            @if($services->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100">
                            <th class="pb-3">Название</th>
                            <th class="pb-3">Длительность</th>
                            <th class="pb-3">Цена</th>
                            <th class="pb-3">Статус</th>
                            <th class="pb-3 text-right">Действия</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        @foreach($services as $service)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-3 h-3 rounded-full" style="background: {{ $service->color ?? '#818cf8' }}"></div>
                                        <span class="font-medium text-gray-900">{{ $service->name }}</span>
                                    </div>
                                    @if($service->description)
                                        <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($service->description, 50) }}</p>
                                    @endif
                                </td>
                                <td class="py-3 text-sm text-gray-600">
                                    {{ $service->duration_minutes }} мин
                                </td>
                                <td class="py-3">
                                    <span class="font-semibold text-gray-900">
                                        {{ number_format($service->price, 0, '.', ' ') }} ₽
                                    </span>
                                    @if($service->discount_price)
                                        <span class="text-xs text-gray-400 line-through ml-2">
                                            {{ number_format($service->discount_price, 0, '.', ' ') }} ₽
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium
                                        @if($service->is_active) bg-emerald-100 text-emerald-700
                                        @else bg-gray-100 text-gray-500 @endif">
                                        {{ $service->is_active ? 'Активна' : 'Неактивна' }}
                                    </span>
                                </td>
                                <td class="py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('services.show', $service) }}" class="p-2 rounded-xl text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('services.edit', $service) }}" class="p-2 rounded-xl text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('services.destroy', $service) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl text-gray-400 hover:text-red-600 hover:bg-red-50 transition" onclick="return confirm('Удалить услугу?')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div class="mt-6">
                    {{ $services->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Нет услуг</h3>
                    <p class="text-gray-500 mb-6">Добавьте первую услугу для вашего бизнеса</p>
                    <a href="{{ route('services.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Добавить услугу
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
