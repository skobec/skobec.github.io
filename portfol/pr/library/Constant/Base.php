<?php

/**
* Конcтанты
*/
class Constant_Base {

    const VAR_DEF_CACHE_TIME = 86400; // Время хранения кеша АПИ-вызовов по умолчанию
    const VAR_USER_AUTH_PERIOD = 604800; // количество секунд в течение которых активная сессия обычного пользователя (по умолчанию 7 дней = 3600сек * 24ч * 7дн)
    const VAR_CAPTCHA_COUNT = 3; // Количество попыток до появления CAPTCHA в форме ввода логина-пароля
    const LIMIT_ACCOUNT_COUNT = 50000; // количество ЛС в методе Model_Billing::getExpense()

    // Количество записей на одну страницу
    const DEF_ITEMS_PER_PAGE = 5;
	const INFINITY_DEF_ITEMS_PER_PAGE = 1000000000;

    const VAR_MAX_UPLOAD_FILES = 5; // максимальное количество загружаемых файлов
    const VAR_TEXT_NO_DATA = 'Нет данных';
    const VAR_TEXT_NO_UPLOADED_FILES = 'нет загруженных файлов';
    const VAR_TEXT_REQUIRED_FIELD = 'Обязательное поле';
    const VAR_TEXT_RAION = 'Муниципальный район / Городской округ';
	const VAR_TEXT_NO_PDF = 'Без генерации PDF';

    // Коды ошибок
    const ERR_NOT_AUTH = 211; // Пользователь не авторизован
    const ERR_BAD_REQUEST = 400; // Плохой, неверный запрос

    // Типы ЛС
    const ACCOUNT_TYPE_RO_ID = 1;
    const ACCOUNT_TYPE_SPEC_ID = 2;

    // Для метода Delivery::sendMail()
    const DELIVERY_HOST = 'smtp.yandex.ru';
    const DELIVERY_PORT = 587;
    const DELIVERY_FROM = 'support1@etton.ru';
    const DELIVERY_USER = 'support1@etton.ru';
    const DELIVERY_PASS = 'vLYLoazcxBdMW98ym2Py';

	// Уровни доступа пользователя к модулям
    const ACCESS_LEVEL_NONE = 0;
    const ACCESS_LEVEL_CONTROLLER = 1;
    const ACCESS_LEVEL_OPERATOR = 2;

    // Типы данных
    const TYPE_LIST = 4;
    const TYPE_TABLE = 5;

    // Статусы записей
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCKED = 2;
    const STATUS_DELETED = 4;
    const STATUS_HIDDEN = 8;
    const STATUS_TEST = 16;
    const STATUS_PRIVATE = 32;
    const STATUS_DUPLICATE = 64;
    const STATUS_MODERATED = 128;

    // статусы заявок
    const ISSUE_STATUS_NEW = 1; // Поступившая (1)
    const ISSUE_STATUS_CONFIRMED = 2; // Подтвержденная (2)
    const ISSUE_STATUS_INWORK = 3; // В работе (3)
    const ISSUE_STATUS_READY = 4; // Выполнена (4)
    const ISSUE_STATUS_COMPLETED = 5; // Завершено (5)
    const ISSUE_STATUS_REJECTED = 6; // Отклонена (6)
    const ISSUE_STATUS_REPEAT = 7; // Повторная (7)
    const ISSUE_STATUS_ARCHIVE = 8; // В архиве (8)

    // События
    const LOG_USER_LOGIN = 1; // Пользователь авторизован
    const LOG_USER_LOGOUT = 2; // Выход из системы
    const LOG_ENTITY_EDIT = 8; // Редактирование объекта
    const LOG_ENTITY_DELETE = 9; // Удаление объекта

}
