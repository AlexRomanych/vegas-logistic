<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Worker\WorkerCollection;
use App\Http\Resources\Worker\WorkerResource;
use App\Models\Worker\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkerController extends Controller
{

    public function workers()
    {
        return new WorkerCollection(
            Worker::query()
                ->orderBy('surname')
                ->orderBy('name')
                ->with('cellItem')
                ->get()
        );
    }


    public function model($code1C)
    {
        return new WorkerResource(Worker::query()->find($code1C));
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Attract Создаем сотрудника
     */
    public function create(Request $request)
    {
        $workerPayload = $request->input('data');           // получаем данные из запроса
        $worker = Worker::query()->create([
            'surname' => $workerPayload['surname'],
            'name' => $workerPayload['name'],
            'patronymic' => $workerPayload['patronymic'],
            'cell_item_id' => $workerPayload['cell_item_id'],
        ]);

        if ($worker) {
            return OK_STATUS_WORD;
        }
        return FAIL_STATUS_WORD;
    }


    /**
     * Attract Возвращаем сотрудника
     */
    public function show(string $id)
    {
        $worker = Worker::query()->find($id);
        if ($worker) return new WorkerResource($worker);
        return FAIL_STATUS_WORD;
    }


    /**
     * Attract Обновляем сотрудника
     */
    public function update(Request $request)
    {
        $workerPayload = $request->input('data');           // получаем данные из запроса
        $worker = Worker::query()->find($workerPayload['id']);   // ищем сотрудника по id в базе

        if ($worker) {
            $worker->surname = $workerPayload['surname'];
            $worker->name = $workerPayload['name'];
            $worker->patronymic = $workerPayload['patronymic'];
            $worker->cell_item_id = $workerPayload['cell_item_id'];
            $worker->save();
            return OK_STATUS_WORD;
        }
        return FAIL_STATUS_WORD;
    }

    /**
     * Attract Удаляем сотрудника
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $worker = Worker::query()->find($id);
        if ($worker) {
            $worker->delete();
            return OK_STATUS_WORD;
        }

        return FAIL_STATUS_WORD;

    }

    /**
     * descr: Заполняем таблицу тестовыми данными
     * @return void
     */
    public function testFill()
    {
        DB::table('workers')->insert([
            ['surname' => 'Иванов', 'name' => 'Иван', 'patronymic' =>'Иванович'],
            ['surname' => 'Петров', 'name' => 'Петр', 'patronymic' =>'Петрович'],
            ['surname' => 'Сидоров', 'name' => 'Сидор', 'patronymic' =>'Сидорович'],
            ['surname' => 'Никитюк', 'name' => 'Никита', 'patronymic' =>'Никитич'],
            ['surname' => 'Стаханов', 'name' => 'Алексей', 'patronymic' =>'Григорьевич'],
            ['surname' => 'Мухина', 'name' => 'Вера', 'patronymic' =>'Игнатьевна'],
        ]);
    }

}
