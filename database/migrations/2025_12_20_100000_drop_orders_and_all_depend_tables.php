<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('cell_sewing_auto');
        Schema::dropIfExists('cell_sewing_hard');
        Schema::dropIfExists('cell_sewing_light');
        Schema::dropIfExists('cell_sewing_universal');
        Schema::dropIfExists('lines');
        Schema::dropIfExists('orders');
    }

};
