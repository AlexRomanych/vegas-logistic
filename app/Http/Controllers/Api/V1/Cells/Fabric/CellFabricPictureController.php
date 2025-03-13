<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Models\Manufacture\Cells\Fabric\FabricPicture;
use App\Services\Manufacture\FabricService;
use Illuminate\Http\Request;

class CellFabricPictureController extends Controller
{

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
                $picDataSet['stitch length'] = (float)$fabricPic['stitch_length'];
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

}
