<?php
// descr: Статусы сменного задания

// descr: Сменное задание создано (или сохранено)
const FABRIC_TASK_UNKNOWN = 'unknown';
const FABRIC_TASK_UNKNOWN_CODE = 0;

// descr: Сменное задание создано (или сохранено)
const FABRIC_TASK_CREATED = 'created';
const FABRIC_TASK_CREATED_CODE = 1;

// descr: Сменное задание отправлено на выполнение
const FABRIC_TASK_PENDING = 'pending';
const FABRIC_TASK_PENDING_CODE = 2;

// descr: Сменное задание взято на выполнение (находится в процессе выполнения)
const FABRIC_TASK_RUNNING = 'running';
const FABRIC_TASK_RUNNING_CODE = 3;

// descr: Сменное задание выполнено (закрыто)
const FABRIC_TASK_DONE = 'done';
const FABRIC_TASK_DONE_CODE = 4;

//hr---------------------------------------------------------------------

// descr: Константы стегальных машин
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

// descr: Константы статусов состояния готовности рулона на стежке
const FABRIC_ROLL_UNREADY = 0;
const FABRIC_ROLL_PENDING = 1;
const FABRIC_ROLL_READY = 2;

// descr: Константы статусов состояния готовности рулона на стежке
// descr: Рулон создан (или сохранен)
const FABRIC_ROLL_CREATED_CODE = 0;

// descr: Рулон взят на выполнение (находится в процессе выполнения)
const FABRIC_ROLL_RUNNING_CODE = 1;

// descr: Рулон был взят на выполнение, но выполнение приостановлено (например, перенос на другую смену)
const FABRIC_ROLL_PAUSED_CODE = 2;

// descr: Рулон выполнен (закрыт)
const FABRIC_ROLL_DONE_CODE = 3;

// descr: Рулон не выполнен (не закрыт)
const FABRIC_ROLL_FALSE_CODE = 4;

// descr: Рулон переходящий (с одной смены на другую)
const FABRIC_ROLL_ROLLING_CODE = 5;

// descr: Рулон поставленный на учет в 1С
const FABRIC_ROLL_REGISTERED_1C_CODE = 6;

// descr: Рулон перемещенный на закрой
const FABRIC_ROLL_MOVED_CODE = 7;

// descr: Рулон принятый на закрое
const FABRIC_ROLL_ACCEPTED_CODE = 8;

// descr: Рулон отмененный на закрое
const FABRIC_ROLL_CANCELLED_CODE = 8;
