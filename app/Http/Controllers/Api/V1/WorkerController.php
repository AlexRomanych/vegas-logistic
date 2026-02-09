<?php

namespace App\Http\Controllers\Api\V1;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;
use App\Http\Resources\Worker\WorkerResource;
use App\Models\Worker\Worker;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WorkerController extends Controller
{

    /**
     * ___ Получаем список всех сотрудников
     * @return AnonymousResourceCollection|string
     */
    public function getWorkers()
    {
        try {
            $workers = Worker::query()
                ->orderBy('surname')
                ->orderBy('name')
                ->with('cellItem')
                ->get();

            return WorkerResource::collection($workers);
            // return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Возвращаем сотрудника
     * @param  string  $id
     * @return WorkerResource|string
     */
    public function getWorkerById(string $id)
    {
        try {
            $validated = Validator::make(['id' => $id], ['id' => 'required|integer|exists:workers,id']);
            $data      = $validated->validate();

            $worker = Worker::query()
                ->with('cellItem')
                ->findOrFail($data['id']);

            return new WorkerResource($worker);
            // return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Создаем сотрудника
     * @param  Request  $request
     * @return string
     */
    public function createWorker(Request $request)
    {
        try {
            $validated = $request->validate([
                'surname'     => 'required|string|min:2',
                'name'        => 'required|string|min:2',
                'patronymic'  => 'required|string|min:3',
                'active'      => 'required|boolean',
                'description' => 'present|nullable|string',
            ]);

            $worker = Worker::query()->create([
                'surname'     => $validated['surname'],
                'name'        => $validated['name'],
                'patronymic'  => $validated['patronymic'],
                'active'      => $validated['active'],
                'description' => $validated['description'],
                // 'cell_item_id' => $validated['cell_item_id'],
            ]);

            if (!$worker) {
                throw new Exception('Something went wrong during the worker creation');
            }

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }


    /**
     * ___ Обновляем сотрудника
     * @param  Request  $request
     * @return string
     */
    public function updateWorker(Request $request)
    {
        try {
            $validated = $request->validate([
                'id'         => 'required|integer|exists:workers,id',
                'surname'    => 'required|string|min:2',
                'name'       => 'required|string|min:2',
                'patronymic' => 'required|string|min:3',
                'active'     => 'required|boolean',
                'description' => 'present|nullable|string',
            ]);

            $worker = Worker::query()->findOrFail($validated['id']);

            $worker->surname     = $validated['surname'];
            $worker->name        = $validated['name'];
            $worker->patronymic  = $validated['patronymic'];
            $worker->active      = $validated['active'];
            $worker->description = $validated['description'];
            $worker->save();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * ___ Удаляем сотрудника
     * @param  Request  $request
     * @return string
     */
    public function deleteWorker(Request $request)
    {
        try {
            $validated = $request->validate([
                'id' => 'required|integer|exists:workers,id',
            ]);

            $worker = Worker::query()->findOrFail($validated['id']);
            $worker->delete();

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e);
        }
    }

    /**
     * descr: Заполняем таблицу тестовыми данными
     * @return void
     */
    public function testFill()
    {
        DB::table('workers')->insert([
            ['surname' => 'Рудак', 'name' => 'Игорь', 'patronymic' => 'Алексеевич'],
            ['surname' => 'Конончик', 'name' => 'Михаил', 'patronymic' => 'Николаевич'],
            ['surname' => 'Бык', 'name' => 'Максим', 'patronymic' => 'Николаевич'],
            ['surname' => 'Климук', 'name' => 'Роман', 'patronymic' => 'Андреевич'],
            ['surname' => 'Галуц', 'name' => 'Галина', 'patronymic' => 'Владимировна'],
            ['surname' => 'Макарова', 'name' => 'Елена', 'patronymic' => 'Гаррьевна'],
            ['surname' => 'Мятто', 'name' => 'Роман', 'patronymic' => 'Юрьевич'],
            ['surname' => 'Шульга', 'name' => 'Наталья', 'patronymic' => 'Владимировна'],
        ]);


        //        DB::table('workers')->insert([
        //            ['surname' => 'Иванов', 'name' => 'Иван', 'patronymic' =>'Иванович'],
        //            ['surname' => 'Петров', 'name' => 'Петр', 'patronymic' =>'Петрович'],
        //            ['surname' => 'Сидоров', 'name' => 'Сидор', 'patronymic' =>'Сидорович'],
        //            ['surname' => 'Никитюк', 'name' => 'Никита', 'patronymic' =>'Никитич'],
        //            ['surname' => 'Стаханов', 'name' => 'Алексей', 'patronymic' =>'Григорьевич'],
        //            ['surname' => 'Мухина', 'name' => 'Вера', 'patronymic' =>'Игнатьевна'],
        //        ]);
    }

}



