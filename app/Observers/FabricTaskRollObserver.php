<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Observers;

use App\Models\Manufacture\Cells\Fabric\FabricTaskRoll;
use App\Models\Manufacture\Logs\FabricTaskRollLog;

class FabricTaskRollObserver
{

    /**
     * ___ Handle the FabricTaskRoll "created" event.
     * @param FabricTaskRoll $fabricTaskRoll
     * @return void
     */
    public function created(FabricTaskRoll $fabricTaskRoll): void
    {
        // TODO: Сделать проверку, что рулон создается в процессе выполнения. Передача тоже через какое-то поле
        FabricTaskRollLog::query()->create([
            'fabric_task_roll_id' => $fabricTaskRoll->id,
            'user_id'             => auth()->id(),
            'status_after'        => $fabricTaskRoll->roll_status,
            'event'               => 'Создание рулона',
            // 'reason'              => '',  // TODO: Брать причину из какого-то поля, еще не доделано
        ]);
    }


    /**
     * ___ Handle the FabricTaskRoll "updated" event.
     * @param FabricTaskRoll $fabricTaskRoll
     * @return void
     */
    public function updated(FabricTaskRoll $fabricTaskRoll): void
    {
        $currentStatus = $fabricTaskRoll->roll_status;
        $oldStatus = $fabricTaskRoll->getOriginal('roll_status');

        $currentPosition = $fabricTaskRoll->roll_position;
        $oldPosition = $fabricTaskRoll->getOriginal('roll_position');

        $check_1C = null;
        $uncheck_1C = null;

        $description = null;

        $responsible = $fabricTaskRoll->finish_by;

        $event = null;
        $reason = null;

        $isLog = true;  // __ Создавать ли лог на триггер

        if ($oldStatus !== $currentStatus) {

            if ($currentStatus === FABRIC_ROLL_FALSE_CODE) {            // __ Статус "Не выполнено"
                // $isLog = false;  // Не логируем
                $event = 'Установка статуса "Не выполнено"';
                $reason = $fabricTaskRoll->false_reason;
            } else if ($currentStatus === FABRIC_ROLL_CANCELLED_CODE) { // __ Статус "Отменено"
                $event = 'Отмена стегания рулона';
                $reason = $fabricTaskRoll->false_reason;
            } else if ($currentStatus === FABRIC_ROLL_DONE_CODE) {      // __ Статус "Выполнено"
                $event = 'Завершение стегания рулона';
            } else if ($currentStatus === FABRIC_ROLL_RUNNING_CODE) {   // __ Статус "В процессе"
                if ($oldStatus === FABRIC_ROLL_PAUSED_CODE) {
                    $event = 'Возобновление стегания рулона';
                } else if ($oldStatus === FABRIC_ROLL_CREATED_CODE) {
                    $event = 'Начало стегания рулона';
                }
            } else if ($currentStatus === FABRIC_ROLL_PAUSED_CODE) {    // __ Статус "Приостановлено"
                if ($oldStatus === FABRIC_ROLL_RUNNING_CODE) {
                    $event = 'Приостановка стегания рулона';
                } else if ($oldStatus === FABRIC_ROLL_ROLLING_CODE) {
                    $event = 'Возврат из статуса "Переходящий рулон"';
                }
            } else if ($currentStatus === FABRIC_ROLL_ROLLING_CODE) {   // __ Статус "Переходящий"
                $event = 'Отметка "Переходящий рулон"';
                $reason = $fabricTaskRoll->false_reason;
            } else if ($currentStatus === FABRIC_ROLL_CREATED_CODE) {   // __ Статус "Создано"
                if ($oldStatus === FABRIC_ROLL_CANCELLED_CODE) {        // __ Возврат из статуса "Отменено"
                    $event = 'Возврат из статуса "Отменено"';
                } else if ($oldStatus === FABRIC_ROLL_FALSE_CODE) {     // __ Возврат из статуса "Не выполнено"
                    $event = 'Возврат из статуса "Не выполнено"';
                }
            } else if ($currentStatus === FABRIC_ROLL_MOVED_CODE) {     // __ Статус "Перемещен на закрой"
                $event = 'Перемещение на закрой';
                $responsible = $fabricTaskRoll->move_to_cut_by;
            } else if ($currentStatus === FABRIC_ROLL_CLOSED_CODE) {    // __ Статус "Закрыт" (Списание)
                $event = 'Списание рулона';
            }

        } else {
            if ($currentPosition !== $oldPosition) { // TODO: Надо добавить еще и проверку присутствия причины для перемещения
                $isLog = false; // TODO: Не логируем, потому, что, когда рулоны падают вниз после статуса "Не выполнено" - куча записей
                $event = 'Изменение позиции рулона';
                // $reason = $fabricTaskRoll->false_reason; // TODO: Брать причину из какого-то поля, еще не доделано
            }

            if ($fabricTaskRoll->registration_1C_by !== 0 && $fabricTaskRoll->getOriginal('registration_1C_by') === 0) {
                $event = 'Регистрация рулона в 1С';
                $responsible = $fabricTaskRoll->registration_1C_by;
                $check_1C = true;
                $uncheck_1C = false;
            } else if ($fabricTaskRoll->registration_1C_by === 0 && $fabricTaskRoll->getOriginal('registration_1C_by') !== 0) {
                $event = 'Отмена регистрации рулона в 1С';
                $check_1C = false;
                $uncheck_1C = true;
            }

            if ($fabricTaskRoll->description !== $fabricTaskRoll->getOriginal('description')) {
                $event = 'Изменение описания рулона';
                $description = $fabricTaskRoll->description;
            }

            if ($fabricTaskRoll->finish_by !== $fabricTaskRoll->getOriginal('finish_by')) {
                $event = 'Изменение ответственного лица';
                $isLog = false; // Не логируем
            }

            if ($fabricTaskRoll->textile_roll_length !== $fabricTaskRoll->getOriginal('textile_roll_length')) {
                $event = 'Изменение длины рулона';
                $isLog = false; // Не логируем
            }

        }

        // __ Делаем такое условие, потому что при смене длины ткани и если оставить прежнее значение, то лог будет пустым
        if ($isLog && $event) {
            FabricTaskRollLog::query()->create([
                'fabric_task_roll_id'  => $fabricTaskRoll->id,
                'user_id'              => auth()->id(),
                'worker_id'            => $responsible,
                'event'                => $event,
                'reason'               => $reason,
                'status_before'        => $oldStatus,
                'status_after'         => $currentStatus,
                'roll_position_before' => $oldPosition,
                'roll_position_after'  => $currentPosition,
                'check_1C'             => $check_1C,
                'uncheck_1C'           => $uncheck_1C,
                'description'          => $description,
                'ip'                   => request()->ip(),
                // 'old_status' остается null для первого создания
            ]);
        }
    }

/*
    public function deleted(FabricTaskRoll $fabricTaskRoll): void
    {
    }

    public function restored(FabricTaskRoll $fabricTaskRoll): void
    {
    }
    public function forceDeleted(FabricTaskRoll $fabricTaskRoll): void
    {
    }
*/
}
