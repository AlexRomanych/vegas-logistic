<?php

namespace App\Services\Manufacture;

// Сервис для Полотен стеганных
use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTaskRollResource;
use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricMachine;
use App\Models\Manufacture\Cells\Fabric\FabricPicture;
use App\Models\Manufacture\Cells\Fabric\FabricPictureSchema;
use App\Models\Manufacture\Cells\Fabric\FabricTask;
use App\Models\Manufacture\Cells\Fabric\FabricTaskContext;
use App\Models\Manufacture\Cells\Fabric\FabricTaskRoll;
use App\Models\Manufacture\Cells\Fabric\FabricTasksDate;
use App\Models\Manufacture\Cells\Fabric\FabricTuningTime;
use Carbon\Carbon;
use Exception;

// use Illuminate\Http\Request;
// use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// use Illuminate\Support\Collection;

final class FabricService
{
    private static array $fabricsNameCache = [];                       // Кэш ПС по имени
    private static array $fabricPicsNameCache = [];                    // Кэш рисунков ПС по имени
    // private static array $fabricIdsCache = [];                         // Кэш рисунков ПС по ID
    private static array $fabricMachinesNameCache = [];                // Кэш стегальных машин
    private static array $fabricPicSchemasNameCache = [];              // Кэш схем мгл рисунков

    // hr-----------------------------------------------------------------------------
    // attract: ПС
    /**
     * Получаем ПС по имени
     * @param string $name
     * @return Fabric|null
     */
    public static function getFabricByName(string $name): ?Fabric
    {

        if (count(self::$fabricsNameCache) === 0) {
            self::cacheFabricsByName();
        }

        if (isset(self::$fabricsNameCache[$name])) {
            return self::$fabricsNameCache[$name];
        }

        return null;
    }

    // Кэшируем ПС: ключ - имя, значение - объект ПС
    private static function cacheFabricsByName(): void
    {
        $fabrics = Fabric::all();

        foreach ($fabrics as $fabric) {
            self::$fabricsNameCache[$fabric->name] = $fabric;
        }

    }

    // hr-----------------------------------------------------------------------------
    // attract: рисунки ПС

    /**
     * ___ Получаем рисунок по имени
     * @param string $name
     * @return FabricPicture|null
     */
    public static function getFabricPicByName(string $name): ?FabricPicture
    {
        if (count(self::$fabricPicsNameCache) === 0) {
            self::cacheFabricPicsByName();
        }

        if (isset(self::$fabricPicsNameCache[$name])) {
            return self::$fabricPicsNameCache[$name];
        }

        return null;
    }


    /**
     * __ Кэшируем рисунки: ключ - имя, значение - объект рисунка
     * @return void
     */
    private static function cacheFabricPicsByName(): void
    {
        $fabricPics = FabricPicture::all();

        foreach ($fabricPics as $fabricPic) {
            self::$fabricPicsNameCache[$fabricPic->name] = $fabricPic;
        }
    }

    // hr-----------------------------------------------------------------------------
    // attract: стегальные машины

    /**
     * Получаем стегальную машину по сокращенному имени
     * @param string $shortName
     * @return FabricMachine|null
     */
    public static function getFabricMachineByShortName(string $shortName): ?FabricMachine
    {
        if (count(self::$fabricMachinesNameCache) === 0) {
            self::cacheFabricMachinesByShortName();
        }

        if (isset(self::$fabricMachinesNameCache[$shortName])) {
            return self::$fabricMachinesNameCache[$shortName];
        }

        return null;
    }


    /**
     * Кэшируем стегальные машины: ключ - сокращенное имя, значение - объект стегальной машины
     * @return void
     */
    private static function cacheFabricMachinesByShortName(): void
    {
        $fabricMachines = FabricMachine::all();
        foreach ($fabricMachines as $fabricMachine) {
            self::$fabricMachinesNameCache[$fabricMachine->short_name] = $fabricMachine;
        }
    }


    // hr-----------------------------------------------------------------------------
    // attract: схемы игл

    /**
     * Получаем схему по имени
     * @param string $schema
     * @return FabricPictureSchema|null
     */
    public static function getFabricPicSchemaByName(string $schema): ?FabricPictureSchema
    {
        if (count(self::$fabricPicSchemasNameCache) === 0) {
            self::cacheFabricPicSchemasByName();
        }

        if (isset(self::$fabricPicSchemasNameCache[$schema])) {
            return self::$fabricPicSchemasNameCache[$schema];
        }

        return null;
    }


    /**
     * Кэшируем схемы: ключ - имя, значение - объект схемы
     * @return void
     */
    private static function cacheFabricPicSchemasByName(): void
    {
        $picSchemas = FabricPictureSchema::all();
        foreach ($picSchemas as $picSchema) {
            self::$fabricPicSchemasNameCache[$picSchema->schema] = $picSchema;

        }
    }


    /**
     * ___ Получаем название стегальной машины по ее ID
     * @param int $machineId
     * @return string
     */
    public static function getFabricMachineNameById(int $machineId): string
    {
        return match ($machineId) {
            FABRIC_MACHINE_UNKNOWN_ID => FABRIC_MACHINE_UNKNOWN_TITLE,
            FABRIC_MACHINE_AMERICAN_ID => FABRIC_MACHINE_AMERICAN_TITLE,
            FABRIC_MACHINE_GERMAN_ID => FABRIC_MACHINE_GERMAN_TITLE,
            FABRIC_MACHINE_CHINA_ID => FABRIC_MACHINE_CHINA_TITLE,
            FABRIC_MACHINE_KOREAN_ID => FABRIC_MACHINE_KOREAN_TITLE,
            default => 'error'
        };
    }

    /**
     * Получаем ID стегальной машины по ее имени
     * @param string $machineName
     * @return int
     */
    public static function getFabricMachineIdByName(string $machineName): int
    {
        return match (strtolower($machineName)) {
            FABRIC_MACHINE_UNKNOWN_TITLE => FABRIC_MACHINE_UNKNOWN_ID,
            FABRIC_MACHINE_AMERICAN_TITLE => FABRIC_MACHINE_AMERICAN_ID,
            FABRIC_MACHINE_GERMAN_TITLE => FABRIC_MACHINE_GERMAN_ID,
            FABRIC_MACHINE_CHINA_TITLE => FABRIC_MACHINE_CHINA_ID,
            FABRIC_MACHINE_KOREAN_TITLE => FABRIC_MACHINE_KOREAN_ID,
            default => 0
        };
    }

    /**
     * Получаем номер бригады стегальной машины по дате
     * @param string|Carbon $date
     * @return int
     */
    public static function getFabricTeamChangeNumberByDate(string|Carbon $date): int
    {

        // descr: Дата начала первой смены первой бригады
        $referenceDate = Carbon::parse('2025-03-26');
        $targetDate = Carbon::parse($date);

        $diffInDays = abs($referenceDate->diffInDays($targetDate));

        $remainsOfDivision = $diffInDays % 4;

        if ($remainsOfDivision === 0 || $remainsOfDivision === 1) return 1;
        return 2;
    }


    /**
     * ___ Проверяем порядок следования рулонов ОПП (контекста) - в зависимости от статуса
     * ___ "Переходящий", "Не выполнено" и т.д.
     * @param FabricTask $task
     * @param array $inContext
     * @return array
     */
    public static function checkContextOrder(FabricTask $task, array $inContext): array
    {
        $a = $inContext;
        $taskContext = $task->fabricTaskContexts->toArray(request());
        usort($taskContext, fn($a, $b) => $a['roll_position'] <=> $b['roll_position']);
        usort($inContext, fn($a, $b) => $a['roll_position'] <=> $b['roll_position']);


        $resultArray = [];
        $count = 1;
        foreach ($taskContext as $context) {

            // __ Находим неперемещяемые рулоны
            if (!$context['editable']) {
                $resultArray[] = $context;
                $count++;

                foreach ($inContext as $index => $inContextItem) {
                    if ($inContextItem['id'] === $context['id']) {
                        $inContext[$index]['id'] = 0;
                        break;
                    }
                }

            };
        }

        foreach ($inContext as $inContextItem) {
            if ($inContextItem['id'] !== 0) {
                $inContextItem['roll_position'] = $count;
                $count++;
                $resultArray[] = $inContextItem;
            }
        }

        return $resultArray;
    }

    /**
     * __ Обновляем порядок следования рулонов
     * @param int $taskId
     * @return void
     */
    public static function reindexOrderContexts(int $taskId): void
    {
        try {

            // __ Получаем контексты по ID задачи
            $taskContexts = FabricTaskContext::query()
                ->where('fabric_task_id', $taskId)
                ->get();

            if ($taskContexts->isEmpty()) return;

            // __ Сортируем по позиции
            $taskContexts->sortBy('roll_position');

            DB::transaction(function () use ($taskContexts) {

                // __ Обновляем с переиндексированием
                $taskContexts->each(fn($context, $index) => $context->update(['roll_position' => $index + 1]));
            });

        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }

    }


    /**
     * ___ Возвращаем среднюю длину рулона по периоду
     * @param int $fabricId
     * @param int $period
     * @return float|string
     */
    public static function getFabricAverageLength(int $fabricId, int $period = FABRIC_STATISTIC_PERIOD): float|string
    {
        try {

            // __ Получаем все статусы рулонов, которые нам интересны
            $statuses = [
                FABRIC_ROLL_DONE_CODE,
                FABRIC_ROLL_MOVED_CODE,
                FABRIC_ROLL_ACCEPTED_CODE,
                FABRIC_ROLL_CLOSED_CODE
            ];

            // __ Находим ПС
            $fabric = Fabric::query()->find($fabricId);
            if (!$fabric) throw new Exception("ПС c id = $fabricId не найдено");

            // __ Находим самый свежий рулон
            $lastModel = FabricTaskRoll::query()
                ->where('fabric_id', $fabricId)
                ->whereIn('roll_status', $statuses)
                ->latest()
                ->first();

            // __ Альтернативный подход
            /*
            $lastModel = FabricTaskRoll::query()
                ->where('fabric_id', $fabricId)
                ->whereIn('status', $statuses)
                ->orderBy('created_at', 'desc')
                ->first();
            */

            if (!$lastModel) return 0.0;

            // __ Находим период
            $endDate = $lastModel->created_at;
            $startDate = $endDate->copy()->subMonths($period); // Используем copy() чтобы не изменять оригинальный объект

            // __ Получаем среднее значение по длине рулонов в периоде
            $averageLength = FabricTaskRoll::query()
                ->where('fabric_id', $fabricId)
                ->whereIn('roll_status', $statuses)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->avg('textile_roll_length');

            // __ Если моделей, соответствующих вашим критериям, не найдено, метод avg() вернет null, а не 0.
            // __ Поэтому важно обрабатывать этот случай.
            if (!$averageLength) return 0.0;

            return (double)$averageLength / $fabric->textile_layers_amount; // С учетом количества рулонов ткани на рулон ПС
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * ___ Устанавливаем среднюю длину рулона
     * @param int $fabricId
     * @param float $averageLength
     * @return string|void
     */
    public static function setFabricAverageLength(int $fabricId, float $averageLength)
    {
        try {
            $fabric = Fabric::query()->find($fabricId);
            if (!$fabric) return;

            $fabric->average_roll_length_hand = $fabric->average_roll_length;
            $fabric->average_roll_length = $averageLength;
            $fabric->save();

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    /**
     * @param int $fabricId
     * @param int $period
     * @return string
     */
    public static function updateFabricAverageLength(int $fabricId, int $period = FABRIC_STATISTIC_PERIOD): string
    {
        try {
            $averageLength = self::getFabricAverageLength($fabricId, $period);
            self::setFabricAverageLength($fabricId, $averageLength);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * ___ Преобразуем данные в матрицу времени переналадки
     * ___ Пока оставляем неиспользуемой, все преобразования будут на фронте
     * @param array $arrayOfData
     * @return array
     */
    public static function getPicturesTuningTimeMatrix(array $arrayOfData): array
    {
        $matrix = $arrayOfData;

        // __ Сортируем по id
        usort($matrix, fn($a, $b) => $a['id'] <=> $b['id']);

        $resultMatrix = [];

        foreach ($matrix as $item) {

            $pic = [
                'id' => $item['id'],
                'name' => $item['name'],
                'machine_id' => $item['machine']['id']
            ];

            $times = [];
            $times = array_pad($times, count($matrix), 0);

            foreach ($item['pictures_from'] as $pictureFrom) {
                $times[$pictureFrom['picture_to']] = $pictureFrom['tuning_time'];
                // $times[$pictureFrom['picture_to']] = $pictureFrom['tuning_time'];

                // $workPic = self::getFabricPicByName();

            }

            // foreach ($item['pictures_to'] as $pictureTo) {
            //     // $times[$pictureTo['id']] = $pictureTo['tuning_time'];
            // }

            $resultMatrix[] = [
                'pic' => $pic,
                'times' => $times,
            ];

            // $resultMatrix[$item['id']] = [
            //     'pic' => $pic,
            //     'times' => $times,
            // ];

        }


        return $resultMatrix;
    }


    /**
     * ___ Получение времени переналадки рисунков между 2 рисунками
     * @param string $from
     * @param string $to
     * @return string
     */
    public static function getFabricsPicturesBetweenTuningTime(string $from, string $to): string
    {
        try {

            $tuningTime = FabricTuningTime::query()
                ->where('picture_from', $from)
                ->where('picture_to', $to)
                ->first();

            $returnData = [
                'data' => [
                    'from' => (int)$from,
                    'to' => (int)$to,
                    'time' => '',
                ]
            ];

            if ($tuningTime) {
                $returnData['data']['time'] = $tuningTime->tuning_time;
            } else {
                $returnData['data']['time'] = null;
            }

            return json_encode($returnData);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    /**
     *  ___ Возвращаем последний рулон предшествующий данной дате на заданной машине
     * @param string $date
     * @param int $machine
     * @return FabricTaskRoll|null
     */
    public static function getLastRoll(string $date, int $machine): FabricTaskRoll|null
    {
        $targetDate = Carbon::parse($date);

        $stop = 0;


        do {
            // __ Находим задачу с максимальной датой, предшествующей заданной
            $tasksDate = FabricTasksDate::query()
                ->where('tasks_date', '<', $targetDate)
                ->whereHas('fabricTasks', function ($query) use ($machine) {
                    $query->where('fabric_machine_id', $machine);
                })
                ->latest('tasks_date') // Сортировка: Сортируем по дате по убыванию, чтобы самая поздняя была первой
                ->first(); // Получение: Получаем только первую (самую позднюю) запись

            // __ Если нет СЗ на этой СМ, то выходим
            if (!$tasksDate) return null;

            // __ Находим все рулоны, которые были сделаны на эту дату
            $rolls = FabricTaskRoll::query()
                ->whereNotIn('roll_status', [FABRIC_ROLL_FALSE_CODE, FABRIC_ROLL_CANCELLED_CODE])
                ->whereHas('fabricTaskContext.fabricTask', function ($query) use ($machine) {
                    $query->where('fabric_machine_id', $machine);
                })
                ->whereHas('fabricTaskContext.fabricTask.fabricTasksDate', function ($query) use ($tasksDate) {
                    $query->where('id', $tasksDate->id);
                })
                ->with('fabric.fabricPicture')
                ->orderBy('roll_position')
                ->get();

            if (!$rolls->isEmpty()) {
                return $rolls->last();
            }

            // __ Может возникнуть ситуация, когда есть рулоны, но они все либо cancelled либо false
            // __ В этом случае, мы должны найти дату, от которой отталкиваемся
            $targetDate = Carbon::parse($tasksDate->tasks_date);

        } while (true);

    }


}
