<?php

namespace App\Models\Plan;

use App\Models\Client;
use App\Models\Order\Order;
use App\Models\Order\OrderType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


// ___ Фактически - это обертка над Orders, в которой продублированы поля из PlanLoad
// ___ В будущем - это будет отдельная таблица и сущность
// ___ Но логику уже разносим по разным сущностям
class PlanLoad extends Order
{
    protected $table = 'orders';



}
