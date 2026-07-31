@extends('layouts.app')

@section('title', 'Календарь записей')

@section('content')
    <div class="space-y-6">
        <!-- Заголовок -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 flex items-center gap-3">
                        <span class="text-3xl">📅</span>
                        Календарь записей
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">Просматривайте и управляйте записями в календаре</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('appointments.index') }}" class="px-4 py-2 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100/80 transition">
                        📋 Список
                    </a>
                    <a href="{{ route('appointments.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-medium shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 transition-all hover:scale-[1.02]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Новая запись
                    </a>
                </div>
            </div>
        </div>

        <!-- Календарь - на всю ширину -->
        <div class="bg-white/70 backdrop-blur-xl rounded-3xl p-4 sm:p-6 border border-white/50 shadow-xl shadow-gray-100/50">
            <div id="calendar" class="w-full"></div>
        </div>
    </div>

    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

    <style>
        #calendar {
            background: transparent;
            padding: 4px;
            width: 100% !important;
        }

        .fc {
            font-family: 'Inter', sans-serif;
            --fc-border-color: #e5e7eb;
            --fc-button-bg-color: #f3f4f6;
            --fc-button-border-color: #e5e7eb;
            --fc-button-hover-bg-color: #e5e7eb;
            --fc-button-hover-border-color: #d1d5db;
            --fc-button-active-bg-color: #818cf8;
            --fc-button-active-border-color: #6366f1;
            --fc-today-bg-color: #eef2ff;
            --fc-highlight-color: rgba(99, 102, 241, 0.1);
            width: 100% !important;
        }

        .fc .fc-toolbar {
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .fc .fc-toolbar-title {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #111827 !important;
        }

        .fc .fc-button {
            padding: 0.5rem 1rem !important;
            border-radius: 0.75rem !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            text-transform: none !important;
            transition: all 0.2s !important;
            box-shadow: none !important;
            border: 1px solid #e5e7eb !important;
            background: #f9fafb !important;
            color: #374151 !important;
        }

        .fc .fc-button:hover {
            transform: scale(1.02);
            background: #f3f4f6 !important;
            border-color: #d1d5db !important;
        }

        .fc .fc-button-primary:focus {
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.3) !important;
        }

        .fc .fc-button-primary:not(:disabled):active,
        .fc .fc-button-primary:not(:disabled).fc-button-active {
            background: #818cf8 !important;
            border-color: #6366f1 !important;
            color: white !important;
        }

        .fc .fc-daygrid-day {
            border-color: #f3f4f6 !important;
            transition: background 0.15s;
        }

        .fc .fc-daygrid-day:hover {
            background: #fafbfc !important;
        }

        .fc .fc-daygrid-day-number {
            font-weight: 500 !important;
            color: #374151 !important;
            padding: 0.5rem !important;
            font-size: 0.875rem !important;
        }

        .fc .fc-daygrid-day.fc-day-today {
            background: #eef2ff !important;
        }

        .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
            color: #4f46e5 !important;
            font-weight: 700 !important;
        }

        .fc .fc-daygrid-event {
            border-radius: 0.5rem !important;
            padding: 0.25rem 0.5rem !important;
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            border: none !important;
            margin: 2px 4px !important;
            cursor: pointer !important;
            transition: transform 0.15s, box-shadow 0.15s !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
        }

        .fc .fc-daygrid-event:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
        }

        .fc .fc-daygrid-event .fc-event-title {
            font-weight: 500 !important;
            padding: 0 !important;
            font-size: 0.75rem !important;
        }

        .fc .fc-daygrid-event .fc-event-time {
            font-weight: 600 !important;
            opacity: 0.9 !important;
            margin-right: 4px !important;
            font-size: 0.7rem !important;
        }

        .fc .fc-daygrid-more-link {
            font-weight: 600 !important;
            color: #4f46e5 !important;
            font-size: 0.75rem !important;
            padding: 0.25rem 0.5rem !important;
            border-radius: 0.375rem !important;
            transition: background 0.15s !important;
        }

        .fc .fc-daygrid-more-link:hover {
            background: #eef2ff !important;
            color: #4338ca !important;
        }

        /* Стили для списка */
        .fc .fc-list-event {
            border-radius: 0.5rem !important;
            border: none !important;
            margin: 2px 0 !important;
        }

        .fc .fc-list-event:hover td {
            background: #f9fafb !important;
        }

        .fc .fc-list-event .fc-list-event-title {
            font-weight: 500 !important;
        }

        .fc .fc-list-event .fc-list-event-time {
            font-weight: 600 !important;
        }

        .fc .fc-list-day-text,
        .fc .fc-list-day-side-text {
            font-weight: 700 !important;
            color: #111827 !important;
        }

        /* Адаптив для мобильных */
        @media (max-width: 640px) {
            .fc .fc-toolbar {
                flex-direction: column !important;
                gap: 0.5rem !important;
                align-items: stretch !important;
            }

            .fc .fc-toolbar-chunk {
                display: flex !important;
                justify-content: center !important;
            }

            .fc .fc-toolbar-title {
                font-size: 1rem !important;
            }

            .fc .fc-button {
                padding: 0.4rem 0.8rem !important;
                font-size: 0.8rem !important;
            }

            .fc .fc-daygrid-day-number {
                font-size: 0.75rem !important;
                padding: 0.25rem !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            if (!calendarEl) return;

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                events: '{{ route("appointments.calendar.data") }}',
                eventClick: function(info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                    }
                },
                eventDidMount: function(info) {
                    var tooltip = info.event.title + '\n' +
                        'Время: ' + info.event.start.toLocaleString('ru-RU', {
                            hour: '2-digit',
                            minute: '2-digit',
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        });
                    info.el.title = tooltip;
                },
                locale: 'ru',
                buttonText: {
                    today: 'Сегодня',
                    month: 'Месяц',
                    week: 'Неделя',
                    day: 'День',
                    list: 'Список'
                },
                height: 'auto',
                contentHeight: 'auto',
                timeZone: 'local',
                nowIndicator: true,
                dayMaxEvents: 3,
                moreLinkText: function(num) {
                    return '+ ещё ' + num;
                },
                views: {
                    timeGridWeek: {
                        slotMinTime: '08:00:00',
                        slotMaxTime: '22:00:00',
                        allDaySlot: false
                    },
                    timeGridDay: {
                        slotMinTime: '08:00:00',
                        slotMaxTime: '22:00:00',
                        allDaySlot: false
                    }
                },
                eventColor: '#818cf8',
                eventTextColor: '#ffffff'
            });

            calendar.render();

            window.addEventListener('resize', function() {
                calendar.updateSize();
            });
        });
    </script>
@endsection
