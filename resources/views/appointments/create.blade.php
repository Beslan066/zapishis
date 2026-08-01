@extends('layouts.app')

@section('title', 'Новая запись')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-8 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900">Новая запись</h1>
                    <p class="text-gray-500 text-sm">Создайте новую запись для клиента</p>
                </div>
            </div>

            <form action="{{ route('appointments.store') }}" method="POST" id="appointmentForm">
                @csrf
                @method('post')

                <div class="space-y-4">
                    <!-- Client с поиском и созданием -->
                    <div>
                        <label for="client_search" class="block text-sm font-semibold text-gray-700 mb-1.5">Клиент <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input type="text" id="client_search"
                                           placeholder="Поиск клиента по имени или телефону..."
                                           autocomplete="off"
                                           class="w-full px-4 py-3 pl-10 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none">
                                    <!-- Результаты поиска -->
                                    <div id="clientResults" class="hidden absolute z-50 w-full mt-1 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-100/50 max-h-60 overflow-y-auto">
                                        <div id="clientResultsList"></div>
                                        <div id="clientCreateNew" class="hidden p-3 border-t border-gray-100">
                                            <button type="button" id="createNewClientBtn" class="w-full flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl hover:bg-indigo-100 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                <span>Создать нового клиента</span>
                                            </button>
                                        </div>
                                        <div id="clientLoading" class="hidden p-4 text-center text-gray-400">
                                            <svg class="w-5 h-5 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                        <div id="clientNoResults" class="hidden p-4 text-center text-gray-400">
                                            <p class="text-sm">Клиент не найден</p>
                                            <button type="button" id="createNewClientBtnNoResults" class="mt-2 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm hover:bg-indigo-700 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                Создать клиента
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="openCreateClientModal"
                                        class="px-4 py-3 bg-indigo-50 text-indigo-600 rounded-2xl hover:bg-indigo-100 transition flex items-center gap-2 whitespace-nowrap">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span class="hidden sm:inline">Новый</span>
                                </button>
                            </div>
                        </div>
                        <input type="hidden" id="client_id" name="client_id">
                        @error('client_id')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Employee & Service -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="employee_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Сотрудник</label>
                            <select id="employee_id" name="employee_id"
                                    class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none appearance-none">
                                <option value="">Выберите сотрудника</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="service_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Услуга <span class="text-rose-500">*</span></label>
                            <select id="service_id" name="service_id" required
                                    class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none appearance-none">
                                <option value="">Выберите услугу</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}"
                                            data-duration="{{ $service->duration_minutes }}"
                                            data-price="{{ $service->price }}"
                                        {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }} ({{ $service->duration_minutes }} мин, {{ number_format($service->price, 0, '.', ' ') }} ₽)
                                    </option>
                                @endforeach
                            </select>
                            @error('service_id')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Date & Time -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="date" class="block text-sm font-semibold text-gray-700 mb-1.5">Дата <span class="text-rose-500">*</span></label>
                            <input type="date" id="date" name="date" required
                                   min="{{ date('Y-m-d') }}"
                                   value="{{ old('date', date('Y-m-d')) }}"
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none">
                            @error('date')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="time" class="block text-sm font-semibold text-gray-700 mb-1.5">Время <span class="text-rose-500">*</span></label>
                            <input type="time" id="time" name="time" required
                                   step="900"
                                   value="{{ old('time', '10:00') }}"
                                   class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none">
                            <div id="slotStatus" class="mt-2 text-sm hidden"></div>
                            @error('time')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Service Info -->
                    <div id="serviceInfo" class="hidden p-4 bg-indigo-50/80 rounded-2xl border border-indigo-100">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500">Длительность</p>
                                <p class="font-semibold text-gray-900" id="serviceDuration">-</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Стоимость</p>
                                <p class="font-semibold text-gray-900" id="servicePrice">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-1.5">Примечания</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full px-4 py-3 bg-gray-50/80 border border-gray-200/80 rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none resize-none"
                                  placeholder="Дополнительная информация...">{{ old('notes') }}</textarea>
                        @error('notes')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100/80">
                    <a href="{{ route('appointments.index') }}"
                       class="px-6 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100/80 transition">
                        Отмена
                    </a>
                    <button type="submit" id="submitBtn"
                            class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed">
                        Создать запись
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Модальное окно создания клиента -->
    <div id="createClientModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl max-w-md w-full shadow-2xl border border-white/50 overflow-hidden">
            <!-- Шапка -->
            <div class="flex items-center justify-between px-8 pt-8 pb-4 border-b border-gray-100/80">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-900">Новый клиент</h3>
                        <p class="text-xs text-gray-400">Заполните данные клиента</p>
                    </div>
                </div>
                <button type="button" id="closeClientModal" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Тело -->
            <form id="createClientForm" class="px-8 py-6">
                @csrf
                <div class="space-y-4">
                    <!-- Имя и Фамилия в одну строку -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Имя <span class="text-rose-500">*</span></label>
                            <input type="text" id="modal_first_name" name="first_name" required
                                   class="w-full px-4 py-2.5 bg-gray-50/80 border border-gray-200/80 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none text-sm"
                                   placeholder="Иван">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Фамилия</label>
                            <input type="text" id="modal_last_name" name="last_name"
                                   class="w-full px-4 py-2.5 bg-gray-50/80 border border-gray-200/80 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none text-sm"
                                   placeholder="Иванов">
                        </div>
                    </div>

                    <!-- Телефон -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Телефон <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input type="tel" id="modal_phone" name="phone" required
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50/80 border border-gray-200/80 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none text-sm"
                                   placeholder="+7 999 123 45 67">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" id="modal_email" name="email"
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50/80 border border-gray-200/80 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition outline-none text-sm"
                                   placeholder="example@mail.com">
                        </div>
                    </div>

                    <!-- Ошибка -->
                    <div id="modalError" class="hidden text-rose-600 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span id="modalErrorMessage"></span>
                    </div>
                </div>

                <!-- Кнопки -->
                <div class="flex items-center justify-end gap-3 mt-6 pt-5 border-t border-gray-100/80">
                    <button type="button" id="cancelClientModal" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100/80 transition">
                        Отмена
                    </button>
                    <button type="submit" id="saveClientBtn"
                            class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02] flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Создать клиента
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // ============================================
                // ЭЛЕМЕНТЫ
                // ============================================
                const searchInput = document.getElementById('client_search');
                const resultsDiv = document.getElementById('clientResults');
                const resultsList = document.getElementById('clientResultsList');
                const clientIdInput = document.getElementById('client_id');
                const createNewBtn = document.getElementById('createNewClientBtn');
                const createNewBtnNoResults = document.getElementById('createNewClientBtnNoResults');
                const clientLoading = document.getElementById('clientLoading');
                const clientNoResults = document.getElementById('clientNoResults');
                const clientCreateNew = document.getElementById('clientCreateNew');

                const modal = document.getElementById('createClientModal');
                const openModalBtn = document.getElementById('openCreateClientModal');
                const closeModalBtn = document.getElementById('closeClientModal');
                const cancelModalBtn = document.getElementById('cancelClientModal');
                const createClientForm = document.getElementById('createClientForm');
                const modalError = document.getElementById('modalError');
                const modalErrorMessage = document.getElementById('modalErrorMessage');
                const saveClientBtn = document.getElementById('saveClientBtn');

                const employeeSelect = document.getElementById('employee_id');
                const dateInput = document.getElementById('date');
                const serviceSelect = document.getElementById('service_id');
                const timeInput = document.getElementById('time');
                const slotStatus = document.getElementById('slotStatus');
                const submitBtn = document.getElementById('submitBtn');
                const serviceInfo = document.getElementById('serviceInfo');
                const serviceDuration = document.getElementById('serviceDuration');
                const servicePrice = document.getElementById('servicePrice');

                let searchTimeout;

                // ============================================
                // ПОИСК КЛИЕНТОВ
                // ============================================
                searchInput.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();

                    if (query.length < 2) {
                        resultsDiv.classList.add('hidden');
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        performSearch(query);
                    }, 300);
                });

                function performSearch(query) {
                    resultsDiv.classList.remove('hidden');
                    clientLoading.classList.remove('hidden');
                    resultsList.innerHTML = '';
                    clientNoResults.classList.add('hidden');
                    clientCreateNew.classList.add('hidden');

                    fetch(`/api/clients/search?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            clientLoading.classList.add('hidden');

                            if (data.length === 0) {
                                clientNoResults.classList.remove('hidden');
                                return;
                            }

                            data.forEach(client => {
                                const item = document.createElement('div');
                                item.className = 'p-3 hover:bg-indigo-50 cursor-pointer transition flex items-center gap-3 border-b border-gray-100 last:border-0';
                                item.innerHTML = `
                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center text-sm font-bold text-indigo-700 flex-shrink-0">
                            ${client.initials || '?'}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">${client.first_name} ${client.last_name || ''}</p>
                            <p class="text-xs text-gray-500">${client.phone || ''} ${client.email ? '· ' + client.email : ''}</p>
                        </div>
                    `;
                                item.addEventListener('click', function () {
                                    selectClient(client.id, client.first_name + ' ' + (client.last_name || ''));
                                });
                                resultsList.appendChild(item);
                            });

                            // Показываем кнопку создания нового клиента
                            clientCreateNew.classList.remove('hidden');
                        })
                        .catch(() => {
                            clientLoading.classList.add('hidden');
                            clientNoResults.classList.remove('hidden');
                        });
                }

                function selectClient(id, name) {
                    clientIdInput.value = id;
                    searchInput.value = name;
                    resultsDiv.classList.add('hidden');
                    searchInput.classList.remove('border-rose-500');
                    searchInput.classList.add('border-emerald-500', 'bg-emerald-50/50');
                    checkAvailability();
                }

                // ============================================
                // МОДАЛЬНОЕ ОКНО СОЗДАНИЯ КЛИЕНТА
                // ============================================
                function openModal() {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';

                    // Очищаем поля
                    document.getElementById('modal_first_name').value = '';
                    document.getElementById('modal_last_name').value = '';
                    document.getElementById('modal_phone').value = '';
                    document.getElementById('modal_email').value = '';

                    // Если в поиске введено имя (не номер), подставляем в поле имени
                    const searchValue = searchInput.value.trim();
                    if (searchValue && !searchValue.match(/^[\d\s\+\-\(\)]+$/)) {
                        document.getElementById('modal_first_name').value = searchValue;
                    }

                    // Если в поиске номер, подставляем в телефон
                    if (searchValue && searchValue.match(/^[\d\s\+\-\(\)]+$/)) {
                        document.getElementById('modal_phone').value = searchValue;
                    }

                    modalError.classList.add('hidden');
                    setTimeout(() => document.getElementById('modal_first_name').focus(), 100);
                }

                function closeModal() {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                    saveClientBtn.disabled = false;
                    saveClientBtn.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Создать клиента
        `;
                }

                // Проверяем наличие элементов перед добавлением событий
                if (openModalBtn) {
                    openModalBtn.addEventListener('click', openModal);
                }
                if (createNewBtn) {
                    createNewBtn.addEventListener('click', openModal);
                }
                if (createNewBtnNoResults) {
                    createNewBtnNoResults.addEventListener('click', openModal);
                }
                if (closeModalBtn) {
                    closeModalBtn.addEventListener('click', closeModal);
                }
                if (cancelModalBtn) {
                    cancelModalBtn.addEventListener('click', closeModal);
                }
                if (modal) {
                    modal.addEventListener('click', function (e) {
                        if (e.target === this) closeModal();
                    });
                }

                // ============================================
                // СОЗДАНИЕ КЛИЕНТА ЧЕРЕЗ AJAX
                // ============================================
                if (createClientForm) {
                    createClientForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        modalError.classList.add('hidden');
                        saveClientBtn.disabled = true;
                        saveClientBtn.innerHTML = '<svg class="w-5 h-5 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

                        const formData = new FormData(this);
                        const data = {
                            first_name: formData.get('first_name'),
                            last_name: formData.get('last_name'),
                            phone: formData.get('phone'),
                            email: formData.get('email'),
                        };

                        fetch('/api/clients', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            credentials: 'include',
                            body: JSON.stringify(data)
                        })
                            .then(response => response.json())
                            .then(result => {
                                saveClientBtn.disabled = false;
                                saveClientBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Создать клиента
                `;

                                if (result.success === true || result.data) {
                                    const client = result.data || result;
                                    selectClient(client.id, client.first_name + ' ' + (client.last_name || ''));
                                    closeModal();

                                    const notification = document.createElement('div');
                                    notification.className = 'fixed top-4 right-4 z-50 p-4 bg-emerald-50/95 backdrop-blur-sm border border-emerald-200 rounded-2xl text-emerald-700 shadow-xl flex items-center gap-3 animate-in slide-in-from-top-2';
                                    notification.innerHTML = `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Клиент создан!</span>
                    `;
                                    document.body.appendChild(notification);
                                    setTimeout(() => notification.remove(), 3000);
                                } else {
                                    modalErrorMessage.textContent = result.message || 'Ошибка создания клиента';
                                    modalError.classList.remove('hidden');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                saveClientBtn.disabled = false;
                                saveClientBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Создать клиента
                `;
                                modalErrorMessage.textContent = 'Ошибка сети. Попробуйте еще раз.';
                                modalError.classList.remove('hidden');
                            });
                    });
                }

                // ============================================
                // ЗАКРЫТИЕ РЕЗУЛЬТАТОВ ПРИ КЛИКЕ ВНЕ
                // ============================================
                document.addEventListener('click', function (e) {
                    if (!resultsDiv.contains(e.target) && e.target !== searchInput) {
                        resultsDiv.classList.add('hidden');
                    }
                });

                // ============================================
                // ИНФОРМАЦИЯ ОБ УСЛУГЕ
                // ============================================
                if (serviceSelect) {
                    serviceSelect.addEventListener('change', function () {
                        const selected = this.options[this.selectedIndex];
                        if (this.value) {
                            serviceInfo.classList.remove('hidden');
                            serviceDuration.textContent = selected.dataset.duration + ' мин';
                            servicePrice.textContent = Number(selected.dataset.price).toLocaleString('ru-RU') + ' ₽';
                        } else {
                            serviceInfo.classList.add('hidden');
                        }
                        checkAvailability();
                    });
                }

                // ============================================
                // ПРОВЕРКА ДОСТУПНОСТИ СЛОТОВ
                // ============================================
                function checkAvailability() {
                    const employeeId = employeeSelect?.value;
                    const date = dateInput?.value;
                    const serviceId = serviceSelect?.value;
                    const clientId = clientIdInput?.value;

                    if (employeeId && date && serviceId && clientId) {
                        if (slotStatus) {
                            slotStatus.classList.remove('hidden');
                            slotStatus.innerHTML = `
                    <span class="text-amber-600 flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Проверка доступных слотов...
                    </span>
                `;
                        }
                        if (submitBtn) submitBtn.disabled = true;

                        fetch(`/api/v1/appointments/available?employee_id=${employeeId}&date=${date}&service_id=${serviceId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    const slots = data.data.available_slots;
                                    const timeValue = timeInput?.value;

                                    if (slots.length === 0) {
                                        if (slotStatus) {
                                            slotStatus.innerHTML = `
                                    <span class="text-amber-600 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        Нет доступных слотов на выбранную дату
                                    </span>
                                `;
                                        }
                                        if (submitBtn) submitBtn.disabled = true;
                                        if (timeInput) timeInput.disabled = true;
                                    } else {
                                        const isAvailable = slots.some(slot => slot.start_time === timeValue);

                                        if (timeValue && !isAvailable) {
                                            if (slotStatus) {
                                                slotStatus.innerHTML = `
                                        <span class="text-rose-600 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            Выбранное время недоступно
                                        </span>
                                    `;
                                            }
                                            if (submitBtn) submitBtn.disabled = true;
                                            if (timeInput) timeInput.disabled = false;
                                        } else {
                                            if (slotStatus) {
                                                slotStatus.innerHTML = `
                                        <span class="text-emerald-600 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Доступно слотов: ${slots.length}
                                        </span>
                                    `;
                                            }
                                            if (submitBtn) submitBtn.disabled = false;
                                            if (timeInput) timeInput.disabled = false;
                                        }
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching slots:', error);
                                if (slotStatus) {
                                    slotStatus.innerHTML = `
                            <span class="text-rose-600">Ошибка проверки слотов</span>
                        `;
                                }
                                if (submitBtn) submitBtn.disabled = true;
                            });
                    } else {
                        if (slotStatus) slotStatus.classList.add('hidden');
                        if (submitBtn) submitBtn.disabled = false;
                        if (timeInput) timeInput.disabled = false;
                    }
                }

                // ============================================
                // СОБЫТИЯ ДЛЯ ПРОВЕРКИ СЛОТОВ
                // ============================================
                if (employeeSelect) {
                    employeeSelect.addEventListener('change', checkAvailability);
                }
                if (dateInput) {
                    dateInput.addEventListener('change', checkAvailability);
                }
                if (serviceSelect) {
                    serviceSelect.addEventListener('change', checkAvailability);
                }
                if (timeInput) {
                    timeInput.addEventListener('change', checkAvailability);
                }

                // ============================================
                // ВАЛИДАЦИЯ ФОРМЫ ПЕРЕД ОТПРАВКОЙ
                // ============================================
                const appointmentForm = document.getElementById('appointmentForm');
                if (appointmentForm) {
                    appointmentForm.addEventListener('submit', function (e) {
                        if (!clientIdInput.value) {
                            e.preventDefault();
                            searchInput.classList.add('border-rose-500', 'bg-rose-50/50');
                            searchInput.focus();
                            return;
                        }
                    });
                }

                if (searchInput) {
                    searchInput.addEventListener('input', function () {
                        if (this.classList.contains('border-rose-500')) {
                            this.classList.remove('border-rose-500', 'bg-rose-50/50');
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
