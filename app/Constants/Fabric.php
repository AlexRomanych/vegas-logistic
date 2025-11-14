<?php
// ___ Стартовый индекс порядка физических рулонов
const EXEC_ROLL_START_ORDER_INDEX = 1;
const EXEC_ROLL_FALSE_ORDER_INDEX = -1000;   // __ Будем считать, что больше 1000 рулонов в день не будет
const EXEC_ROLL_ROLLING_ORDER_INDEX = -10000;

// ___ Статусы сменного задания

// ___ Сменное задание создано (или сохранено)
const FABRIC_TASK_UNKNOWN = 'unknown';
const FABRIC_TASK_UNKNOWN_CODE = 0;

// ___ Сменное задание создано (или сохранено)
const FABRIC_TASK_CREATED = 'created';
const FABRIC_TASK_CREATED_CODE = 1;

// ___ Сменное задание отправлено на выполнение
const FABRIC_TASK_PENDING = 'pending';
const FABRIC_TASK_PENDING_CODE = 2;

// ___ Сменное задание взято на выполнение (находится в процессе выполнения)
const FABRIC_TASK_RUNNING = 'running';
const FABRIC_TASK_RUNNING_CODE = 3;

// ___ Сменное задание выполнено (закрыто)
const FABRIC_TASK_DONE = 'done';
const FABRIC_TASK_DONE_CODE = 4;

//hr---------------------------------------------------------------------

// ___ Константы стегальных машин
const FABRIC_MACHINE_UNKNOWN_ID = 0;
const FABRIC_MACHINE_UNKNOWN_TITLE = 'unknown';
const FABRIC_MACHINE_AMERICAN_ID = 1;
const FABRIC_MACHINE_AMERICAN_TITLE = 'american';
const FABRIC_MACHINE_GERMAN_ID = 2;
const FABRIC_MACHINE_GERMAN_TITLE = 'german';
const FABRIC_MACHINE_CHINA_ID = 3;
const FABRIC_MACHINE_CHINA_TITLE = 'china';
const FABRIC_MACHINE_KOREAN_ID = 4;
const FABRIC_MACHINE_KOREAN_TITLE = 'korean';

//hr---------------------------------------------------------------------

// ___ Константы статусов состояния готовности рулона на стежке
const FABRIC_ROLL_UNREADY = 0;
const FABRIC_ROLL_PENDING = 1;
const FABRIC_ROLL_READY = 2;

// ___ Константы статусов состояния готовности рулона на стежке
// ___ Рулон не создан
const FABRIC_ROLL_NULL_CODE = -1;

// ___ Рулон создан (или сохранен)
const FABRIC_ROLL_CREATED_CODE = 0;

// ___ Рулон взят на выполнение (находится в процессе выполнения)
const FABRIC_ROLL_RUNNING_CODE = 1;

// ___ Рулон был взят на выполнение, но выполнение приостановлено (например, перенос на другую смену)
const FABRIC_ROLL_PAUSED_CODE = 2;

// ___ Рулон выполнен (закрыт)
const FABRIC_ROLL_DONE_CODE = 3;

// ___ Рулон не выполнен (не закрыт)
const FABRIC_ROLL_FALSE_CODE = 4;

// ___ Рулон переходящий (с одной смены на другую)
const FABRIC_ROLL_ROLLING_CODE = 5;

// ___ Рулон поставленный на учет в 1С
const FABRIC_ROLL_REGISTERED_1C_CODE = 6;

// ___ Рулон перемещенный на закрой
const FABRIC_ROLL_MOVED_CODE = 7;

// ___ Рулон принятый на закрое
const FABRIC_ROLL_ACCEPTED_CODE = 8;

// ___ Рулон отмененный на закрое
const FABRIC_ROLL_CANCELLED_CODE = 8;

// ___ Рулон списанный после закроя
const FABRIC_ROLL_CLOSED_CODE = 9;

// line ----------------------------------------------------------------
// ___ Константы Групп ПЯ
const COMMON_GROUP = 0;      // 'name' => 'Общая'
const FABRIC_GROUP = 1;      //'name' => 'Стежка'
const CUTTING_GROUP = 2;     // 'name' => 'Раскрой'
const SEWING_GROUP = 3;      // 'name' => 'Швейный цех'
const ASSEMBLY_GROUP = 4;    // 'name' => 'Сборка'
const PACK_GROUP = 5;        // 'name' => 'Упаковка'
const BLOCK_GROUP = 6;       //  'name' => 'Участок ПБ'


// line ----------------------------------------------------------------

// ___ Период, за который считаем статистику по ткани для ПС
const FABRIC_STATISTIC_PERIOD = 1;
