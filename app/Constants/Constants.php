<?php

const TIME_ZONE_OFFSET = 3;                     // смещение времени по UTC +3

const CODE_1C = 'code_1c';                      // Название переменной, в которой передается код 1С
const CODE_1C_LENGTH = 9;                       // Длина кода 1С


const LAT_X = 'x';
const RUS_X = 'х';

const VEGAS = 'vegas';                          // Название переменной для внутреннего использования
const ORDERS = 'orders';                        // для логирования ошибок для заказов

const OK_STATUS_WORD = 'success';               // отправляем при удачном запросе
const FAIL_STATUS_WORD = 'fail';                // отправляем при неудачном запросе


const CLIENT_AVERAGE_MATTRESS_PREFIX = 'CMID_';  // CLIENT MATTRESS ID
const CLIENT_AVERAGE_ACCESSORY_PREFIX = 'CAID_';  // CLIENT ACCESSORY ID

// Descr: Список всех модулей производства (ПЯ)
// Descr: Причем, это не обязательно те модули, которые реально есть сейчас в ПЯ,
// Descr: а просто все возможные, которые могут быть в ПЯ.
// Descr: Например, в стежке, все стегальные машины определены в отдельные ПЯ,
// Descr: но в данном 'FABRIC' с ID=1 будут они все объединены в 1 модуль ПЯ 'Производство полотен стеганных'
// Descr: Warning: Должны быть в точности продублированы в JavaScript!!!!!

// Descr: ID модуля производства "Производство полотен стеганных" (объединяет все стегальные машины)
const FABRIC_ID = 1;

// __ Поля, которые получаем из базы, если заявка существует
const ORDER_METADATA_FIELD = 'metadata';
const CLIENT_SHORT_NAME_FIELD = 'client_short_name';
const ORDER_NO_FIELD = 'order_no';
const ORDER_PERIOD_FIELD = 'order_period';


const VALIDATE_FIELD = 'validate';
const CHECK_FIELD = 'check';
const CHECK_PASS = 'ok';
const CHECK_FAIL = 'fail';

const ADVICE_FIELD = 'advice';

const ORDER_ELEMENTS_TYPE_FIELD = 'elements_type';

const ACTION_FIELD = 'action';
const ACTION_NONE = 'Нет действия';
const ACTION_CLIENT_IGNORE = 'Игнорировать Клиента';
const ACTION_CLIENT_ADD = 'Создать Клиента';
const ACTION_ORDER_UPDATE = 'Обновить Заявку';
const ACTION_ORDER_IGNORE = 'Игнорировать Заявку';
const ACTION_ORDER_ADD = 'Создать Заявку';
const ACTION_MODEL_ADD = 'Создать Модель';
const ACTION_MODEL_IGNORE = 'Игнорировать Модель';





