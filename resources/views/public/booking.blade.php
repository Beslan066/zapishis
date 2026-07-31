@extends('layouts.app')

@section('title', 'Запись - ' . $business->name)

@section('content')
    <div class="space-y-6">
        <!-- Информация о компании -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl font-bold text-indigo-600">
                        {{ strtoupper(substr($business->name, 0, 1)) }}
                    </span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $business->name }}</h1>
                        @if($business->address)
                            <p class="text-sm text-gray-500">{{ $business->address }}</p>
                        @endif
                    </div>
                </div>
                <a href="{{ route('public.companies') }}" class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100/80 transition">
                    ← Назад
                </a>
            </div>
        </div>

        <!-- Шаг 1: Выбор услуги -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex items-center gap-2 mb-4">
                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold">1</span>
                <h2 class="text-sm font-semibold text-gray-700">Выберите услугу</h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($business->services as $service)
                    <button type="button"
                            class="service-btn p-4 bg-gray-50/80 rounded-2xl border-2 border-transparent hover:border-indigo-300 hover:bg-indigo-50/50 transition-all text-left group"
                            data-id="{{ $service->id }}"
                            data-name="{{ $service->name }}"
                            data-duration="{{ $service->duration_minutes }}"
                            data-price="{{ $service->price }}">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl">💇</span>
                            <span class="text-xs font-medium text-gray-400 group-hover:text-indigo-600 transition">Выбрать →</span>
                        </div>
                        <p class="font-semibold text-gray-900 text-sm mt-2">{{ $service->name }}</p>
                        <p class="text-xs text-gray-500">{{ $service->duration_minutes }} мин · {{ number_format($service->price, 0, '.', ' ') }} ₽</p>
                    </button>
                @endforeach
            </div>
            <div id="selectedServiceInfo" class="hidden mt-4 p-4 bg-indigo-50/80 rounded-2xl border border-indigo-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500">Выбрана услуга</p>
                        <p class="font-semibold text-gray-900" id="selectedServiceName">-</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Стоимость</p>
                        <p class="font-semibold text-gray-900" id="selectedServicePrice">-</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Шаг 2: Выбор специалиста -->
        @if($business->employees->count() > 0)
            <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
                <div class="flex items-center gap-2 mb-4">
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold">2</span>
                    <h2 class="text-sm font-semibold text-gray-700">Выберите специалиста</h2>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button type="button" class="employee-btn px-5 py-2.5 bg-gray-50/80 rounded-2xl border-2 border-indigo-500 text-indigo-700 font-medium transition hover:bg-indigo-50" data-id="">Любой</button>
                    @foreach($business->employees as $employee)
                        <button type="button"
                                class="employee-btn px-5 py-2.5 bg-gray-50/80 rounded-2xl border-2 border-transparent hover:border-indigo-300 hover:bg-indigo-50/50 transition font-medium text-gray-700"
                                data-id="{{ $employee->id }}">
                            {{ $employee->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Шаг 3: Календарь -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full text-xs font-bold">3</span>
                    <h2 class="text-sm font-semibold text-gray-700">Выберите дату и время</h2>
                </div>
                <div id="selectedDateTime" class="text-sm text-gray-400 hidden">
                    Выбрано: <span class="font-medium text-gray-700" id="selectedDateTimeText">-</span>
                </div>
            </div>
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Модальное окно -->
    <div id="bookingModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl animate-in slide-in-from-bottom-4 duration-300">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-extrabold text-gray-900">Подтверждение</h3>
                    <p class="text-sm text-gray-500">Проверьте данные записи</p>
                </div>
                <button type="button" id="closeModalBtn" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-3">
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl">
                    <span class="text-2xl">💇</span>
                    <div>
                        <p class="text-xs text-gray-500">Услуга</p>
                        <p class="font-medium text-gray-900" id="modalService">-</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl">
                    <span class="text-2xl">👤</span>
                    <div>
                        <p class="text-xs text-gray-500">Специалист</p>
                        <p class="font-medium text-gray-900" id="modalEmployee">-</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl">
                    <span class="text-2xl">📅</span>
                    <div>
                        <p class="text-xs text-gray-500">Дата и время</p>
                        <p class="font-medium text-gray-900" id="modalDateTime">-</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-2xl">
                    <span class="text-2xl">💰</span>
                    <div>
                        <p class="text-xs text-gray-500">Стоимость</p>
                        <p class="font-medium text-gray-900" id="modalPrice">-</p>
                    </div>
                </div>
            </div>

            <form id="bookingForm" action="{{ route('public.booking.store', $business->slug) }}" method="POST" class="mt-6 pt-6 border-t border-gray-100">
                @csrf
                <input type="hidden" name="service_id" id="formServiceId">
                <input type="hidden" name="employee_id" id="formEmployeeId">
                <input type="hidden" name="date" id="formDate">
                <input type="hidden" name="time" id="formTime">

                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ваше имя *</label>
                        <input type="text" name="first_name" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                               placeholder="Иван">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Телефон *</label>
                        <input type="tel" name="phone" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                               placeholder="+7 999 123 45 67">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none"
                               placeholder="example@mail.ru">
                    </div>
                </div>

                <button type="submit" class="w-full mt-4 px-6 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-semibold shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                    Подтвердить запись
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
        <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

        <style>
            #calendar {
                background: transparent;
                padding: 4px;
            }
            .fc {
                font-family: 'Inter', sans-serif;
                --fc-border-color: #e5e7eb;
                --fc-today-bg-color: #eef2ff;
            }
            .fc .fc-toolbar-title {
                font-size: 1.1rem !important;
                font-weight: 700 !important;
                color: #111827 !important;
            }
            .fc .fc-button {
                padding: 0.5rem 1rem !important;
                border-radius: 0.75rem !important;
                font-weight: 600 !important;
                font-size: 0.85rem !important;
                text-transform: none !important;
                transition: all 0.2s !important;
                border: 1px solid #e5e7eb !important;
                background: #f9fafb !important;
                color: #374151 !important;
            }
            .fc .fc-button:hover {
                background: #f3f4f6 !important;
                border-color: #d1d5db !important;
                transform: scale(1.02);
            }
            .fc .fc-button-primary:not(:disabled):active,
            .fc .fc-button-primary:not(:disabled).fc-button-active {
                background: #818cf8 !important;
                border-color: #6366f1 !important;
                color: white !important;
            }
            .fc .fc-daygrid-day {
                min-height: 100px !important;
                cursor: pointer !important;
                transition: all 0.15s;
                border-radius: 8px !important;
                border: 2px solid transparent !important;
            }
            .fc .fc-daygrid-day:hover {
                background: #f9fafb !important;
                border-color: #e5e7eb !important;
            }
            .fc .fc-daygrid-day-number {
                font-weight: 500 !important;
                color: #374151 !important;
                padding: 0.5rem !important;
                font-size: 0.9rem !important;
            }
            .fc .fc-daygrid-day.fc-day-today {
                background: #eef2ff !important;
                border-color: #818cf8 !important;
            }
            .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
                color: #4f46e5 !important;
                font-weight: 700 !important;
            }
            .fc .fc-daygrid-day.fc-day-past {
                opacity: 0.4;
                cursor: not-allowed !important;
                background: #f9fafb !important;
            }
            .fc .fc-daygrid-day.fc-day-past:hover {
                background: #f9fafb !important;
                border-color: transparent !important;
            }
            .fc .fc-daygrid-day.fc-day-available {
                background: #f0fdf4 !important;
                border-color: #86efac !important;
            }
            .fc .fc-daygrid-day.fc-day-available:hover {
                background: #dcfce7 !important;
                border-color: #4ade80 !important;
                transform: scale(1.02);
            }
            .fc .fc-daygrid-day.fc-day-available .fc-daygrid-day-number {
                color: #16a34a !important;
                font-weight: 600 !important;
            }
            .fc .fc-daygrid-day.fc-day-partial {
                background: #fffbeb !important;
                border-color: #fcd34d !important;
            }
            .fc .fc-daygrid-day.fc-day-partial .fc-daygrid-day-number {
                color: #d97706 !important;
                font-weight: 600 !important;
            }
            .fc .fc-daygrid-day.fc-day-booked {
                background: #fef2f2 !important;
                border-color: #fca5a5 !important;
                cursor: not-allowed !important;
            }
            .fc .fc-daygrid-day.fc-day-booked .fc-daygrid-day-number {
                color: #dc2626 !important;
                text-decoration: line-through !important;
            }
            .fc .fc-daygrid-day.fc-day-booked:hover {
                background: #fef2f2 !important;
                border-color: #fca5a5 !important;
                transform: none !important;
            }
            .fc .fc-daygrid-day .fc-daygrid-day-bottom {
                text-align: center;
                margin-top: 4px;
            }
            .fc .fc-daygrid-day .fc-daygrid-day-bottom .fc-daygrid-day-status {
                font-size: 0.6rem;
                font-weight: 600;
                padding: 2px 8px;
                border-radius: 20px;
                display: inline-block;
            }
            .fc .fc-daygrid-day.fc-day-available .fc-daygrid-day-bottom .fc-daygrid-day-status {
                background: #86efac;
                color: #166534;
            }
            .fc .fc-daygrid-day.fc-day-partial .fc-daygrid-day-bottom .fc-daygrid-day-status {
                background: #fcd34d;
                color: #92400e;
            }
            .fc .fc-daygrid-day.fc-day-booked .fc-daygrid-day-bottom .fc-daygrid-day-status {
                background: #fca5a5;
                color: #991b1b;
            }
            .fc .fc-daygrid-day.fc-day-selected {
                background: #818cf8 !important;
                border-color: #6366f1 !important;
                transform: scale(1.02);
            }
            .fc .fc-daygrid-day.fc-day-selected .fc-daygrid-day-number {
                color: white !important;
            }
            @media (max-width: 640px) {
                .fc .fc-toolbar {
                    flex-direction: column !important;
                    gap: 0.5rem !important;
                }
                .fc .fc-toolbar-title {
                    font-size: 0.9rem !important;
                }
                .fc .fc-daygrid-day {
                    min-height: 70px !important;
                }
                .fc .fc-daygrid-day-number {
                    font-size: 0.75rem !important;
                    padding: 0.3rem !important;
                }
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const serviceBtns = document.querySelectorAll('.service-btn');
                const employeeBtns = document.querySelectorAll('.employee-btn');
                const selectedServiceInfo = document.getElementById('selectedServiceInfo');
                const selectedServiceName = document.getElementById('selectedServiceName');
                const selectedServicePrice = document.getElementById('selectedServicePrice');
                const selectedDateTime = document.getElementById('selectedDateTime');
                const selectedDateTimeText = document.getElementById('selectedDateTimeText');

                const modal = document.getElementById('bookingModal');
                const closeModalBtn = document.getElementById('closeModalBtn');
                const bookingForm = document.getElementById('bookingForm');

                const modalService = document.getElementById('modalService');
                const modalEmployee = document.getElementById('modalEmployee');
                const modalDateTime = document.getElementById('modalDateTime');
                const modalPrice = document.getElementById('modalPrice');

                const formServiceId = document.getElementById('formServiceId');
                const formEmployeeId = document.getElementById('formEmployeeId');
                const formDate = document.getElementById('formDate');
                const formTime = document.getElementById('formTime');

                let selectedService = null;
                let selectedEmployee = null;
                let selectedDate = null;
                let selectedTime = null;
                let calendar = null;
                let allSlots = {};

                // Выбор услуги
                serviceBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        serviceBtns.forEach(b => {
                            b.classList.remove('border-indigo-500', 'bg-indigo-50/50');
                            b.classList.add('border-transparent');
                        });
                        this.classList.remove('border-transparent');
                        this.classList.add('border-indigo-500', 'bg-indigo-50/50');

                        selectedService = {
                            id: this.dataset.id,
                            name: this.dataset.name,
                            duration: Number(this.dataset.duration),
                            price: Number(this.dataset.price)
                        };

                        selectedServiceInfo.classList.remove('hidden');
                        selectedServiceName.textContent = selectedService.name;
                        selectedServicePrice.textContent = selectedService.price.toLocaleString('ru-RU') + ' ₽';

                        loadSlots();
                    });
                });

                // Выбор специалиста
                employeeBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        employeeBtns.forEach(b => {
                            b.classList.remove('border-indigo-500', 'text-indigo-700', 'bg-indigo-50');
                            b.classList.add('border-transparent');
                        });
                        this.classList.remove('border-transparent');
                        this.classList.add('border-indigo-500', 'text-indigo-700', 'bg-indigo-50');

                        selectedEmployee = this.dataset.id;
                        if (selectedService) {
                            loadSlots();
                        }
                    });
                });

                function loadSlots() {
                    if (!selectedService) {
                        if (calendar) {
                            allSlots = {};
                            calendar.refetchEvents();
                        }
                        return;
                    }

                    const params = new URLSearchParams({
                        service_id: selectedService.id,
                        employee_id: selectedEmployee || ''
                    });

                    fetch(`/api/public/calendar-slots?${params}`)
                        .then(response => response.json())
                        .then(data => {
                            allSlots = data;
                            if (calendar) {
                                calendar.refetchEvents();
                            }
                        })
                        .catch(() => {
                            allSlots = {};
                            if (calendar) {
                                calendar.refetchEvents();
                            }
                        });
                }

                function initCalendar() {
                    const calendarEl = document.getElementById('calendar');

                    calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: ''
                        },
                        locale: 'ru',
                        buttonText: {
                            today: 'Сегодня'
                        },
                        height: 'auto',
                        contentHeight: 'auto',
                        dayMaxEvents: false,
                        dayCellDidMount: function(info) {
                            const dateStr = info.date.toISOString().split('T')[0];
                            const today = new Date().toISOString().split('T')[0];

                            info.el.classList.remove('fc-day-available', 'fc-day-partial', 'fc-day-booked', 'fc-day-past', 'fc-day-selected');

                            if (dateStr < today) {
                                info.el.classList.add('fc-day-past');
                                return;
                            }

                            if (!selectedService) {
                                info.el.style.opacity = '0.5';
                                info.el.style.cursor = 'default';
                                return;
                            }
                            info.el.style.opacity = '1';

                            const slots = allSlots[dateStr] || [];
                            const totalSlots = slots.length;
                            const bookedSlots = slots.filter(s => !s.is_available).length;
                            const freeSlots = slots.filter(s => s.is_available).length;

                            if (totalSlots === 0) {
                                info.el.style.opacity = '0.3';
                                info.el.style.cursor = 'default';
                                return;
                            }
                            info.el.style.opacity = '1';

                            if (freeSlots === totalSlots && totalSlots > 0) {
                                info.el.classList.add('fc-day-available');
                                info.el.title = 'Все слоты свободны';
                            } else if (freeSlots > 0 && bookedSlots > 0) {
                                info.el.classList.add('fc-day-partial');
                                info.el.title = `${freeSlots} свободно, ${bookedSlots} занято`;
                            } else if (freeSlots === 0 && totalSlots > 0) {
                                info.el.classList.add('fc-day-booked');
                                info.el.title = 'Все слоты заняты';
                            }

                            let statusEl = info.el.querySelector('.fc-daygrid-day-bottom');
                            if (!statusEl) {
                                const dayEl = info.el.querySelector('.fc-daygrid-day-frame');
                                if (dayEl) {
                                    statusEl = document.createElement('div');
                                    statusEl.className = 'fc-daygrid-day-bottom';
                                    dayEl.appendChild(statusEl);
                                }
                            }

                            if (statusEl) {
                                let statusText = '';
                                let statusClass = '';

                                if (totalSlots > 0 && freeSlots === totalSlots) {
                                    statusText = `✓ ${freeSlots} свободно`;
                                    statusClass = 'bg-emerald-200 text-emerald-700';
                                } else if (freeSlots > 0 && bookedSlots > 0) {
                                    statusText = `~ ${freeSlots} свободно`;
                                    statusClass = 'bg-amber-200 text-amber-700';
                                } else if (totalSlots > 0 && freeSlots === 0) {
                                    statusText = '✕ Занято';
                                    statusClass = 'bg-rose-200 text-rose-700';
                                }

                                if (statusText) {
                                    statusEl.innerHTML = `<span class="fc-daygrid-day-status ${statusClass}">${statusText}</span>`;
                                } else {
                                    statusEl.innerHTML = '';
                                }
                            }

                            if (dateStr === selectedDate) {
                                info.el.classList.add('fc-day-selected');
                            }
                        },
                        dateClick: function(info) {
                            const dateStr = info.date.toISOString().split('T')[0];
                            const today = new Date().toISOString().split('T')[0];

                            if (dateStr < today) return;
                            if (!selectedService) {
                                alert('Пожалуйста, выберите услугу');
                                return;
                            }

                            const slots = allSlots[dateStr] || [];
                            const freeSlots = slots.filter(s => s.is_available);

                            if (freeSlots.length === 0) {
                                alert('На эту дату нет свободных слотов');
                                return;
                            }

                            selectedDate = dateStr;

                            document.querySelectorAll('.fc-daygrid-day').forEach(el => {
                                el.classList.remove('fc-day-selected');
                            });
                            info.dayEl.classList.add('fc-day-selected');

                            showTimePicker(freeSlots);
                        }
                    });

                    calendar.render();
                }

                function showTimePicker(slots) {
                    const dateFormatted = new Date(selectedDate + 'T00:00:00').toLocaleDateString('ru-RU', {
                        day: 'numeric',
                        month: 'long'
                    });

                    const timeModal = document.createElement('div');
                    timeModal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm';
                    timeModal.innerHTML = `
            <div class="bg-white rounded-3xl max-w-sm w-full p-8 shadow-2xl animate-in slide-in-from-bottom-4 duration-300">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-900">Выберите время</h3>
                        <p class="text-sm text-gray-500">${dateFormatted}</p>
                    </div>
                    <button type="button" class="close-time-modal p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="grid grid-cols-3 gap-2 max-h-60 overflow-y-auto">
                    ${slots.map(slot => `
                        <button type="button" class="time-slot-btn px-3 py-3 bg-gray-50 border border-gray-200 rounded-2xl hover:bg-indigo-50 hover:border-indigo-300 transition text-sm font-medium text-gray-700 hover:text-indigo-700">
                            ${slot.start_time}
                        </button>
                    `).join('')}
                </div>
            </div>
        `;

                    document.body.appendChild(timeModal);

                    timeModal.querySelector('.close-time-modal')?.addEventListener('click', function() {
                        timeModal.remove();
                    });

                    timeModal.querySelectorAll('.time-slot-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            selectedTime = this.textContent;
                            timeModal.remove();
                            openBookingModal();
                        });
                    });

                    timeModal.addEventListener('click', function(e) {
                        if (e.target === this) this.remove();
                    });
                }

                function openBookingModal() {
                    const dateFormatted = new Date(selectedDate + 'T00:00:00').toLocaleDateString('ru-RU', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });

                    modalService.textContent = selectedService.name;
                    modalEmployee.textContent = selectedEmployee
                        ? document.querySelector(`.employee-btn[data-id="${selectedEmployee}"]`)?.textContent || 'Любой'
                        : 'Любой';
                    modalDateTime.textContent = `${dateFormatted}, ${selectedTime}`;
                    modalPrice.textContent = selectedService.price.toLocaleString('ru-RU') + ' ₽';

                    formServiceId.value = selectedService.id;
                    formEmployeeId.value = selectedEmployee || '';
                    formDate.value = selectedDate;
                    formTime.value = selectedTime;

                    selectedDateTime.classList.remove('hidden');
                    selectedDateTimeText.textContent = `${dateFormatted}, ${selectedTime}`;

                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }

                function closeModal() {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                }

                closeModalBtn.addEventListener('click', closeModal);
                modal.addEventListener('click', function(e) {
                    if (e.target === this) closeModal();
                });

                initCalendar();

                // Автовыбор первой услуги
                if (serviceBtns.length > 0) {
                    serviceBtns[0].click();
                }

                // Автовыбор "Любой" специалист
                document.querySelector('.employee-btn[data-id=""]')?.click();
            });
        </script>
    @endpush
@endsection
