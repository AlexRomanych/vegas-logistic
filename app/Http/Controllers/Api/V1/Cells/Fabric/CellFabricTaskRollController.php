<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskRollCollection;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskRollMovingCollection;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskRollResource;

use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use App\Models\Manufacture\Cells\Fabric\FabricTaskContext;
use App\Models\Manufacture\Cells\Fabric\FabricTaskRoll;

// use App\Models\Manufacture\Cells\Fabric\FabricTasksDate;

use App\Services\Manufacture\FabricService;
use Illuminate\Http\Request;
use \Exception;
use Illuminate\Support\Facades\Auth;

// use Carbon\Carbon;
// use Illuminate\Support\Facades\DB;

class CellFabricTaskRollController extends Controller
{

    /**
     * ___ Возвращает рулоны за период. Если не указан - возвращает все рулоны
     * @param Request $request
     * @return FabricTaskRollCollection|string
     */
    public function taskRolls(Request $request)
    {
        try {

            // Если без параметров, возвращаем все заказы
            if (!$request->has('start') || !$request->has('end')) {
                return new FabricTaskRollCollection(FabricTaskRoll::all());
            }

            $validData = $request->validate([
                'start' => 'required|date|beforeOrEqual:end',
                'end' => 'required|date|afterOrEqual:start',
            ]);

            $rollsQuery = FabricTaskRoll::query()
                ->whereBetween('created_at', [$validData['start'], $validData['end']])
                ->orderBy('id')
                ->get();

            return new FabricTaskRollCollection($rollsQuery);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * Descr: Возвращает список не принятых на закрой рулонов (DONE или REGISTERED_1C или MOVED)
     * @param Request $request
     * @return FabricTaskRollMovingCollection
     */
    public function getNotAcceptedToCutRolls(Request $request)
    {
        $rollsQuery = FabricTaskRoll::query()
            ->where('roll_status', FABRIC_ROLL_DONE_CODE)
            ->orWhere('roll_status', FABRIC_ROLL_REGISTERED_1C_CODE)
            ->orWhere('roll_status', FABRIC_ROLL_MOVED_CODE)
            ->with([
                'fabric',
                'finishBy',
                'registration1CBy',
                'moveToCutBy',
                'receiptToCutBy',
                'user'])
            ->orderBy('id')
            ->get();

        return new FabricTaskRollMovingCollection($rollsQuery);
    }


    /**
     * ___ Обновляет данные по рулону.
     * @param Request $request
     * @return FabricTaskRollResource|string
     */
    public function update(Request $request)
    {
        try {

            $rollData = $request->data['data'];

            // TODO: Выполнить жесткую валидацию жестких данных

            $fabric = Fabric::query()->find($rollData['fabric_id']); // Находим нужную ткань
            if (!$fabric) {
                throw new Exception('Не найдена ткань c ID: ' . $rollData['fabric_id']);
            }


            $updateData = [
                'roll_status' => $rollData['status'],
                'roll_status_previous' => $rollData['status_prev'],
                'start_at' => $this->getUTCDateOrNull($rollData['start_at']),
                'paused_at' => $this->getUTCDateOrNull($rollData['paused_at']),
                'resume_at' => $this->getUTCDateOrNull($rollData['resume_at']),
                'finish_at' => $this->getUTCDateOrNull($rollData['finish_at']),
                'duration' => $rollData['duration'],
                'finish_by' => $rollData['finish_by'],
                'rolling' => $rollData['rolling'],
                'description' => $rollData['descr'],
                'false_reason' => $rollData['false_reason'],
                'textile_roll_length' => $rollData['textile_length'],
                'fabric_roll_length' => (float)$rollData['rate'] === 0.0 ? 0.0 : $rollData['textile_length'] / $rollData['rate'] / $fabric->textile_layers_amount,

                'user_id' => Auth::id(),    // Текущий пользователь
            ];


            $taskRoll = FabricTaskRoll::query()->updateOrCreate(
                ['id' => $rollData['id']],
                $updateData

            );


            if (!$taskRoll) {
                throw new Exception('Не удалось обновить рулон');
            }

            // __ Тут логика обновления рулона. Если статус рулона DONE - необходимо обновить буфер ткани
            if ($taskRoll->roll_status === FABRIC_ROLL_DONE_CODE) {

                // $fabric = Fabric::query()->find($taskRoll->fabric_id); // Находим нужную ткань

                if ($fabric) {

                    if ($fabric->translate_rate === 0) {
                        throw new Exception('У ' . $fabric->display_name . ' отсутствует коэффициент перевода!');
                    }

                    $fabric->buffer_amount += $taskRoll->textile_roll_length / $fabric->translate_rate;


                    // __ Тут обновляем статистику по средней длине ткани, если стоит галка
                    if ($fabric->average_roll_length_from_statistic) {
                        $averageLength = FabricService::getFabricAverageLength($fabric->id);
                        if ($averageLength !== 0.0) {
                            $fabric->average_roll_length = $averageLength;
                            $fabric->average_roll_length_statistic = $averageLength;
                        }
                    }

                    $fabric->save();

                } else {
                    throw new Exception('Не найдена ткань по ID: ' . $taskRoll->fabric_id);
                }

            }

            return new FabricTaskRollResource($taskRoll);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    /**
     * ___ Задает статус рулона как зарегистрированный в 1С
     * ___ В новой версии убираем статус регистрации в 1С и оставляем его как признак
     * @param Request $request
     * @return string
     */
    public function setRollRegisteredStatus(Request $request)
    {
        try {

            $result = $request->validate([
                'data' => 'required|array',
                'data.id' => 'required|numeric',
                'data.status' => 'required|boolean'     // Тут ожидаем либо true или false
            ]);

            $roll = FabricTaskRoll::query()->find($result['data']['id']);
            if (!$roll) {
                return EndPointStaticRequestAnswer::fail(response()->json(['message' => 'Рулон не найден']));
            }

            $status = $roll->roll_status;

            // __ Ставим в 1С только если статус "Выполнен" или "Перемещен на закрой"
            if ($status !== FABRIC_ROLL_MOVED_CODE && $status !== FABRIC_ROLL_DONE_CODE) {
                throw new Exception('Некорректный статус');
            }

            $roll->isRegistered = $result['data']['status'];
            // $dirtyAttributes = $roll->getDirty();

            $roll->save();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }

    }


    /**
     * Descr: Задает статус рулона как зарегистрированный в 1С
     * @param Request $request
     * @return string
     */
    public function setRollRegisteredStatus_Old(Request $request)
    {
        try {
            $result = $request->validate([
                'data' => 'required|array',
                'data.id' => 'required|numeric',
                'data.status' => 'required|numeric'
            ]);

            $status = $result['data']['status'];
            if ($status !== FABRIC_ROLL_REGISTERED_1C_CODE && $status !== FABRIC_ROLL_DONE_CODE) {
                throw new Exception('Неизвестный статус');
            }

            $roll = FabricTaskRoll::query()->find($result['data']['id']);

            if (!$roll) {
                return EndPointStaticRequestAnswer::fail(response()->json(['message' => 'Рулон не найден']));
            }

            if ($roll->roll_status !== FABRIC_ROLL_REGISTERED_1C_CODE && $roll->roll_status !== FABRIC_ROLL_DONE_CODE) {
                return EndPointStaticRequestAnswer::fail(response()->json(['message' => 'Статус не соответствует действительности']));
            }

            if ($status === FABRIC_ROLL_REGISTERED_1C_CODE) {
                $result = $roll->update([
                    'roll_status' => FABRIC_ROLL_REGISTERED_1C_CODE,
                    'registration_1C_by' => Auth::id(),
                    'registration_1C_at' => now(),
                ]);
            } else {
                $result = $roll->update([
                    'roll_status' => FABRIC_ROLL_DONE_CODE,
                    'registration_1C_by' => 0,
                    'registration_1C_at' => null,
                ]);
            }

            if ($result) throw new Exception('Не удалось обновить статус');

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }

    }


    /**
     * Descr: Задает статус рулона как перемещенный на закрой
     * @param Request $request
     * @return string
     */
    public function setRollMovedStatus(Request $request)
    {
        // try {
            $result = $request->validate([
                'data' => 'required|array',
                'data.id' => 'required|numeric',
                'data.status' => 'required|numeric'
            ]);

            $status = $result['data']['status'];


            // __ Перемещенный на закрой в одном направлении
            // if ($status !== FABRIC_ROLL_MOVED_CODE) {
            //     throw new Exception('Неверный статус');
            // }

            // __ Перемещенный на закрой в двух направлениях
            // if ($status !== FABRIC_ROLL_DONE_CODE && $status !== FABRIC_ROLL_MOVED_CODE) {
            //     throw new Exception('Неверный статус');
            // }

            $roll = FabricTaskRoll::query()->find($result['data']['id']);

            if (!$roll) {
                throw new Exception('Рулон не найден');
            }

            // __ Перемещенный на закрой в одном направлении
            if ($roll->roll_status === FABRIC_ROLL_MOVED_CODE) {
                throw new Exception('Рулон уже перемещен на закрой');
            }

            if ($roll->roll_status !== FABRIC_ROLL_DONE_CODE && $roll->roll_status !== FABRIC_ROLL_REGISTERED_1C_CODE) {
                throw new Exception('Неверный статус');
            }

            // __ Перемещенный на закрой в двух направлениях
            // if ($roll->roll_status !== FABRIC_ROLL_DONE_CODE && $roll->roll_status !== FABRIC_ROLL_MOVED_CODE) {
            //     throw new Exception('Неверный статус');
            // }

            $delta = $roll->textile_roll_length / $roll->translate_rate; // +/- количество к буферу

            // __ Перемещенный на закрой в одном направлении
            $result = $roll->update([
                'roll_status' => FABRIC_ROLL_MOVED_CODE,
                'move_to_cut_by' => Auth::id(),
                'move_to_cut_at' => now(),
            ]);

            // __ Перемещенный на закрой в двух направлениях
            // if ($status === FABRIC_ROLL_MOVED_CODE) {
            //     $result = $roll->update([
            //         'roll_status' => FABRIC_ROLL_MOVED_CODE,
            //         'move_to_cut_by' => Auth::id(),
            //         'move_to_cut_at' => now(),
            //     ]);
            //     $delta = -$delta;
            //
            // } else {
            //     $result = $roll->update([
            //         'roll_status' => FABRIC_ROLL_REGISTERED_1C_CODE,
            //         'move_to_cut_by' => 0,
            //         'move_to_cut_at' => null,
            //     ]);
            //
            // }

            if (!$result) throw new Exception('Не удалось обновить статус');

            // __Изменяем количество в буфере ткани в зависимости от статуса
            $fabric = Fabric::query()->find($roll->fabric_id);

            if (!$fabric) throw new Exception('Не удалось найти ткань');

            $fabric->buffer_amount += $delta;
            $fabric->save();

            return EndPointStaticRequestAnswer::ok();
        // } catch (Exception $e) {
        //     return EndPointStaticRequestAnswer::fail(response()->json($e));
        // }

    }

    public function setRollMovedStatus_Old(Request $request)
    {
        try {
            $result = $request->validate([
                'data' => 'required|array',
                'data.id' => 'required|numeric',
                'data.status' => 'required|numeric'
            ]);

            $status = $result['data']['status'];
            if ($status !== FABRIC_ROLL_REGISTERED_1C_CODE && $status !== FABRIC_ROLL_MOVED_CODE) {
                throw new Exception('Неизвестный статус');
            }

            $roll = FabricTaskRoll::query()->find($result['data']['id']);

            if (!$roll) {
                return EndPointStaticRequestAnswer::fail(response()->json(['message' => 'Рулон не найден']));
            }

            if ($roll->roll_status !== FABRIC_ROLL_REGISTERED_1C_CODE && $roll->roll_status !== FABRIC_ROLL_MOVED_CODE) {
                return EndPointStaticRequestAnswer::fail(response()->json(['message' => 'Статус не соответствует действительности']));
            }

            $delta = $roll->textile_roll_length / $roll->translate_rate; // +/- количество к буферу
            if ($status === FABRIC_ROLL_MOVED_CODE) {
                $result = $roll->update([
                    'roll_status' => FABRIC_ROLL_MOVED_CODE,
                    'move_to_cut_by' => Auth::id(),
                    'move_to_cut_at' => now(),
                ]);
                $delta = -$delta;

            } else {
                $result = $roll->update([
                    'roll_status' => FABRIC_ROLL_REGISTERED_1C_CODE,
                    'move_to_cut_by' => 0,
                    'move_to_cut_at' => null,
                ]);

            }

            if (!$result) throw new Exception('Не удалось обновить статус');

            // attract: Изменяем количество в буфере ткани в зависимости от статуса
            $fabric = Fabric::query()->find($roll->fabric_id);

            if (!$fabric) throw new Exception('Не удалось найти ткань');

            $fabric->buffer_amount += $delta;
            $fabric->save();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }

    }

    // line -----------------------------------------------------------------------------------


    // line -----------------------------------------------------------------------------------
    // line -------------------- Создает рулон во время выполнения СЗ -------------------------
    // line -----------------------------------------------------------------------------------
    /**
     * ___ Создает рулон во время выполнения СЗ
     * @param Request $request
     * @return string
     */
    public function addExecuteRoll(Request $request)
    {
        try {
            // __ Валидация данных
            $request->validate([
                'data' => 'required|array',
                'data.taskId' => 'required|numeric',
                'data.machineId' => 'required|numeric',
                'data.fabricId' => 'required|numeric',
                'data.falseReason' => 'required|string',
            ]);

            // __ Находим СЗ на данной СМ, чтобы добавить контекст - FabricTaskContext
            $task = FabricTask::query()
                ->where('fabric_tasks_date_id', $request->data['taskId'])
                ->where('fabric_machine_id', $request->data['machineId'])
                ->first();
            if (!$task) throw new Exception('Не найдено СЗ');


            // __ Находим максимальный индекс контекстного рулона
            $maxContextPosition = FabricTaskContext::query()
                ->where('fabric_task_id', $task->id)
                ->max('roll_position');

            // __ Получаем список СЗ на данной СМ, для определения порядка рулонов контекста
            $taskContexts = FabricTaskContext::query()
                ->where('fabric_task_id', $task->id)
                ->orderBy('roll_position')
                ->with('fabricTaskRolls')
                ->get();

            // __ Находим максимальную позицию рулона в контексте
            $maxRollPosition = 0;

            foreach ($taskContexts as $taskContext) {
                foreach ($taskContext->fabricTaskRolls as $execRoll) {
                    $maxRollPosition = max($maxRollPosition, $execRoll->roll_position);
                }
            }

            // __ Находим ПС, для добавления в контекст
            $fabric = Fabric::query()->find($request->data['fabricId']);
            if (!$fabric) throw new Exception('Не найдена ПС');

            // __ Создаем контекст для задания
            $taskContext = FabricTaskContext::query()->create([
                'fabric_task_id' => $task->id,
                'fabric_id' => $fabric->id,
                'roll_position' => ++$maxContextPosition,       // Вставляем найденную позицию
                'average_textile_length' => $fabric->average_roll_length * $fabric->textile_layers_amount,
                'translate_rate' => $fabric->translate_rate,
                'productivity' => $fabric->productivity,
                'rolls_amount' => 1,
                'description' => 'Создан в процессе выполнения СЗ.'
                // 'note' => 'Создан в процессе выполнения СЗ. ' . (Carbon::parse($contextDate))->format('d.m.Y'),
            ]);

            // __ Создаем рулон
            $roll = FabricTaskRoll::query()->create([
                'fabric_task_context_id' => $taskContext->id,
                'fabric_id' => $fabric->id,
                'roll_position' => ++$maxRollPosition,
                'roll_status' => FABRIC_ROLL_CREATED_CODE,
                'textile_roll_length' => $fabric->average_roll_length * $fabric->textile_layers_amount,
                'fabric_roll_length' => (float)$fabric->translate_rate === 0.0 ? 0.0 :  $fabric->average_roll_length / $fabric->translate_rate,
                'translate_rate' => $fabric->translate_rate,
                'productivity' => $fabric->productivity,
                'description' => 'Создан в процессе выполнения',   // дописываем плановый комментарий
                'false_reason' => $request->data['falseReason'],
                'note' => $request->data['falseReason'],
                'comment' => $request->data['falseReason']
            ]);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    public function addExecuteRoll_Old(Request $request)
    {
        try {
            // attract: Валидация данных
            $request->validate([
                'data' => 'required|array',
                'data.taskId' => 'required|numeric',
                'data.machineId' => 'required|numeric',
                'data.fabricId' => 'required|numeric',
                'data.falseReason' => 'required|string',
            ]);

            // attract: Находим СЗ на данной СМ, чтобы добавить контекст - FabricTaskContext
            $task = FabricTask::query()
                ->where('fabric_tasks_date_id', $request->data['taskId'])
                ->where('fabric_machine_id', $request->data['machineId'])
                ->first();
            if (!$task) throw new Exception('Не найдено СЗ');

            // __ Получаем список СЗ на данной СМ, для определения порядка рулонов контекста
            $taskContexts = FabricTaskContext::query()
                ->where('fabric_task_id', $task->id)
                ->orderBy('roll_position')
                ->with('fabricTaskRolls')
                ->get();
            if (!$taskContexts) throw new Exception('Не удалось получить контексты СЗ');

            // attract: Находим позицию для добавления контекстного рулона
            $insertPosition = $taskContexts[count($taskContexts) - 1]->roll_position + 1;  // Предположим, что это будет последний рулон
            $offset = count($taskContexts) + 1;

            // __ Находим тот TaskContext, в котором есть только созданные рулоны и вставляем перед ним
            foreach ($taskContexts as $index => $taskContext) {

                $allCreatedRolls = true;
                foreach ($taskContext->fabricTaskRolls as $execRoll) {
                    if ($execRoll->roll_status !== FABRIC_ROLL_CREATED_CODE) {
                        $allCreatedRolls = false;
                        break;
                    }
                }

                if ($allCreatedRolls) {
                    $insertPosition = $taskContext->roll_position;
                    $offset = $index;
                    break;
                }
            }


            // attract: Перемещаем позицию всех контекстных рулонов вперед
            for ($i = $offset; $i < count($taskContexts); $i++) {
                $taskContext = $taskContexts[$i];
                $taskContext->roll_position += 1;
                $taskContext->save();
            }

            //            return ['contexsts' => $taskContexts];

            // attract: Находим ПС, для добавления в контекст
            $fabric = Fabric::query()->find($request->data['fabricId']);
            if (!$fabric) throw new Exception('Не найдена ПС');

            // attract: Создаем контекст для задания
            $taskContext = FabricTaskContext::query()->create([
                'fabric_task_id' => $task->id,
                'fabric_id' => $fabric->id,
                'roll_position' => $insertPosition,       // Вставляем найденную позицию
                'average_textile_length' => $fabric->average_roll_length,
                'translate_rate' => $fabric->translate_rate,
                'productivity' => $fabric->productivity,
                'rolls_amount' => 1,
                'description' => 'Создан в процессе выполнения СЗ.'
                //            'note' => 'Создан в процессе выполнения СЗ. ' . (Carbon::parse($contextDate))->format('d.m.Y'),
            ]);
            if (!$taskContext) throw new Exception('Не удалось создать контекст СЗ.');

            // attract: Создаем рулон
            $roll = FabricTaskRoll::query()->create([
                'fabric_task_context_id' => $taskContext->id,
                'fabric_id' => $fabric->id,
                'roll_position' => 0,
                'roll_status' => FABRIC_ROLL_CREATED_CODE,
                'textile_roll_length' => $fabric->average_roll_length,
                'translate_rate' => $fabric->translate_rate,
                'productivity' => $fabric->productivity,
                'description' => 'Создан в процессе выполнения',   // дописываем плановый комментарий
                'false_reason' => $request->data['falseReason'],
                'note' => $request->data['falseReason'],
                'comment' => $request->data['falseReason']
            ]);


            // __ Обновляем позиции в физических рулонах в СЗ
            // __ Получаем список СЗ на данной СМ, для определения порядка рулонов контекста
            $taskContexts = FabricTaskContext::query()
                ->where('fabric_task_id', $task->id)
                ->orderBy('roll_position')
                ->with('fabricTaskRolls')
                ->get();

            $count = EXEC_ROLL_START_ORDER_INDEX;
            foreach ($taskContexts as $index => $taskContext) {
                foreach ($taskContext->fabricTaskRolls as $execRoll) {
                    $execRoll->update(['roll_position' => $count++]);
                }
            }

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }

    }

    // line -----------------------------------------------------------------------------------


    /**
     *  ___ Меняем порядок исполняемых рулонов
     * @param Request $request
     * @return string
     */
    public function saveExecuteRollsOrder(Request $request)
    {
        try {
            // __ Валидация данных
            $validData = $request->validate([
                'rolls' => 'required|array',
                'reason' => 'required|string'
            ]);

            foreach ($validData['rolls'] as $roll) {

                // FabricTaskRoll::query()->find($roll['id'])->update(['roll_position' => $roll['position']]);

                $targetRoll = FabricTaskRoll::query()->find($roll['id']);

                if (!$targetRoll) throw new Exception('Не удалось найти рулон c id = ' . $roll['id']);

                if ($targetRoll->roll_position !== $roll['position']) {
                    $targetRoll->roll_position = $roll['position'];
                    $targetRoll->save();

                    // TODO: Добавить логирование c указанием причины ($reason)

                }
            }

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    /**
     * ___ Обновляем комментарий к рулону
     * @param Request $request
     * @return string
     */
    public function updateRollComment(Request $request)
    {
        try {
            $validDate = $request->validate([
                'data.id' => 'required|numeric',
                'data.comment' => 'string|nullable', // Позволяет пустую строку, но ключ 'comment' должен присутствовать
                // 'data.comment' => 'sometimes|string', // Правило sometimes валидирует поле только в том случае, если оно присутствует в полезной нагрузке запроса.
                // 'data.comment' => 'required|nullable', // Позволяет пустую строку, но ключ 'comment' должен присутствовать
            ]);

            $payload = $validDate['data'];

            FabricTaskRoll::query()->find($payload['id'])->update(['comment' => $payload['comment']]);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    // __ Вспомогалочка. Или Null, или правильная дата (+3 часа)
    private function getUTCDateOrNull($date)
    {
        if (is_null($date)) return null;
        else return correctTimeZone($date);
    }


}

/*
descr: Это поля объекта выполняемого рулона
{
    "id": 23,
    "fabric_id": 9,
    "position": 2,
    "status": 4,
    "start_at": null,
    "paused_at": null,
    "resume_at": null,
    "finish_at": null,
    "duration": null,
    "finish_by": 0,
    "rolling": false,
    "textile_length": 68.63,
    "productivity": 47.61904762,
    "descr": null,
    "active": true
}
*/
