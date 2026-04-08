<?php
/** @noinspection DuplicatedCode */

namespace App\Http\Controllers\Api\V1;

use App\Classes\EndPointStaticRequestAnswer;
use App\Contracts\VegasDataGetContract;
use App\Http\Controllers\Controller;


use App\Http\Requests\Clients\StoreClientRequest;
use App\Http\Resources\Client\ClientResource;
use App\Models\Client;
use App\Services\ClientsService;
use App\Services\ManagersService;
use App\Services\SharedService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

// use Illuminate\Support\Facades\DB;


class ClientController extends Controller
{
    //__ Загружаем клиентов из файла
    public function clientsLoad(VegasDataGetContract $getter)
    {
        return '123';
        $a = 0;

        try {
            App::make(ManagersService::class, [$getter])->updateData();
            App::make(ClientsService::class, [$getter])->updateData();
            return '';

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }


    /**
     * ___ Отдаем список клиентов
     * @param $status : нет (null) - все, 0 (false) - неактивные, 1 (true) - активные
     * @return AnonymousResourceCollection|string
     */
    public function getClients($status = null)
    {
        try {
            $validator = validator([
                'status' => (int)$status,
            ], [
                'status' => 'nullable|in:0,1',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $status = is_null($status) ? -1 : (int)$status;

            match ($status) {
                0 => $result = Client::query()->where('active', false)->get(),
                1 => $result = Client::query()->where('active', true)->get(),
                default => $result = Client::all(),
            };

            return ClientResource::collection($result);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    /**
     * ___ Отдаем клиента по id
     * @param int $id
     * @return ClientResource|string
     */
    public function getClient(int $id)
    {
        try {
            $validator = validator([
                'id' => $id,
            ], [
                'id' => 'required|exists:clients,id',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            return new ClientResource(Client::query()->find($id));
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }

    }


    /**
     * ___ Сохраняем клиента
     * @param StoreClientRequest $request
     * @return string
     * @noinspection PhpUndefinedFieldInspection
     */
    public function createClient(StoreClientRequest $request)
    {
        try {
            $data = $request->validated();

            if ($data['id'] == 0 || ($data['id'] < 0 && $data['id'] != -1)) {
                throw new Exception('Неверный id = ' . $data['id']);
            }

            $client = Client::query()->find($data['id']);
            if ($client) {
                throw new Exception('Клиент c id = ' . $data['id'] . ' уже существует');
            }

            $client = Client::query()
                ->whereRaw('LOWER(name) = ?', [strtolower($data['name'])])
                ->whereRaw('LOWER(add_name) = ?', [strtolower($data['add_name'])])
                ->first();

            if ($client) {
                throw new Exception('Дубликат имен: ' . $data['name'] . ' / ' . $data['add_name']);
            }

            $client = Client::query()
                ->whereRaw('LOWER(short_name) = ?', [strtolower($data['short_name'])])
                ->first();

            if ($client) {
                throw new Exception('Дубликат отображаемых имен: ' . $data['short_name']);
            }

            // __ Сырой код по вставке клиента
            /*
            $binds = [
                // Если вы вставляете ID вручную, добавьте его первым:
                'id'          => $data['id'],
                'name'        => $data['name'],
                'add_name'    => $data['add_name'] ?? '', // Обработка null (как в вашем примере)
                'short_name'  => $data['short_name'],
                // Логические значения (active) должны быть правильно приведены для SQL (0 или 1)
                'active'      => (int)$data['active'],
                'region'      => $data['region'],
                'description' => $data['description'],
                'comment'     => $data['comment'],
                // Добавление временных меток вручную
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            if ($data['id'] === -1) {
                unset($binds['id']);          // Задаем id вручную нового клиента
            }

            // Формируем список столбцов и плейсхолдеров (?) для привязки
            $columns = implode(', ', array_keys($binds));
            $placeholders = implode(', ', array_fill(0, count($binds), '?'));

            $sql = "INSERT INTO clients ({$columns}) VALUES ({$placeholders})";

            DB::statement($sql, array_values($binds));
            */

            $client = new Client();
            // $client->fill($data);

            if ($data['id'] != -1) {
                $client->id = $data['id'];          // Задаем id вручную нового клиента
            }

            $client->name = $data['name'];
            $client->add_name = $data['add_name'] ?? '';
            $client->short_name = $data['short_name'];
            $client->active = $data['active'];
            $client->region = $data['region'];
            $client->description = $data['description'];
            $client->comment = $data['comment'];

            $client->save();

            if ($data['id'] != -1) {
                SharedService::setSequence('clients');  // Если установили id вручную, то корректируем sequence
            }

            return EndPointStaticRequestAnswer::ok('Сохранено успешно');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
            // return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * ___ Обновляем клиента
     * @param StoreClientRequest $request
     * @return string
     */
    public function updateClient(StoreClientRequest $request)
    {
        try {
            // $all = $request->all();
            $data = $request->validated();
            $client = Client::query()->find($data['id']);
            if (!$client) {
                throw new Exception('Клиент c id = ' . $data['id'] . ' не найден');
            }

            $client->name = $data['name'];
            $client->add_name = $data['add_name'] ?? '';
            $client->short_name = $data['short_name'];
            $client->active = $data['active'];
            $client->region = $data['region'];
            $client->description = $data['description'];
            $client->comment = $data['comment'];

            $client->save();

            return EndPointStaticRequestAnswer::ok('Обновлено успешно');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }

    /**
     * ___ Удаляем клиента
     * @param Request $request
     * @return string
     */
    public function deleteClient(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'id' => 'required|integer|min:1|exists:clients,id',
            ]);

            $client = Client::query()->find($validatedData['id']);

            if (!$client) {
                throw new Exception('Клиент c id = ' . $validatedData['id'] . ' не найден');
            }

            $client->delete();

            return EndPointStaticRequestAnswer::ok('Удалено успешно');
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail(response()->json($e));
        }
    }


    public function clientsUpload()
    {
        //return 123;
        DB::table('clients')->truncate();
        DB::table('clients')->insert([


            ['id' => '0', 'name' => 'Нет данных', 'add_name' => '', 'short_name' => 'Нет данных', 'code_1c' => ''],
            ['id' => '1', 'name' => 'CITA SANTEHNIKA', 'add_name' => '', 'short_name' => 'CITA SANTEHNIKA', 'code_1c' => ''],
            ['id' => '2', 'name' => 'Classica Nova GmbH&Co.KG', 'add_name' => '', 'short_name' => 'CLN', 'code_1c' => ''],
            ['id' => '3', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Rietberg', 'short_name' => 'MSA_Rietberg', 'code_1c' => ''],
            ['id' => '4', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Inter Handels GmbH', 'short_name' => 'MSA_IH', 'code_1c' => ''],
            ['id' => '5', 'name' => 'Kappmeier Mobel GmbH', 'add_name' => '', 'short_name' => 'Kappmeier', 'code_1c' => ''],
            ['id' => '6', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'MSA', 'short_name' => 'MSA', 'code_1c' => ''],
            ['id' => '7', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Nieder', 'short_name' => 'MSA_Nieder', 'code_1c' => ''],
            ['id' => '8', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Classica Nova', 'short_name' => 'MSA_CLN', 'code_1c' => ''],
            ['id' => '9', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Kappmeier', 'short_name' => 'MSA_Kappmeier', 'code_1c' => ''],
            ['id' => '10', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Vertrieb Schwaben', 'short_name' => 'MSA_VS', 'code_1c' => ''],
            ['id' => '11', 'name' => 'HanSales', 'add_name' => '', 'short_name' => 'HanSales', 'code_1c' => ''],
            ['id' => '12', 'name' => 'SIA Do it', 'add_name' => '', 'short_name' => 'Jysk_LV', 'code_1c' => ''],
            ['id' => '13', 'name' => 'UAB "Vegas LT"', 'add_name' => '', 'short_name' => 'Литва', 'code_1c' => ''],
            ['id' => '14', 'name' => 'Drommerom', 'add_name' => '', 'short_name' => 'Drommerom', 'code_1c' => ''],
            ['id' => '15', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Concord', 'short_name' => 'MSA_Concord', 'code_1c' => ''],
            ['id' => '16', 'name' => 'Брест-Интурист', 'add_name' => '', 'short_name' => 'Интурист', 'code_1c' => ''],
            ['id' => '17', 'name' => 'Отдел разработки', 'add_name' => '', 'short_name' => 'ОР', 'code_1c' => ''],
            ['id' => '18', 'name' => 'Pigu UAB', 'add_name' => '', 'short_name' => 'Pigu', 'code_1c' => ''],
            ['id' => '19', 'name' => 'ООО Вегас', 'add_name' => '', 'short_name' => 'ООО Вегас', 'code_1c' => ''],
            ['id' => '20', 'name' => 'Вегас Групп (Словакия)', 'add_name' => '', 'short_name' => 'Словакия', 'code_1c' => ''],
            ['id' => '21', 'name' => 'ВЕГАС ГРУПП РУС', 'add_name' => 'Москва', 'short_name' => 'ВГР_Москва', 'code_1c' => '003454'],
            ['id' => '22', 'name' => 'ВЕГАС ГРУПП РУС', 'add_name' => 'СПб', 'short_name' => 'ВГР_СПб', 'code_1c' => ''],
            ['id' => '23', 'name' => 'Вегас Казахстан', 'add_name' => '', 'short_name' => 'Казахстан', 'code_1c' => ''],
            ['id' => '24', 'name' => 'Благотворительный фонд', 'add_name' => '', 'short_name' => 'Благ. Фонд', 'code_1c' => ''],
            ['id' => '25', 'name' => 'Децимус', 'add_name' => '', 'short_name' => 'Молдова', 'code_1c' => ''],
            ['id' => '26', 'name' => 'Дивинский детский дом', 'add_name' => '', 'short_name' => 'Див. детдом', 'code_1c' => ''],
            ['id' => '27', 'name' => 'ИП Головейко А.В.', 'add_name' => '', 'short_name' => 'ИП Головейко', 'code_1c' => ''],
            ['id' => '28', 'name' => 'INDOOR GROUP AS', 'add_name' => '', 'short_name' => 'INDOOR', 'code_1c' => ''],
            ['id' => '29', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'RaumDecor', 'short_name' => 'MSA_RaumDecor', 'code_1c' => ''],
            ['id' => '30', 'name' => 'Йуск', 'add_name' => '', 'short_name' => 'Jysk_BY', 'code_1c' => ''],
            ['id' => '31', 'name' => 'Бронь', 'add_name' => '', 'short_name' => 'Бронь', 'code_1c' => ''],
            ['id' => '32', 'name' => 'ЛидерМатрасМаркет', 'add_name' => 'ЛММ_Минск', 'short_name' => 'ЛММ_Минск', 'code_1c' => ''],
            ['id' => '33', 'name' => 'ЛидерМатрасМаркет', 'add_name' => 'ЛММ_Брест', 'short_name' => 'ЛММ_Брест', 'code_1c' => ''],
            ['id' => '34', 'name' => 'ЛидерМатрасМаркет', 'add_name' => 'ЛММ_Гомель', 'short_name' => 'ЛММ_Гомель', 'code_1c' => ''],
            ['id' => '35', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Albatros', 'short_name' => 'MSA_Albatros', 'code_1c' => ''],
            ['id' => '36', 'name' => 'Нафтан', 'add_name' => '', 'short_name' => 'Нафтан', 'code_1c' => ''],
            ['id' => '37', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'HappyHartmann', 'short_name' => 'MSA_HappyHartmann', 'code_1c' => ''],
            ['id' => '38', 'name' => 'НиккоДом', 'add_name' => '', 'short_name' => 'Брест', 'code_1c' => ''],
            ['id' => '39', 'name' => 'ООО "Вегас Украина"', 'add_name' => '', 'short_name' => 'Киев', 'code_1c' => ''],
            ['id' => '40', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Teppichparadies', 'short_name' => 'MSA_Teppichparadies', 'code_1c' => ''],
            ['id' => '41', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Jakatex', 'short_name' => 'MSA_Jеkatex', 'code_1c' => ''],
            ['id' => '42', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Duedo', 'short_name' => 'MSA_Duedo', 'code_1c' => ''],
            ['id' => '43', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Westwing', 'short_name' => 'MSA_Westwing', 'code_1c' => ''],
            ['id' => '44', 'name' => 'Семенов В.А.', 'add_name' => '', 'short_name' => 'ИП Семенов', 'code_1c' => ''],
            ['id' => '45', 'name' => 'Софт-Лайн', 'add_name' => '', 'short_name' => 'Софт-Лайн', 'code_1c' => ''],
            ['id' => '46', 'name' => 'Таран А.В.', 'add_name' => '', 'short_name' => 'Крым', 'code_1c' => '002801'],
            ['id' => '47', 'name' => 'ЦОР по водным видам спорта', 'add_name' => '', 'short_name' => 'ЦОР', 'code_1c' => ''],
            ['id' => '48', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Bibel', 'short_name' => 'MSA_Bibel', 'code_1c' => ''],
            ['id' => '49', 'name' => 'МЧС', 'add_name' => '', 'short_name' => 'МЧС', 'code_1c' => ''],
            ['id' => '50', 'name' => 'Свято-Рождественский -Богородицкий женский монастырь', 'add_name' => '', 'short_name' => 'Монастырь', 'code_1c' => ''],
            ['id' => '51', 'name' => 'Наурызбаев Р.Ш.', 'add_name' => '', 'short_name' => 'ИП Наурызбаев', 'code_1c' => ''],
            ['id' => '52', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'OTTO', 'short_name' => 'MSA_OTTO', 'code_1c' => ''],
            ['id' => '53', 'name' => 'Шляхно', 'add_name' => '', 'short_name' => 'Шляхно', 'code_1c' => ''],
            ['id' => '54', 'name' => 'ИП Серба', 'add_name' => '', 'short_name' => 'ИП Серба', 'code_1c' => ''],
            ['id' => '55', 'name' => 'Renesans', 'add_name' => '', 'short_name' => 'Renesans', 'code_1c' => ''],
            ['id' => '56', 'name' => 'Olivia Sport and Sport IKE', 'add_name' => '', 'short_name' => 'Olivia Sp. & Sp. IKE', 'code_1c' => ''],
            ['id' => '57', 'name' => 'Материальная помощь', 'add_name' => '', 'short_name' => 'Мат. помощь', 'code_1c' => ''],
            ['id' => '58', 'name' => 'Студенческая деревня', 'add_name' => '90х200х30 Sample 6', 'short_name' => 'Тендер СД', 'code_1c' => ''],
            ['id' => '59', 'name' => 'Студенческая деревня', 'add_name' => '40х90х30 Sample 6', 'short_name' => 'Тендер СД', 'code_1c' => ''],
            ['id' => '60', 'name' => 'Тендер', 'add_name' => '', 'short_name' => 'Тендер', 'code_1c' => ''],
            ['id' => '61', 'name' => 'GBB projekt', 'add_name' => '', 'short_name' => 'GBB projekt', 'code_1c' => ''],
            ['id' => '62', 'name' => 'РУП "Одиннадцать"', 'add_name' => '', 'short_name' => 'РУП 11', 'code_1c' => ''],
            ['id' => '63', 'name' => 'RAY', 'add_name' => '', 'short_name' => 'RAY', 'code_1c' => '004033'],
            ['id' => '64', 'name' => 'HOFF', 'add_name' => '', 'short_name' => 'HOFF', 'code_1c' => ''],
            ['id' => '65', 'name' => 'RB Architects', 'add_name' => '', 'short_name' => 'RB Arc', 'code_1c' => ''],
            ['id' => '66', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Ahaus', 'short_name' => 'MSA_Ahaus', 'code_1c' => ''],
            ['id' => '67', 'name' => 'ВЕГАС ГРУПП РУС', 'add_name' => 'Тогас', 'short_name' => 'ВГР_Тогас', 'code_1c' => ''],
            ['id' => '68', 'name' => 'ГрГУ им Я. Купалы', 'add_name' => '', 'short_name' => 'ГрГУ им Я. Купалы', 'code_1c' => ''],
            ['id' => '69', 'name' => 'ЛидерМатрасМаркет', 'add_name' => 'ЛММ_Акция', 'short_name' => 'ЛММ - Акция', 'code_1c' => ''],
            ['id' => '70', 'name' => 'ИП Ефремов И.М.', 'add_name' => '', 'short_name' => 'EddBess', 'code_1c' => ''],
            ['id' => '71', 'name' => 'ЛидерМатрасМаркет', 'add_name' => 'ЛММ_Гродно', 'short_name' => 'ЛММ_Гродно', 'code_1c' => ''],
            ['id' => '72', 'name' => 'Lelekka', 'add_name' => '', 'short_name' => 'Lelekka', 'code_1c' => ''],
            ['id' => '73', 'name' => 'УЗ "Ивацевичская ЦРБ"', 'add_name' => '', 'short_name' => 'УЗ Ивац. ЦРБ', 'code_1c' => ''],
            ['id' => '74', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Dreameo', 'short_name' => 'MSA_Dreameo', 'code_1c' => ''],
            ['id' => '75', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Kunden', 'short_name' => 'MSA_Kunden', 'code_1c' => ''],
            ['id' => '76', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Intenza', 'short_name' => 'MSA Intenza', 'code_1c' => ''],
            ['id' => '77', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Betten Uhlmann', 'short_name' => 'MSA_Betten Uhlmann', 'code_1c' => ''],
            ['id' => '78', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'FMP', 'short_name' => 'MSA_FMP', 'code_1c' => ''],
            ['id' => '79', 'name' => 'Новое поколение', 'add_name' => '', 'short_name' => 'Новое поколение', 'code_1c' => ''],
            ['id' => '80', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Mollen', 'short_name' => 'MSA_Mollen', 'code_1c' => ''],
            ['id' => '81', 'name' => 'Andreas Uhlig GmbH & Co. KG', 'add_name' => '', 'short_name' => 'Uhlig', 'code_1c' => ''],
            ['id' => '82', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'MFO', 'short_name' => 'MSA_MFO', 'code_1c' => ''],
            ['id' => '83', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'B&S', 'short_name' => 'MSA_B&S', 'code_1c' => ''],
            ['id' => '84', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'XXXLutz', 'short_name' => 'MSA_XXXLutz', 'code_1c' => ''],
            ['id' => '85', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Betten Jung', 'short_name' => 'MSA_Betten Jung', 'code_1c' => ''],
            ['id' => '86', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Sandmann', 'short_name' => 'MSA_Sandmann', 'code_1c' => ''],
            ['id' => '87', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Vcure', 'short_name' => 'MSA_Vcure', 'code_1c' => ''],
            ['id' => '88', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Bader', 'short_name' => 'MSA_Bader', 'code_1c' => ''],
            ['id' => '89', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Geodis', 'short_name' => 'MSA_Geodis', 'code_1c' => ''],
            ['id' => '90', 'name' => 'ВЕГАС ГРУПП РУС', 'add_name' => 'WB', 'short_name' => 'ВГР_WB', 'code_1c' => ''],
            ['id' => '91', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Average', 'short_name' => 'MSA_Average', 'code_1c' => ''],
            ['id' => '92', 'name' => 'Обшивка', 'add_name' => '', 'short_name' => 'Обшивка', 'code_1c' => ''],
            ['id' => '93', 'name' => 'Eastblock d.o.o ', 'add_name' => '', 'short_name' => 'Eastblock', 'code_1c' => ''],
            ['id' => '94', 'name' => 'ЛидерМатрасМаркет', 'add_name' => 'ЛММ_Триовист', 'short_name' => 'ЛММ_Триовист', 'code_1c' => ''],
            ['id' => '95', 'name' => 'ВЕГАС ГРУПП РУС', 'add_name' => 'Озон', 'short_name' => 'ВГР_Озон', 'code_1c' => ''],
            ['id' => '96', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'FAFA', 'short_name' => 'MSA_FAFA', 'code_1c' => ''],
            ['id' => '97', 'name' => 'Moderne SchlafArt GmbH i.G.', 'add_name' => 'Blanar ', 'short_name' => 'MSA_Blanar ', 'code_1c' => ''],
            ['id' => '98', 'name' => 'Киргизия', 'add_name' => '', 'short_name' => 'ААЛАМ ТРЕЙДИНГ', 'code_1c' => 'БП0002291'],
            ['id' => '99', 'name' => 'Дагестан', 'add_name' => '', 'short_name' => 'Головешкина Юлия Ивановна', 'code_1c' => 'БП0002483'],
            ['id' => '100', 'name' => 'Грузия', 'add_name' => '', 'short_name' => 'SIMAL', 'code_1c' => 'БП0002482'],
            ['id' => '101', 'name' => 'Wersal', 'add_name' => '', 'short_name' => 'Wersal', 'code_1c' => 'БП0001471'],
            ['id' => '102', 'name' => 'КеаХоум', 'add_name' => '', 'short_name' => 'КеаХоум', 'code_1c' => 'БП0002542'],
            ['id' => '103', 'name' => 'Оршанский льнокомбинат', 'add_name' => '', 'short_name' => 'Оршанский льнокомбинат', 'code_1c' => ''],
            ['id' => '104', 'name' => 'Заявка ИП', 'add_name' => '', 'short_name' => 'Заявка ИП', 'code_1c' => ''],
            ['id' => '105', 'name' => 'Детский сад №25 г. Бреста', 'add_name' => '', 'short_name' => 'Детский сад №25 г. Бреста', 'code_1c' => 'БП0002603'],
            ['id' => '106', 'name' => 'Головчик Василий Григорьевич', 'add_name' => '', 'short_name' => 'Головчик Василий Григорьевич', 'code_1c' => ''],

        ]);


    }
}
