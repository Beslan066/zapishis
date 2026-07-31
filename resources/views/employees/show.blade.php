@extends('layouts.app')

@section('title', $employee->name)

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center shadow-sm">
                <span class="text-3xl font-bold text-indigo-600">
                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                </span>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900">{{ $employee->name }}</h1>
                    @if($employee->position)
                        <p class="text-gray-500">{{ $employee->position }}</p>
                    @endif
                </div>
                <div class="ml-auto">
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    @if($employee->is_active) bg-emerald-100 text-emerald-700
                    @else bg-gray-100 text-gray-500 @endif">
                    {{ $employee->is_active ? 'Активен' : 'Неактивен' }}
                </span>
                </div>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    @if($employee->phone)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Телефон</label>
                            <p class="text-gray-900 mt-1">{{ $employee->phone }}</p>
                        </div>
                    @endif
                    @if($employee->email)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900 mt-1">{{ $employee->email }}</p>
                        </div>
                    @endif
                </div>

                @if($employee->commission_percent)
                    <div>
                        <label class="text-sm font-medium text-gray-500">Комиссия</label>
                        <p class="text-gray-900 mt-1">{{ $employee->commission_percent }}%</p>
                    </div>
                @endif

                <div class="pt-4 border-t border-gray-100">
                    <label class="text-sm font-medium text-gray-500">Записей</label>
                    <p class="text-2xl font-bold text-gray-900">{{ $employee->appointments_count ?? 0 }}</p>
                </div>

                @if($employee->created_at)
                    <div class="text-xs text-gray-400 pt-4 border-t border-gray-100">
                        Добавлен: {{ $employee->created_at->format('d.m.Y H:i') }}
                    </div>
                @endif
            </div>

            <div class="flex gap-2 mt-6 pt-6 border-t border-gray-100">
                <a href="{{ route('employees.edit', $employee) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition">
                    Редактировать
                </a>
                <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition">
                    Назад
                </a>
            </div>
        </div>
    </div>
@endsection
