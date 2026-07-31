{{-- resources/views/appointments/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Appointments')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-semibold text-gray-900">Appointments</h2>
                <div class="flex space-x-3">
                    <a href="{{ route('appointments.calendar') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                        Calendar View
                    </a>
                    <a href="{{ route('appointments.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        + New Appointment
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="mb-6 flex flex-wrap gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select onchange="window.location.href=this.value" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="{{ route('appointments.index') }}">All</option>
                        <option value="{{ route('appointments.index', ['status' => 'pending']) }}" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="{{ route('appointments.index', ['status' => 'confirmed']) }}" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="{{ route('appointments.index', ['status' => 'completed']) }}" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="{{ route('appointments.index', ['status' => 'cancelled']) }}" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Date</label>
                    <input type="date" name="date" value="{{ request('date') }}"
                           onchange="window.location.href=this.value ? '{{ route('appointments.index') }}?date='+this.value : '{{ route('appointments.index') }}'"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Appointments Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($appointments as $appointment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ $appointment->start_time->format('d.m.Y') }}</div>
                                <div class="text-gray-500">{{ $appointment->start_time->format('H:i') }} - {{ $appointment->end_time->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-600">{{ $appointment->client->initials }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $appointment->client->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $appointment->client->phone }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $appointment->service->name }}</span>
                                <div class="text-xs text-gray-500">{{ $appointment->service->duration_minutes }} min</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if(isset($appointment->employee))
                                    {{ $appointment->employee->name }}
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($appointment->price, 2) }} ₽
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    bg-{{ $appointment->status_color }}-100 text-{{ $appointment->status_color }}-800">
                                    {{ $appointment->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('appointments.show', $appointment) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                @if($appointment->canBeConfirmed())
                                    <form action="{{ route('appointments.confirm', $appointment) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 mr-3">Confirm</button>
                                    </form>
                                @endif
                                @if($appointment->canBeCancelled())
                                    <form action="{{ route('appointments.cancel', $appointment) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Cancel</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                No appointments found
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $appointments->links() }}
            </div>
        </div>
    </div>
@endsection
