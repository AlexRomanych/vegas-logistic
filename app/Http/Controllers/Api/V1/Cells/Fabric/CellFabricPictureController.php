<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricPictureResource;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricPictureTuningTimeResource;
use App\Models\Manufacture\Cells\Fabric\FabricPicture;
use App\Services\Manufacture\FabricService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CellFabricPictureController extends Controller
{


    /**
     * Descr: Получение списка рисунков ПС
     * @return AnonymousResourceCollection|string
     */
    public function getFabricPictures()
    {
        try {
            $fabricPictures = FabricPicture::query()
                ->with([
                    'fabricMainMachine',
                    'fabricAltMachine_1',
                    'fabricAltMachine_2',
                    'fabricAltMachine_3',
                    'fabricMainMachineSchema',
                    'fabricAltMachineSchema_1',
                    'fabricAltMachineSchema_2',
                    'fabricAltMachineSchema_3',
                ])
                ->get();


            return FabricPictureResource::collection($fabricPictures);
        } catch (\Exception $e) {
            return EndPointStaticRequestAnswer::fail($e->getMessage());
        }
    }

    /**
     * Descr: Получение рисунка ПС по id
     * @param $id
     * @return FabricPictureResource|string
     */
    public function getFabricPicture($id)
    {
        try {
            $picture = FabricPicture::query()
                ->with([
                    'fabricMainMachine',
                    'fabricAltMachine_1',
                    'fabricAltMachine_2',
                    'fabricAltMachine_3',
                    'fabricMainMachineSchema',
                    'fabricAltMachineSchema_1',
                    'fabricAltMachineSchema_2',
                    'fabricAltMachineSchema_3',
                ])
                ->find($id);

            return new FabricPictureResource($picture);
        } catch (\Exception $e) {
            return EndPointStaticRequestAnswer::fail($e->getMessage());
        }
    }


    /**
     * Descr: Обновление данных рисунка ПС
     * @param Request $request
     * @return string
     */
    public function updateFabricPictures(Request $request)
    {
        try {

            // todo Сделать валидацию данных
            $fabricPicturePayload = $request->input('data');           // получаем данные из запроса

            $fabricPicture = FabricPicture::query()->find($fabricPicturePayload['id']);

            if (!$fabricPicture) {
                throw new Exception('Рисунок ПС не найден');
            }

            $fabricPicture->name = $fabricPicturePayload['name'];
            $fabricPicture->active = $fabricPicturePayload['active'];
            $fabricPicture->stitch_length = $fabricPicturePayload['stitch_length'];
            $fabricPicture->stitch_speed = $fabricPicturePayload['stitch_speed'];
            $fabricPicture->moment_speed = $fabricPicturePayload['moment_speed'];
            $fabricPicture->shuttle_amount = $fabricPicturePayload['shuttle_amount'] === 0 ? null : $fabricPicturePayload['shuttle_amount'];
            $fabricPicture->productivity = $fabricPicturePayload['productivity'];
            $fabricPicture->description = $fabricPicturePayload['description'];

            $fabricPicture->fabric_machine_id = $fabricPicturePayload['fabricMainMachineId'];
            $fabricPicture->fabric_picture_schema_id = $fabricPicturePayload['fabricMainMachineSchemaId'];

            $fabricPicture->alt_machine_1_id = $fabricPicturePayload['fabricAltMachineId_1'];
            $fabricPicture->alt_machine_1_schema_id = $fabricPicturePayload['fabricAltMachineSchemaId_1'];

            $fabricPicture->alt_machine_2_id = $fabricPicturePayload['fabricAltMachineId_2'];
            $fabricPicture->alt_machine_2_schema_id = $fabricPicturePayload['fabricAltMachineSchemaId_2'];

            $fabricPicture->alt_machine_3_id = $fabricPicturePayload['fabricAltMachineId_3'];
            $fabricPicture->alt_machine_3_schema_id = $fabricPicturePayload['fabricAltMachineSchemaId_3'];

            $fabricPicture->save();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e->getMessage());
        }
    }


    /**
     * Descr: Создание рисунка ПС
     * @param Request $request
     * @return string
     */
    public function createFabricPictures(Request $request)
    {

        try {

            // todo Сделать валидацию данных
            $fabricPicturePayload = $request->input('data');           // получаем данные из запроса

            $fabric = FabricPicture::query()->create(
                [
                    'name' => $fabricPicturePayload['name'],
                    'active' => $fabricPicturePayload['active'],
                    'stitch_length' => $fabricPicturePayload['stitch_length'],
                    'stitch_speed' => $fabricPicturePayload['stitch_speed'],
                    'moment_speed' => $fabricPicturePayload['moment_speed'],
                    'productivity' => $fabricPicturePayload['productivity'],

                    'shuttle_amount' => $fabricPicturePayload['shuttle_amount'] === 0 ? null : $fabricPicturePayload['shuttle_amount'],
                    'description' => $fabricPicturePayload['description'],

                    'fabric_machine_id' => $fabricPicturePayload['fabricMainMachineId'],
                    'fabric_picture_schema_id' => $fabricPicturePayload['fabricMainMachineSchemaId'],

                    'alt_machine_1_id' => $fabricPicturePayload['fabricAltMachineId_1'],
                    'alt_machine_1_schema_id' => $fabricPicturePayload['fabricAltMachineSchemaId_1'],

                    'alt_machine_2_id' => $fabricPicturePayload['fabricAltMachineId_2'],
                    'alt_machine_2_schema_id' => $fabricPicturePayload['fabricAltMachineSchemaId_2'],

                    'alt_machine_3_id' => $fabricPicturePayload['fabricAltMachineId_3'],
                    'alt_machine_3_schema_id' => $fabricPicturePayload['fabricAltMachineSchemaId_3'],
                ]);

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e->getMessage());
        }
    }


    /**
     * Загружает в базу данных список рисунков ПС
     * @param Request $request
     * @return string|array
     */
    public function uploadFabricPictures(Request $request): string|array
    {

        // Info: Структура выгружаемых данных

        /*
            {
                "pic_name":"E1",                ' ['pic_name']          Название рисунка
                "active":"да",                  ' ['active']            Актуальность
                "main_mach":"Американец",       ' ['main_mach']         Наименование основной стегальной машины
                "main_mach_sch":"1/2",          ' ['main_mach_sch']     Схема игл основной стегальной машины
                "alt_1_mach":"Немец",           ' ['alt_1_mach']        Наименование альтернативной стегальной машины №1
                "alt_1_mach_sch":"1/2",         ' ['alt_1_mach_sch']    Схема игл альтернативной стегальной машины №1
                "alt_2_mach":"Китаец",          ' ['alt_2_mach']        Наименование альтернативной стегальной машины №2
                "alt_2_mach_sch":"1/2",         ' ['alt_2_mach_sch']    Схема игл альтернативной стегальной машины №2
                "alt_3_mach":"",                ' ['alt_3_mach']        Наименование альтернативной стегальной машины №3
                "alt_3_mach_sch":"",            ' ['alt_3_mach_sch']    Схема игл альтернативной стегальной машины №3
                "stitch_length":3.5,            ' ['stitch_length']     Длина стежка, мм
                "stitch_speed":700,             ' ['stitch_speed']      Скорость стежков, шт./мин.
                "moment_speed":88,              ' ['moment_speed']      Мгновенная скорость, м/ч
                "shuttle_amount":0              ' ['shuttle_amount']    Количество челноков для корейца, шт.
            }
        */

        // todo Сделать проверку на валидность данных массива fabric_pictures.json

        $fabricPics = $request->all();
        $dubs = [];

        foreach ($fabricPics as $fabricPic) {

            $checkFabricPic = FabricPicture::query()
                ->where('name', $fabricPic['pic_name'])
                ->first();

            if ($checkFabricPic) {

                $dubs[] = $fabricPic['pic_name'];

            } else {

                $picDataSet = [
                    'name' => $fabricPic['pic_name'],
                    'active' => strtolower($fabricPic['active']) === 'да',
                ];

                // Сохранение основной машины
                $mainMachine = FabricService::getFabricMachineByShortName($fabricPic['main_mach'] ?? '');
                $picDataSet['fabric_machine_id'] = $mainMachine->id ?? 0;

                // Сохранение альтернативной машины №1
                $altMachine_1 = FabricService::getFabricMachineByShortName($fabricPic['alt_1_mach'] ?? '');
                $picDataSet['alt_machine_1_id'] = $altMachine_1->id ?? 0;

                // Сохранение альтернативной машины №2
                $altMachine_2 = FabricService::getFabricMachineByShortName($fabricPic['alt_2_mach'] ?? '');
                $picDataSet['alt_machine_2_id'] = $altMachine_2->id ?? 0;

                // Сохранение альтернативной машины №3
                $altMachine_3 = FabricService::getFabricMachineByShortName($fabricPic['alt_3_mach'] ?? '');
                $picDataSet['alt_machine_3_id'] = $altMachine_3->id ?? 0;


                // Сохранение схемы основной машины
                $mainMachineSchema = FabricService::getFabricPicSchemaByName($fabricPic['main_mach_sch'] ?? '');
                $picDataSet['fabric_picture_schema_id'] = $mainMachineSchema->id ?? 0;

                // Сохранение схемы альтернативной машины №1
                $altMachineSchema_1 = FabricService::getFabricPicSchemaByName($fabricPic['alt_1_mach_sch'] ?? '');
                $picDataSet['alt_machine_1_schema_id'] = $altMachineSchema_1->id ?? 0;

                // Сохранение схемы альтернативной машины №2
                $altMachineSchema_2 = FabricService::getFabricPicSchemaByName($fabricPic['alt_2_mach_sch'] ?? '');
                $picDataSet['alt_machine_2_schema_id'] = $altMachineSchema_2->id ?? 0;

                // Сохранение схемы альтернативной машины №3
                $altMachineSchema_3 = FabricService::getFabricPicSchemaByName($fabricPic['alt_3_mach_sch'] ?? '');
                $picDataSet['alt_machine_3_schema_id'] = $altMachineSchema_3->id ?? 0;


                // Сохранение характеристик ПС
                $picDataSet['stitch_length'] = (float)$fabricPic['stitch_length'];
                $picDataSet['stitch_speed'] = (int)$fabricPic['stitch_speed'];
                $picDataSet['moment_speed'] = (int)$fabricPic['moment_speed'];
                $picDataSet['shuttle_amount'] = (int)$fabricPic['shuttle_amount'] === 0 ? null : $fabricPic['shuttle_amount'];


                FabricPicture::query()->create($picDataSet);
            }

        }

        if (count($dubs)) {
            EndPointStaticRequestAnswer::dubs($dubs);
        }

        return EndPointStaticRequestAnswer::ok();
    }


    public function getFabricsPicturesTuningTime(Request $request)
    {
        // try {

        // $tuningTime = FabricTuningTime::query()
        //     ->with(['picturesFrom', 'picturesTo'])
        //     ->get();

        $tuningTime = FabricPicture::query()
            ->with([
                'fabricMainMachine',                                          // добавляем основную СМ
                'picturesFrom',                                               // добавляем рисунки из которых производится тюнинг
                'picturesFrom.picTo', 'picturesFrom.picTo.fabricMainMachine', // добавляем объекты рисунков и СМ
            ])
            // ->with([
            //     'fabricMainMachine',
            //     'picturesFrom.picFrom', 'picturesFrom.picFrom.fabricMainMachine',
            //     'picturesFrom.picTo', 'picturesFrom.picTo.fabricMainMachine',
            //     'picturesTo.picFrom', 'picturesTo.picFrom.fabricMainMachine',
            //     'picturesTo.picTo', 'picturesTo.picTo.fabricMainMachine',
            // ])
            ->get();

        return FabricPictureTuningTimeResource::collection($tuningTime);
        // return FabricPictureTuningTimeResource::collection($tuningTime)->toArray($request);

        // $arrayOfData = FabricPictureTuningTimeResource::collection($tuningTime)->toArray($request);
        // $matrix = FabricService::getPicturesTuningTimeMatrix($arrayOfData);
        return ['data' => $matrix];

        // return FabricPictureTuningTimeResource::collection($tuningTime);

        // } catch (Exception $e) {
        //     return EndPointStaticRequestAnswer::fail(response()->json($e));
        // }
    }


}
