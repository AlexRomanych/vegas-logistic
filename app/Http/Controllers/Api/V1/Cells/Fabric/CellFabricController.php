<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Classes\FabricInstance;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricCollection;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricResource;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricTasksDateCollection;
use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricTaskRoll;
use App\Models\Manufacture\Cells\Fabric\FabricTasksDate;
use App\Services\Manufacture\FabricService;
use Exception;
use Illuminate\Http\Request;

class CellFabricController extends Controller
{
    /**
     * Attract: Загружаем данные из отчета 1С
     * @param Request $request
     * @return array|string
     */
    public function uploadFabrics(Request $request): array|string
    {
        // Info: Структура выгружаемых данных
        /*
            {
            "code":"000034799",                         ' ['code']          Код 1C
            "name":"ПС 142МТ 8П 30С граф (рис. ГР)",    ' ['name']          Наименование ПС
            "active":"да",                              ' ['active']        Действующий или архивный
            "pic":"ГР",                                 ' ['pic']           Название рисунка
            "opt_party":300,                            ' ['opt_party']     Оптимальная партия для запуска
            "rolls_amount":1,                           ' ['rolls_amount']  Кол-во рулонов в ПС
            "load_time":7,                              ' ['load_time']     Время загрузки рулона
            "time_loss":5,                              ' ['time_loss']     Временные потери, %
            "translate_rate":1.052                      ' ['translate_rate']    Перевод ПС в ткань
            }
        */

        // todo Сделать проверку на валидность данных массива fabrics.json

        $fabrics = $request->all();
        $dubs = [];

        foreach ($fabrics as $fabric) {

            $checkFabric = Fabric::query()
                ->where('code_1C', $fabric['code'])
                ->first();

            if ($checkFabric) {
                $dubs[] = $fabric['name'];

            } else {

                $bufferMinRolls = (int)$fabric['buffer_min_rolls'] === 0 ? 1 : (int)$fabric['buffer_min_rolls'];
                $bufferMaxRolls = (int)$fabric['buffer_max_rolls'] === 0 ? 1 : (int)$fabric['buffer_max_rolls'];

                $fabricDataSet = [
                    'code_1C' => $fabric['code'],
                    'name' => $fabric['name'],
                    'active' => strtolower($fabric['active']) == 'да',
                    'buffer_min' => (float)$fabric['buffer_min'],
                    'buffer_max' => (float)$fabric['buffer_max'],
                    'buffer_min_rolls' => $bufferMinRolls,
                    'buffer_max_rolls' => $bufferMaxRolls,
                    'optimal_party' => (int)$fabric['opt_party'],
                    'rolls_amount' => (int)$fabric['rolls_amount'],
                    'load_roll_time' => (int)$fabric['load_time'],
                    'time_loss' => $fabric['time_loss'],
                    'translate_rate' => (float)$fabric['translate_rate'],
                    'productivity' => (float)$fabric['productivity'],
                    'average_roll_length' => (float)$fabric['average_roll_length'],
                ];

                $fabricPictureId = FabricService::getFabricPicByName($fabric['pic']);
                $fabricDataSet['fabric_picture_id'] = $fabricPictureId->id ?? 0;
//
                Fabric::query()->create($fabricDataSet);
            }

        }

        if (count($dubs)) {
            EndPointStaticRequestAnswer::dubs($dubs);
        }

        return EndPointStaticRequestAnswer::ok();
    }

    /**
     * Attract: Возвращаем ПС по id
     * @param $id
     * @return FabricResource
     */
    public function fabric($id)
    {
        return new FabricResource(Fabric::query()->find($id));
    }


    /**
     *  Attract: Возвращаем список ПС
     * @param Request $request
     * @return FabricCollection|false|string
     */
    public function fabrics(Request $request)
    {
        try {

            // attract: Проверяем есть ли в запросе параметр active
            if (!$request->has('active')) {

                $fabrics = Fabric::query()
                    ->orderBy('name')
                    ->with('fabricPicture')
//                ->with(['fabricPicture', 'fabricOrder'])
                    ->get();

            } else {

                // attract: Если есть, то проверяем, не null ли он
                $payloadStatus = $request->validate(['data.active' => 'boolean']);
                $payloadStatus = $request->get('active');

                $fabrics = Fabric::query()
                    ->where('active', $payloadStatus)
                    ->orderBy('name')
                    ->with('fabricPicture')
//                ->with(['fabricPicture', 'fabricOrder'])
                    ->get();

            }

            if (!$fabrics) {
                return json_encode(['data' => null]);
            }

            return new FabricCollection($fabrics);

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    /**
     * Descr: Обновляем ПС
     * @param Request $request
     * @return string
     */
    public function update(Request $request)
    {
        try {

            // todo Сделать проверку на валидность данных изменяемого ПС
            $fabricPayload = $request->input('data');           // получаем данные из запроса

            $fabricInstance = new FabricInstance($fabricPayload['name']);
            $picName = $fabricInstance->getPicture();
            $picture = FabricService::getFabricPicByName($picName);

            if (!$picture) {
                throw new Exception('Рисунок стежки не найден!');
            }

            $fabric = Fabric::query()->find($fabricPayload['id']);

            if (!$fabric) {
                throw new Exception('ПС не найдено!');
            }

            $fabric->code_1C = $fabricPayload['code_1C'];
            $fabric->name = $fabricPayload['name'];
            $fabric->buffer_amount = $fabricPayload['buffer']['amount'];
            $fabric->average_roll_length = $fabricPayload['buffer']['average_length'];
            $fabric->buffer_min_rolls = $fabricPayload['buffer']['min_rolls'];
            $fabric->buffer_max_rolls = $fabricPayload['buffer']['max_rolls'];
            $fabric->optimal_party = $fabricPayload['buffer']['optimal_party'];
            $fabric->translate_rate = $fabricPayload['buffer']['rate'];
            $fabric->productivity = $fabricPayload['buffer']['productivity'];
            $fabric->description = $fabricPayload['text']['description'];
            $fabric->active = $fabricPayload['active'];
            $fabric->fabric_picture_id = $picture->id;
            $fabric->save();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }

    }


    /**
     * Descr: Создание ПС
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        try {

            // todo Сделать проверку на валидность данных создаваемого ПС
            $fabricPayload = $request->input('data');           // получаем данные из запроса

            $fabricInstance = new FabricInstance($fabricPayload['name']);
            $picName = $fabricInstance->getPicture();
            $picture = FabricService::getFabricPicByName($picName);

            if (!$picture) {
                throw new Exception('Рисунок стежки не найден!');
            }

            $fabric = Fabric::query()->create([

                'code_1C' => $fabricPayload['code_1C'],
                'name' => $fabricPayload['name'],
                'buffer_amount' => $fabricPayload['buffer']['amount'],
                'average_roll_length' => $fabricPayload['buffer']['average_length'],
                'buffer_min_rolls' => $fabricPayload['buffer']['min_rolls'],
                'buffer_max_rolls' => $fabricPayload['buffer']['max_rolls'],
                'optimal_party' => $fabricPayload['buffer']['optimal_party'],
                'translate_rate' => $fabricPayload['buffer']['rate'],
                'productivity' => $fabricPayload['buffer']['productivity'],
                'description' => $fabricPayload['text']['description'],
                'active' => $fabricPayload['active'],
                'fabric_picture_id' => $picture->id,

            ]);

            if (!$fabric) {
                throw new Exception('Ошибка создания ПС!');
            }

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }

    }


    /**
     * Attract Удаляем ПС
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $fabric = Fabric::query()->find($id);
        if ($fabric) {
            $fabric->delete();
            return OK_STATUS_WORD;
        }

        return FAIL_STATUS_WORD;

    }


    public function updateFabricsBuffer()
    {
        try {

            $fabrics = Fabric::query()
                ->where('active', true)
                ->get();

            foreach ($fabrics as $fabric) {

                $textileLength =
                    FabricTaskRoll::query()
                        ->where('fabric_id', $fabric->id)
                        ->whereIn('roll_status', [FABRIC_ROLL_DONE_CODE, FABRIC_ROLL_REGISTERED_1C_CODE])
                        ->sum('textile_roll_length');


                $fabric->buffer_amount = $fabric->translate_rate === 0 ? 0 : $textileLength / $fabric->translate_rate;

                $fabric->save();
            }


            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

}
