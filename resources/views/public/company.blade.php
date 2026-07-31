@extends('layouts.app')

@section('title', $business->name)

@section('content')
    <div class="max-w-5xl mx-auto">
        <!-- Информация о компании -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl font-bold text-indigo-600">
                        {{ strtoupper(substr($business->name, 0, 1)) }}
                    </span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $business->name }}</h1>
                        @if($business->address)
                            <p class="text-sm text-gray-500">{{ $business->address }}</p>
                        @endif
                        @if($business->phone)
                            <p class="text-sm text-gray-500">📞 {{ $business->phone }}</p>
                        @endif
                        @if($business->email)
                            <p class="text-sm text-gray-500">✉️ {{ $business->email }}</p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('public.booking', $business->slug) }}" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02] whitespace-nowrap">
                    Записаться →
                </a>
            </div>
        </div>

        <!-- Описание -->
        @if($business->description)
            <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50 mb-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">О компании</h3>
                <p class="text-gray-700">{{ $business->description }}</p>
            </div>
        @endif

        <!-- Услуги -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50 mb-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Услуги</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($business->services as $service)
                    <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100/80">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl">💇</span>
                            <span class="text-sm font-bold text-indigo-600">{{ number_format($service->price, 0) }} ₽</span>
                        </div>
                        <p class="font-semibold text-gray-900 mt-2">{{ $service->name }}</p>
                        <p class="text-xs text-gray-500">⏱ {{ $service->duration_minutes }} мин</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Сотрудники -->
        @if($business->employees->count() > 0)
            <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Специалисты</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($business->employees as $employee)
                        <div class="bg-gray-50/80 rounded-2xl p-4 border border-gray-100/80">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center">
                            <span class="text-sm font-bold text-indigo-600">
                                {{ strtoupper(substr($employee->name, 0, 1)) }}
                            </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $employee->name }}</p>
                                    @if($employee->position)
                                        <p class="text-xs text-gray-500">{{ $employee->position }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-6 text-center">
            <a href="{{ route('public.companies') }}" class="text-sm text-gray-400 hover:text-gray-600 transition">
                ← Назад к компаниям
            </a>
        </div>
    </div>
@endsection
