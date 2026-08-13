@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="line-y py-2 text-3xl tracking-tight  text-pretty">СОГЛАСИЕ НА ОБРАБОТКУ ПЕРСОНАЛЬНЫХ ДАННЫХ</h1>

        <div
            class="line-y mt-6 max-w-2xl py-2 text-lg/7 font-medium text-pretty text-gray-600 dark:text-gray-400 flex flex-col">
            <span>ООО «Запишись»</span>
            <span>Дата публикации: 01.08.2026. Версия: 2.0</span>
        </div>

        <div
            class="max-w-2xl py-12 grid grid-cols-1 gap-6 text-sm/7 text-gray-600 dark:text-gray-400 [&_strong]:font-semibold [&_strong]:text-gray-950 dark:[&_strong]:text-white [&_h2]:text-lg/8 [&_h2]:font-semibold [&_h2]:text-gray-950 dark:[&_h2]:text-white [&_h3]:text-base/7 [&_h3]:font-semibold [&_h3]:text-gray-950 dark:[&_h3]:text-white [&_li]:relative [&_li]:before:absolute [&_li]:before:-top-0.5 [&_li]:before:-left-6 [&_li]:before:text-gray-300 [&_li]:before:content-[&quot;▪&quot;] [&_ul]:pl-9">

            <div class="flex flex-col mb-6 p-6 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <p class="text-base/7 font-medium text-gray-900 dark:text-white mb-2">
                    Я, нижеподписавшийся, при регистрации на Платформе «Запишись» (далее — Платформа),
                    даю согласие ООО «Запишись» (ИНН 0000000000) на обработку моих персональных данных.
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Настоящее согласие действует в соответствии с Федеральным законом от 27.07.2006 № 152-ФЗ
                    «О персональных данных».
                </p>
            </div>

            <h2 class="line-y">1. ПЕРЕЧЕНЬ ПЕРСОНАЛЬНЫХ ДАННЫХ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">1.1. Обязательные данные</span>
                    <ul>
                        <li>Номер мобильного телефона.</li>
                        <li>Имя и фамилия.</li>
                        <li>Адрес электронной почты (при наличии).</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">1.2. Дополнительные данные (для Бизнеса)</span>
                    <ul>
                        <li>Название компании.</li>
                        <li>Юридический адрес.</li>
                        <li>ИНН/ОГРН (при указании).</li>
                        <li>График работы.</li>
                        <li>Описание услуг.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">1.3. Технические данные</span>
                    <ul>
                        <li>IP-адрес.</li>
                        <li>Тип устройства и версия ОС.</li>
                        <li>Данные геолокации (с разрешения).</li>
                        <li>Cookie-файлы.</li>
                        <li>История посещений.</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">2. ЦЕЛИ ОБРАБОТКИ ДАННЫХ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">2.1. Основные цели</span>
                    <ul>
                        <li>Регистрация и идентификация Пользователя на Платформе.</li>
                        <li>Предоставление услуг онлайн-записи.</li>
                        <li>Отправка уведомлений о статусе записей.</li>
                        <li>Восстановление доступа к аккаунту.</li>
                        <li>Обеспечение безопасности и предотвращение мошенничества.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">2.2. Аналитические цели</span>
                    <ul>
                        <li>Улучшение качества сервиса.</li>
                        <li>Анализ поведения пользователей.</li>
                        <li>Формирование статистических отчетов.</li>
                        <li>Разработка новых функций.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">2.3. Маркетинговые цели</span>
                    <ul>
                        <li>Информирование о новых услугах и акциях.</li>
                        <li>Персонализация предложений.</li>
                        <li>Проведение опросов и исследований.</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">3. СПОСОБЫ ОБРАБОТКИ ДАННЫХ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">3.1. Способы обработки</span>
                    <ul>
                        <li>Автоматизированная обработка с использованием средств вычислительной техники.</li>
                        <li>Хранение в электронных базах данных.</li>
                        <li>Резервное копирование.</li>
                        <li>Шифрование данных при передаче и хранении.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">3.2. Передача третьим лицам</span>
                    <ul>
                        <li><strong>SMS-провайдеры</strong> — для отправки кодов подтверждения.</li>
                        <li><strong>WhatsApp (Green API)</strong> — для отправки уведомлений.</li>
                        <li><strong>Облачные сервисы</strong> — для хранения данных (Яндекс.Облако).</li>
                        <li><strong>Аналитические системы</strong> — для сбора анонимной статистики.</li>
                        <li>Передача данных осуществляется только с соблюдением требований 152-ФЗ.</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">4. СРОК ОБРАБОТКИ И ХРАНЕНИЯ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">4.1. Срок действия согласия</span>
                    <ul>
                        <li>Согласие действует с момента регистрации на Платформе.</li>
                        <li>Срок действия согласия — до момента удаления аккаунта Пользователем.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">4.2. Хранение данных</span>
                    <ul>
                        <li>Данные хранятся в течение всего срока использования Платформы.</li>
                        <li>После удаления аккаунта данные хранятся в резервных копиях 30 дней.</li>
                        <li>По истечении 30 дней данные уничтожаются.</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">5. ПРАВА СУБЪЕКТА ДАННЫХ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">5.1. Права Пользователя</span>
                    <ul>
                        <li><strong>Право на информацию</strong> — запросить копию своих данных (срок ответа — 30 дней).</li>
                        <li><strong>Право на исправление</strong> — изменить неактуальные данные в профиле.</li>
                        <li><strong>Право на удаление</strong> — удалить аккаунт и все данные.</li>
                        <li><strong>Право на ограничение</strong> — ограничить обработку данных.</li>
                        <li><strong>Право на отзыв согласия</strong> — отозвать согласие в любой момент.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">5.2. Реализация прав</span>
                    <ul>
                        <li>Для реализации прав необходимо отправить запрос на email: privacy@zapishis.ru.</li>
                        <li>Запрос должен содержать: ФИО, номер телефона, описание действия.</li>
                        <li>Срок рассмотрения запроса — 10 рабочих дней.</li>
                        <li>Ответ направляется на указанный email.</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">6. ОТЗЫВ СОГЛАСИЯ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">6.1. Порядок отзыва</span>
                    <ul>
                        <li>Согласие может быть отозвано в любое время.</li>
                        <li>Для отзыва необходимо направить письменное заявление по адресу: г. Назрань, ул. Московская, д. 1.</li>
                        <li>Либо направить запрос по email: privacy@zapishis.ru.</li>
                        <li>В заявлении необходимо указать: ФИО, номер телефона, дату регистрации.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">6.2. Последствия отзыва</span>
                    <ul>
                        <li>После отзыва согласия обработка данных прекращается.</li>
                        <li>Доступ к Платформе блокируется.</li>
                        <li>Данные удаляются в течение 30 дней.</li>
                        <li>Отзыв согласия не является основанием для возврата оплаченных средств.</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">7. ДОПОЛНИТЕЛЬНО ДЛЯ МОБИЛЬНОГО ПРИЛОЖЕНИЯ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">7.1. Разрешения приложения</span>
                    <ul>
                        <li><strong>Камера</strong> — для сканирования QR-кодов (опционально).</li>
                        <li><strong>Уведомления</strong> — для Push-уведомлений о записях.</li>
                        <li><strong>Геолокация</strong> — для поиска ближайших компаний (с разрешения).</li>
                        <li><strong>Контакты</strong> — для быстрого добавления (опционально).</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">7.2. Обработка в приложении</span>
                    <ul>
                        <li>Данные синхронизируются с сервером.</li>
                        <li>Локальное хранение данных ограничено.</li>
                        <li>Push-уведомления обрабатываются через Firebase Cloud Messaging.</li>
                    </ul>
                </div>
            </div>

            <h2 class="line-y">8. ЗАКЛЮЧИТЕЛЬНЫЕ ПОЛОЖЕНИЯ</h2>

            <div>
                <div class="flex flex-col mb-4">
                    <span class="font-bold">8.1. Действие согласия</span>
                    <ul>
                        <li>Согласие вступает в силу с момента регистрации.</li>
                        <li>Согласие действительно на всей территории Российской Федерации.</li>
                        <li>Согласие распространяется на все виды обработки данных.</li>
                    </ul>
                </div>

                <div class="flex flex-col mb-4">
                    <span class="font-bold">8.2. Изменения</span>
                    <ul>
                        <li>Компания оставляет право изменять условия согласия.</li>
                        <li>Об изменениях уведомляем за 7 дней до вступления.</li>
                        <li>Уведомление публикуется на сайте и отправляется по email.</li>
                    </ul>
                </div>

                <div class="flex flex-col">
                    <span class="font-bold">8.3. Контакты</span>
                    <ul>
                        <li>По вопросам обработки данных: privacy@zapishis.ru.</li>
                        <li>Телефон: +7 (999) 123-45-67.</li>
                        <li>Юридический адрес: г. Назрань, ул. Московская, д. 1.</li>
                    </ul>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row md:justify-between gap-6">
                    <div class="flex flex-col">
                        <span class="font-bold text-gray-900 dark:text-white">Дата:</span>
                        <span class="text-gray-600 dark:text-gray-400">_______________</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-gray-900 dark:text-white">Подпись:</span>
                        <span class="text-gray-600 dark:text-gray-400">_______________</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-gray-900 dark:text-white">Расшифровка:</span>
                        <span class="text-gray-600 dark:text-gray-400">_______________</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
