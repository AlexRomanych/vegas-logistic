<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->string('code1C');
            $table->primary('code1C');
            $table->string('name')->nullable(false)->unique();

            $table->unsignedTinyInteger('active')->nullable(false)->default(0)->comment('Активный или нет');

            $table->timestamps();
        });




    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
