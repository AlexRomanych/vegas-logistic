<?php

namespace App\Http\Controllers\Api\V1\Cells\Assembly;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Assembly\Manage\AssemblyTaskLineResource;
use App\Models\Manufacture\Cells\Assembly\AssemblyTaskLine;
use App\Models\Models\Model;
use App\Services\Manufacture\AssemblyService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class AssemblyTaskLineController extends Controller
{

    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // !!! ---             Assembly Lines                  !!!
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

    /**
     * ___ Меняем Линию Сборки для Записи СЗ Сборки
     * @param Request $request
     * @return string
     */
    public function taskLinesManufLineSet(Request $request)
    {
        try {
            //$all = $request->all();

            $validated = $request->validate([
                // __ Проверяем, что 'data' — это обязательный, не пустой массив
                'data'        => 'required|array|min:1',

                // __ Проверяем ID внутри каждого элемента массива
                'data.*.id'   => 'required|integer|exists:assembly_task_lines,id',

                // __ Проверяем строку 'table' на соответствие конкретным значениям
                'data.*.line' => [
                    'required',
                    'string',
                    Rule::in([
                        Model::ASSEMBLY_LINE_LAMIT,
                        Model::ASSEMBLY_LINE_TABLE,
                    ]),
                ],
            ]);

            // __ Валидация с кастомными сообщениями
            //$validated = $request->validate([
            //    'data' => 'required|array|min:1',
            //    'data.*.id' => 'required|integer|exists:assembly_task_lines,id',
            //    'data.*.table' => ['required', 'string', Rule::in([AssemblyTaskLine::FIELD_TABLE_1, AssemblyTaskLine::FIELD_TABLE_2, AssemblyTaskLine::FIELD_TABLE_3])],
            //], [
            //    'data.required' => 'Массив данных обязателен для заполнения.',
            //    'data.min' => 'Массив данных не должен быть пустым.',
            //    'data.*.id.exists' => 'Выбранный ID задачи не существует в базе данных.',
            //    'data.*.table.in' => 'Поле table должно принимать значения: table_1, table_2 или table_3.',
            //]);

            $data = $validated['data'];
            DB::transaction(function () use ($data) {
                foreach ($data as $item) {
                    $line = AssemblyTaskLine::query()->find($item['id']);
                    if (!$line) {
                        throw new Exception('Missing assembly task line with id: ' . $item['id'] . '.');
                    }
                    $line->assembly_line = $item['line'];
                    $line->save();
                }
            });


            // __ Скрипт для обновления одним запросом
            //// Строим сырой запрос для массового обновления (Bulk Update)
            //// Формируем плейсхолдеры (?, ?) для каждого элемента массива данных
            //$valuePairs = array_map(function () {
            //    return '(?, ?)';
            //}, $data);
            //
            //$valuesSql = implode(', ', $valuePairs);
            //
            //// Собираем все значения в один плоский массив для безопасной привязки параметров (SQL Injection Protection)
            //$bindings = [];
            //foreach ($data as $item) {
            //    $bindings[] = $item['id'];
            //    $bindings[] = $item['table'];
            //}
            //
            //// Итоговый SQL-запрос для PostgreSQL
            ///** @noinspection SqlDialectInspection */
            //$query = "
            //    UPDATE assembly_task_lines AS c
            //    SET \"table\" = v.new_table
            //    FROM (VALUES {$valuesSql}) AS v(id, new_table)
            //    WHERE c.id = CAST(v.id AS INTEGER)
            //";
            //
            //// Выполняем одним запросом внутри транзакции
            //DB::transaction(function () use ($query, $bindings) {
            //    DB::update($query, $bindings);
            //});


            return EndPointStaticRequestAnswer::ok('Изменено успешно');
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Устанавливаем статус Выполнено для Записи СЗ Сборки
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function setAssemblyTaskLinesDone(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids'   => 'required|array',
                'ids.*' => 'required|integer|exists:assembly_task_lines,id',
            ]);

            // __ Проверяем на то, что все Строки не имеют незавершенных Sectors
            $assemblyTaskLines = AssemblyTaskLine::query()
                ->whereIn('id', $validated['ids'])
                ->with(['sectors:id,assembly_task_line_id,finished_at,false_at'])
                ->get();

            $allFinishedIds = [];

            foreach ($assemblyTaskLines as $assemblyTaskLine) {
                $allFinished = true;
                foreach ($assemblyTaskLine->sectors as $sector) {
                    if (is_null($sector->finished_at)) {
                        $allFinished = false;
                        break;
                    }
                }

                if ($allFinished) {
                    $assemblyTaskLine->finished_at = now();
                    $assemblyTaskLine->save();
                    $allFinishedIds[] = $assemblyTaskLine->id;
                }
            }


            //foreach ($validated['ids'] as $id) {
            //    $line = AssemblyTaskLine::query()->find($id);
            //    if (!$line) {
            //        throw new Exception('Missing Assembly Task Line with id: ' . $id . '.');
            //    }
            //
            //    $line->finished_at = now();
            //    $line->save();
            //}

            // __ Устанавливаем статус самого СЗ
            AssemblyService::setAssemblyTaskStatus($allFinishedIds, false);
            //AssemblyService::setAssemblyTaskStatus($validated['ids'], false);

            $sectors = AssemblyTaskLine::query()->whereIn('id', $allFinishedIds)->get();
            //$sectors = AssemblyTaskLine::query()->whereIn('id', $validated['ids'])->get();
            return AssemblyTaskLineResource::collection($sectors);
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Устанавливаем статус Не выполнено для Записи СЗ Сборки
     * @param Request $request
     * @return AnonymousResourceCollection|string
     * @noinspection DuplicatedCode
     */
    public function setAssemblyTaskLinesFalse(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids'    => 'required|array',
                'ids.*'  => 'required|integer|exists:assembly_task_lines,id',
                'reason' => 'required|string',
            ]);

            foreach ($validated['ids'] as $id) {
                $line = AssemblyTaskLine::query()->find($id);
                if (!$line) {
                    throw new Exception('Missing Assembly Task Line with id: ' . $id . '.');
                }

                $line->false_at     = now();
                $line->false_reason = $validated['reason'];

                $history = $line->false_history;
                if (is_null($history)) {
                    $history = [];
                }

                $history[]           = [
                    'at'     => $line->false_at->format(RETURN_DATE_TIME_FORMAT),
                    'by'     => auth()->id(),
                    'reason' => $validated['reason'],
                ];
                $line->false_history = $history;
                $line->finished_at   = null;
                $line->save();
            }

            // __ Устанавливаем статус самого СЗ
            AssemblyService::setAssemblyTaskStatus($validated['ids'], false);

            $lines = AssemblyTaskLine::query()->whereIn('id', $validated['ids'])->get();
            return AssemblyTaskLineResource::collection($lines);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Сбрасываем отметку Выполнено/Не выполнено для Записи СЗ Сборки
     * @param Request $request
     * @return AnonymousResourceCollection|string
     */
    public function setAssemblyTaskLinesReset(Request $request)
    {
        try {
            $validated = $request->validate([
                'ids'   => 'required|array',
                'ids.*' => 'required|integer|exists:assembly_task_lines,id',
            ]);

            foreach ($validated['ids'] as $id) {
                $line = AssemblyTaskLine::query()->find($id);
                if (!$line) {
                    throw new Exception('Missing Assembly Task Line with id: ' . $id . '.');
                }

                $line->finished_at  = null;
                $line->false_at     = null;
                $line->false_reason = null;
                $line->save();
            }

            // __ Устанавливаем статус самого СЗ
            AssemblyService::setAssemblyTaskStatus($validated['ids'], false);

            $lines = AssemblyTaskLine::query()->whereIn('id', $validated['ids'])->get();
            return AssemblyTaskLineResource::collection($lines);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Обновляем Комментарий для Записи СЗ Сборки
     * @param Request $request
     * @return string
     */
    public function setAssemblyTaskLineDescription(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'          => 'required|integer|exists:assembly_task_lines,id',
                'description' => 'nullable|string',
            ]);

            $description = $validated['description'] ?? null;
            AssemblyTaskLine::query()
                ->where('id', $validated['id'])
                ->update(['description' => $description]);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception|Throwable $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


}
