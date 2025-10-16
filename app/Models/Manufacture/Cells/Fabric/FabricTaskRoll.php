<?php

namespace App\Models\Manufacture\Cells\Fabric;

use App\Models\User;
use App\Models\Worker\Worker;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricTaskRoll extends Model
{
    // use HasAttributes;

    protected $guarded = [];

    //warning: Конвенция наименования атрибутов
    protected $appends = [
        'isRegistered',     //__ Атрибут, который будет доступен через getIsRegisteredAttribute()/setIsRegisteredAttribute()
        'is_registered',    //__ Атрибут, который будет доступен через isRegistered()
    ];    // Атрибуты, которые будут доступны через $model->isRegistered

    // Аксессор (GET)
    public function getIsRegisteredAttribute(): bool
    {
        // Логика остается той же, но в старом методе
        return $this->registration_1C_at !== null && $this->registration_1C_by !== 0;
    }

    // Мутатор (SET)
    public function setIsRegisteredAttribute(bool $value): void
    {
        if ($value) {
            $this->attributes['registration_1C_at'] = now();
            $this->attributes['registration_1C_by'] = auth()->id();
        } else {
            $this->attributes['registration_1C_at'] = null;
            $this->attributes['registration_1C_by'] = 0;
        }
    }


    // ___ Attribute: Рулон зарегистрирован в 1С
    protected function isRegistered(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->registration_1C_at !== null && $this->registration_1C_by !== 0,
            set: fn (bool $value) => $this->attributes = array_merge(
                $this->attributes,
                [
                    'registration_1C_at' => $value ? now() : null,
                    'registration_1C_by' => $value ? auth()->id() : 0,
                ]
            ),


            // get: function () {
            //     return $this->registration_1C_at !== null && $this->registration_1C_by !== 0;
            // },
            // set: function (bool $value, array $attributes) {
            //     if ($value) {
            //         $this->registration_1C_at = now();
            //         $this->registration_1C_by = auth()->id();
            //
            //         $registration_1C_at = $this->registration_1C_at;
            //         $registration_1C_by = $this->registration_1C_by;
            //
            //     } else {
            //         $this->registration_1C_at = null;
            //         $this->registration_1C_by = 0;
            //     }
            //     return $value;
            // }

        // get: fn (mixed $value, array $attributes) => new Address(
        //    $attributes['address_line_one'],
        //    $attributes['address_line_two'],
        // get: fn() => $this->registration_1C_at !== null && $this->registration_1C_by !== 0,
        // set: fn(bool $value) => $value ? now() : null,

        );
    }




    //    // Relations: Связь со сменным заданием
    //    public function fabricTask(): BelongsTo
    //    {
    //        return $this->belongsTo(FabricTask::class);
    //    }


    // Relations: Связь с ПС
    public function fabric(): BelongsTo
    {
        return $this->belongsTo(Fabric::class);
    }


    // Relations: Связь с плановым сменным заданием
    public function fabricTaskContext(): BelongsTo
    {
        return $this->belongsTo(FabricTaskContext::class);
    }


    // Relations: Связь с работягой, ответственного за выпуск рулона
    public function finishBy(): BelongsTo
    {
        return $this->belongsTo(Worker::class, 'finish_by', 'id');
    }


    // Relations: Связь с работягой, ответственного за постановку рулона в 1С
    public function registration1CBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registration_1C_by', 'id');
    }


    // Relations: Связь с работягой, ответственного за перемещение рулона на закрой
    public function moveToCutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'move_to_cut_by', 'id');
    }


    // Relations: Связь с работягой, ответственного за принятие рулона на закрой
    public function receiptToCutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receipt_to_cut_by', 'id');
    }


    // Relations: Связь с работягой, от записи которого делались действия с рулоном на производстве
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
