@extends('layouts.app')

@section('title', 'Сотрудники')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                        <span class="text-3xl">👥</span>
                        Сотрудники
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">Управляйте сотрудниками вашего бизнеса</p>
                </div>
                <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Добавить сотрудника
                </a>
            </div>
        </div>

        <!-- Список сотрудников -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            @if($employees->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($employees as $employee)
                        <div class="bg-white/50 backdrop-blur-sm rounded-2xl p-5 border border-gray-100/80 hover:shadow-lg hover:shadow-indigo-100/30 transition-all hover:scale-[1.02] group">
                            <div class="flex items-start gap-4">
                                <!-- Аватар -->
                                <div class="w-14 h-14 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                <span class="text-2xl font-bold text-indigo-600">
                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                </span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="font-semibold text-gray-900 truncate">{{ $employee->name }}</h3>
                                            @if($employee->position)
                                                <p class="text-sm text-gray-500">{{ $employee->position }}</p>
                                            @endif
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full text-xs font-medium flex-shrink-0
                                        @if($employee->is_active) bg-emerald-100 text-emerald-700
                                        @else bg-gray-100 text-gray-500 @endif">
                                        {{ $employee->is_active ? 'Активен' : 'Неактивен' }}
                                    </span>
                                    </div>

                                    <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-gray-500">
                                        @if($employee->phone)
                                            <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                            {{ $employee->phone }}
                                        </span>
                                        @endif
                                        @if($employee->email)
                                            <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            {{ $employee->email }}
                                        </span>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex items-center gap-2">
                                        @if($employee->commission_percent)
                                            <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full">
                                            Комиссия: {{ $employee->commission_percent }}%
                                        </span>
                                        @endif
                                        <span class="text-xs bg-gray-50 text-gray-600 px-2 py-0.5 rounded-full">
                                        {{ $employee->appointments_count ?? 0 }} записей
                                    </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Действия -->
                            <div class="mt-4 pt-4 border-t border-gray-100/80 flex items-center justify-end gap-2">
                                <a href="{{ route('employees.schedule', $employee) }}" class="p-2 rounded-xl text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Расписание">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </a>
                                <a href="{{ route('employees.show', $employee) }}" class="p-2 rounded-xl text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Просмотр">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('employees.edit', $employee) }}" class="p-2 rounded-xl text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Редактировать">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Удалить" onclick="return confirm('Удалить сотрудника?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Пагинация -->
                <div class="mt-6">
                    {{ $employees->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-3xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Нет сотрудников</h3>
                    <p class="text-gray-500 mb-6">Добавьте первого сотрудника в ваш бизнес</p>
                    <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Добавить сотрудника
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
