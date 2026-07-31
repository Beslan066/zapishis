@extends('layouts.app')

@section('title', 'Редактировать запись')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900">Редактировать запись</h1>
                    <p class="text-gray-500 text-sm">Измените детали записи</p>
                </div>
            </div>

            <form action="{{ route('appointments.update', $appointment) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <!-- Client -->
                    <div>
                        <label for="client_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Клиент *</label>
                        <select id="client_id" name="client_id" required
                                class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none appearance-none">
                            <option value="">Выберите клиента</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $appointment->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->full_name }} ({{ $client->phone }})
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="employee_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Сотрудник *</label>
                            <select id="employee_id" name="employee_id" required
                                    class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none appearance-none">
                                <option value="">Выберите сотрудника</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id', $appointment->employee_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="service_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Услуга *</label>
                            <select id="service_id" name="service_id" required
                                    class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none appearance-none">
                                <option value="">Выберите услугу</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}"
                                            data-duration="{{ $service->duration_minutes }}"
                                            data-price="{{ $service->price }}"
                                        {{ old('service_id', $appointment->service_id) == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }} ({{ $service->duration_minutes }} мин, {{ number_format($service->price, 0, '.', ' ') }} ₽)
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="date" class="block text-sm font-semibold text-gray-700 mb-1.5">Дата *</label>
                            <input type="date" id="date" name="date" required
                                   min="{{ date('Y-m-d') }}"
                                   value="{{ old('date', $appointment->start_time->format('Y-m-d')) }}"
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none">
                            @error('date')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="time" class="block text-sm font-semibold text-gray-700 mb-1.5">Время *</label>
                            <input type="time" id="time" name="time" required
                                   step="900"
                                   value="{{ old('time', $appointment->start_time->format('H:i')) }}"
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none">
                            @error('time')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-1.5">Примечания</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none resize-none"
                                  placeholder="Дополнительная информация...">{{ old('notes', $appointment->notes) }}</textarea>
                        @error('notes')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100/80">
                    <a href="{{ route('appointments.index') }}" class="px-6 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100/80 transition">
                        Отмена
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
