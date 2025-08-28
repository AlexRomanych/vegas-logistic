<?php

namespace App\Http\Controllers\Api\V1\Cells\Fabric;

use App\Classes\EndPointStaticRequestAnswer;
use App\Http\Controllers\Controller;

//use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricOrderCollection;
use App\Http\Resources\Manufacture\Cells\Fabric\FabricOrderResource;
use App\Models\Manufacture\Cells\Fabric\Fabric;
use App\Models\Manufacture\Cells\Fabric\FabricExpense;
use App\Models\Manufacture\Cells\Fabric\FabricOrder;
use App\Services\Manufacture\FabricService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CellFabricOrderController extends Controller
{

    /**
     * __ Загрузка расхода ПС из отчета СВПМ
     * @param Request $request
     * @return string
     */
    public function uploadFabricOrders(Request $request)
    {
        $fabricOrders = $request->all();

        // todo Сделать проверку на валидность данных в FabricOrders - отчет 1С - СВПМ

        $dubs = [];
        $missingFabrics = [];

        foreach ($fabricOrders as $fabricOrder) {

            // attract: проверяем есть ли такой заказ по коду из 1С
            $checkFabricOrder = FabricOrder::query()
                ->where('code_1C', $fabricOrder['code_1C'])
                ->first();

            // attract: Если такой заказ уже есть, удаляем его вместе с хвостом
            if ($checkFabricOrder) {
                $dubs[] = $fabricOrder;                                 // Добавляем в массив дубликатов
                $checkFabricOrder->delete();
            }

            // __ Если нет, создаем новый заказ
            $maxPosition = FabricOrder::query()->max('position');// Получаем максимальную позицию
            $newFabricOrder = FabricOrder::query()->create([
                'client_id' => $fabricOrder['client_id'],
                'order_no' => $fabricOrder['order_no'],
                'raw_text' => $fabricOrder['raw'],
                'code_1C' => $fabricOrder['code_1C'],
                'time_1C' => Carbon::parse($fabricOrder['moment']),
                'type' => $fabricOrder['type'],
                'expense_date' => Carbon::now()->addDay(),               // Добавляем 1 день
                'is_closed' => false,
                'active' => true,
                'position' => $maxPosition + 1,
            ]);


            $newFabricOrderId = $newFabricOrder->id;                    // Дергаем ID для заполнения FabricExpense
            $newFabricOrderExpenseDate = $newFabricOrder->expense_date;

            // attract: Проходимся по каждому расходу и добавляем его в таблицу FabricExpense
            foreach ($fabricOrder['fabrics'] as $fabricExpense) {

                $fabric = FabricService::getFabricByName($fabricExpense['name']);

                if (is_null($fabric)) {
                    $missingFabrics[] = $fabricExpense['name'];        // Собираем список ПС которых нет в базе
                } else {
                    $newFabricExpense = FabricExpense::query()->create([
                        'fabric_order_id' => $newFabricOrderId,
                        'fabric_id' => $fabric->id,
                        'name' => $fabricExpense['name'],
                        'expense' => $fabricExpense['expense'],
                        'expense_at' => $newFabricOrderExpenseDate,
                    ]);
                }
            }
        }

        if (count($dubs) && count($missingFabrics) === 0) {
            return EndPointStaticRequestAnswer::ok();
        }

        return EndPointStaticRequestAnswer::fail(['dubs' => $dubs, 'missing_fabrics' => array_unique($missingFabrics)]);
    }


    // ___ Получить список расходов ПС по активным заказам

    /**
     * @return AnonymousResourceCollection|string
     */
    public function getFabricOrders()
    {
        try {
            $fabricOrders = FabricOrder::query()
                ->where('is_closed', false)
                ->with(['fabricsExpense', 'client'])
                // ->orderBy('expense_date')
                ->orderBy('position')                   // сортируем по позиции
                ->get();

            return FabricOrderResource::collection($fabricOrders);
            // return new FabricOrderCollection($fabricOrders);
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e->getMessage());
        }
    }


    // Descr: Меняем статус заказа расходов ПС (отображаем в расчетах или нет)

    /**
     * @param Request $request
     * @return string
     */
    public function setFabricOrderActive(Request $request)
    {
        try {
            $validData = $request->validate([
                'data' => 'required|array',
                'data.id' => 'required|integer',
                'data.active' => 'required|boolean',
            ]);

            $data = $request->input('data');

            $fabricOrder = FabricOrder::query()->find($data['id']);

            if ($fabricOrder) {
                $fabricOrder->active = $data['active'];
                $fabricOrder->save();

                return EndPointStaticRequestAnswer::ok();
            } else {
                throw new Exception('Заказ расходов ПС с id = ' . $data['id'] . ' не найден');
            }

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e->getMessage());
        }

    }


    // Descr: Закрываем заказ расходов ПС

    /**
     * @param Request $request
     * @return string
     */
    public function closeFabricOrder(Request $request)
    {
        try {
            $validData = $request->validate([
                'data' => 'required|array',
                'data.id' => 'required|integer',
            ]);

            $data = $request->input('data');

            $fabricOrder = FabricOrder::query()->find($data['id']);

            if ($fabricOrder) {
                $fabricOrder->is_closed = true;
                $fabricOrder->save();

                return EndPointStaticRequestAnswer::ok();
            } else {
                throw new Exception('Заказ расходов ПС с id = ' . $data['id'] . ' не найден');
            }

        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e->getMessage());
        }
    }


    // ___ Сохраняем порядок расходов ПС
    /**
     * @param Request $request
     * @return string
     */
    public function saveFabricsOrdersOrder(Request $request)
    {
        try {
            $validData = $request->validate([
                'order' => 'required|array',
                'order.*.order_id' => 'required|integer',
                'order.*.position' => 'required|integer',
            ]);

            foreach ($validData['order'] as $item) {
                $expenseOrder = FabricOrder::query()->find($item['order_id']);
                if ($expenseOrder) {
                    $expenseOrder->position = $item['position'];
                    $expenseOrder->save();
                } else {
                    throw new Exception('Заказ расходов ПС с id = ' . $item['order_id'] . ' не найден');
                }
            }

            return EndPointStaticRequestAnswer::ok();
        } catch (Exception $e) {
            return EndPointStaticRequestAnswer::fail($e->getMessage());
        }
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
