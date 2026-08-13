@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="line-y py-2 text-3xl tracking-tight text-pretty">ПОЛИТИКА КОНФИДЕНЦИАЛЬНОСТИ</h1>

        <div
            class="line-y mt-6 max-w-2xl py-2 text-lg/7 font-medium text-pretty text-gray-600 dark:text-gray-400 flex flex-col">
            <span>ООО «Запишись»</span>
            <span>Дата публикации: 01.08.2026. Версия: 2.0</span>
        </div>

        <div
            class="max-w-2xl py-12 grid grid-cols-1 gap-6 text-sm/7 text-gray-600 dark:text-gray-400 [&_strong]:font-semibold [&_strong]:text-gray-950 dark:[&_strong]:text-white [&_h2]:text-lg/8 [&_h2]:font-semibold [&_h2]:text-gray-950 dark:[&_h2]:text-white [&_h3]:text-base/7 [&_h3]:font-semibold [&_h3]:text-gray-950 dark:[&_h3]:text-white [&_li]:relative [&_li]:before:absolute [&_li]:before:-top-0.5 [&_li]:before:-left-6 [&_li]:before:text-gray-300 [&_li]:before:content-[&quot;▪&quot;] [&_ul]:pl-9">

            <h2 class="line-y">1. ОБЩИЕ ПОЛОЖЕНИЯ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">1.1. Термины</span>
                    <ul>
                        <li><strong>Персональные данные</strong> — любая информация, относящаяся к Пользователю.</li>
                        <li><strong>Обработка</strong> — сбор, запись, систематизация, хранение, обновление, использование, распространение, блокирование, уничтожение.</li>
                        <li><strong>Автоматизированная обработка</strong> — обработка с использованием средств вычислительной техники.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">1.2. Область действия</span>
                    <ul>
                        <li>Веб-сайт https://zapishis.ru</li>
                        <li>Мобильное приложение «Запишись»</li>
                        <li>API-интерфейсы</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">2. КАКИЕ ДАННЫЕ МЫ СОБИРАЕМ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">2.1. Обязательные данные</span>
                    <ul>
                        <li><strong>Номер телефона</strong> — идентификация, авторизация, уведомления.</li>
                        <li><strong>Имя и фамилия</strong> — формирование профиля.</li>
                        <li><strong>Email</strong> — восстановление доступа, опциональные уведомления.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">2.2. Данные для Бизнеса</span>
                    <ul>
                        <li><strong>Название компании</strong> — идентификация бизнеса.</li>
                        <li><strong>Адрес</strong> — отображение на карте.</li>
                        <li><strong>График работы</strong> — формирование расписания.</li>
                        <li><strong>Описание услуг</strong> — информирование клиентов.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">2.3. Технические данные</span>
                    <ul>
                        <li>IP-адрес.</li>
                        <li>Тип устройства.</li>
                        <li>Версия ОС.</li>
                        <li>Данные геолокации (с разрешения).</li>
                        <li>Cookie-файлы.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">2.4. Данные о записях</span>
                    <ul>
                        <li>История посещений.</li>
                        <li>Список услуг.</li>
                        <li>Комментарии и оценки.</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">3. КАК МЫ ИСПОЛЬЗУЕМ ДАННЫЕ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">3.1. Основные цели</span>
                    <ul>
                        <li>Предоставление услуг записи.</li>
                        <li>Идентификация пользователей.</li>
                        <li>Отправка уведомлений.</li>
                        <li>Улучшение работы сервиса.</li>
                        <li>Предотвращение мошенничества.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">3.2. Аналитика и маркетинг</span>
                    <ul>
                        <li>Анонимная статистика использования.</li>
                        <li>Сегментация пользователей.</li>
                        <li>Рекомендательные системы.</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">4. КОМУ МЫ ПЕРЕДАЕМ ДАННЫЕ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">4.1. Третьи лица</span>
                    <ul>
                        <li><strong>SMS-провайдеры</strong> — номер телефона для отправки кодов.</li>
                        <li><strong>WhatsApp (Green API)</strong> — номер телефона для отправки уведомлений.</li>
                        <li><strong>Облачные сервисы</strong> — технические данные для хостинга.</li>
                        <li><strong>Аналитика (Яндекс.Метрика)</strong> — анонимные данные для статистики.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">4.2. Бизнесы</span>
                    <ul>
                        <li>При создании записи передаются: имя, телефон клиента.</li>
                        <li>Бизнес не получает полный профиль клиента.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">4.3. Обязательные требования</span>
                    <ul>
                        <li>Передача данных осуществляется по запросу уполномоченных органов (МВД, суд и т.д.).</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">5. ГДЕ И КАК МЫ ХРАНИМ ДАННЫЕ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">5.1. Способы хранения</span>
                    <ul>
                        <li>Данные хранятся в зашифрованном виде.</li>
                        <li>Резервное копирование каждые 24 часа.</li>
                        <li>Используются серверы на территории РФ.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">5.2. Срок хранения</span>
                    <ul>
                        <li>Данные хранятся до момента удаления аккаунта.</li>
                        <li>После удаления аккаунта — 30 дней в резервных копиях.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">5.3. Безопасность</span>
                    <ul>
                        <li>Используется TLS 1.3 для шифрования передачи.</li>
                        <li>Пароли хранятся с использованием bcrypt.</li>
                        <li>API-ключи хранятся в защищенном окружении.</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">6. ПРАВА ПОЛЬЗОВАТЕЛЯ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">6.1. Право на информацию</span>
                    <ul>
                        <li>Пользователь может запросить копию своих данных.</li>
                        <li>Срок ответа — 30 дней.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">6.2. Право на исправление</span>
                    <ul>
                        <li>Пользователь может изменить данные в профиле.</li>
                        <li>Исправление технических ошибок возможно по запросу.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">6.3. Право на удаление</span>
                    <ul>
                        <li>Пользователь может удалить аккаунт в любое время.</li>
                        <li>После удаления доступ к данным восстанавливается 30 дней.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">6.4. Право на ограничение</span>
                    <ul>
                        <li>Пользователь может ограничить получение уведомлений.</li>
                        <li>Отказ от маркетинговых рассылок в один клик.</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">7. COOKIE-ФАЙЛЫ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">7.1. Типы cookie</span>
                    <ul>
                        <li><strong>Сессионные</strong> — авторизация, безопасность.</li>
                        <li><strong>Аналитические</strong> — Яндекс.Метрика, Google Analytics.</li>
                        <li><strong>Функциональные</strong> — запоминание настроек.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">7.2. Управление cookie</span>
                    <ul>
                        <li>Пользователь может отключить cookie в браузере.</li>
                        <li>Некоторые функции могут работать некорректно.</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">8. ОТВЕТСТВЕННОСТЬ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">8.1. Меры безопасности</span>
                    <ul>
                        <li>Регулярные аудиты безопасности.</li>
                        <li>Шифрование данных.</li>
                        <li>Мониторинг подозрительной активности.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">8.2. Инциденты</span>
                    <ul>
                        <li>При утечке данных Пользователь уведомляется в течение 24 часов.</li>
                        <li>Уведомление Роскомнадзора в течение 72 часов.</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">9. ДОПОЛНИТЕЛЬНО ДЛЯ МОБИЛЬНОГО ПРИЛОЖЕНИЯ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">9.1. Разрешения</span>
                    <ul>
                        <li><strong>Камера</strong> — сканирование QR-кодов для входа.</li>
                        <li><strong>Уведомления</strong> — Push-уведомления о записях.</li>
                        <li><strong>Геолокация</strong> — поиск ближайших компаний.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">9.2. Приложения</span>
                    <ul>
                        <li>В приложении используются стандартные библиотеки.</li>
                        <li>Доступ к данным строго ограничен.</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">10. КОНТАКТЫ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">10.1. По вопросам конфиденциальности</span>
                    <ul>
                        <li>Email: privacy@zapishis.ru</li>
                        <li>Телефон: +7 (999) 123-45-67</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">10.2. По техническим вопросам</span>
                    <ul>
                        <li>Email: support@zapishis.ru</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">11. ИЗМЕНЕНИЯ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">11.1. Уведомление об изменениях</span>
                    <ul>
                        <li>Изменения публикуются на сайте за 7 дней до вступления.</li>
                        <li>Активные пользователи получают уведомление по email/SMS.</li>
                    </ul>
                </div>

                <div class="flex flex-col">
                    <span class="font-bold">11.2. Согласие</span>
                    <ul>
                        <li>Использование сервиса после изменений означает согласие.</li>
                        <li>При несогласии необходимо удалить аккаунт.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
@endsection
