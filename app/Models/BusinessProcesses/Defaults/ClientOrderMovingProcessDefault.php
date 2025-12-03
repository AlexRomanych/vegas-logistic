<?php

namespace App\Models\BusinessProcesses\Defaults;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClientOrderMovingProcessDefault extends Pivot // !!!
{
    protected $table = 'client_order_moving_process_defaults';

    protected $guarded = false;
    protected $hidden = ['created_at', 'updated_at'];

}
