<?php

namespace App\Services;

use App\Models\Plan\PlanLoad;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

final class PlanService
{



    /**
     * ___ Возвращает true, если есть коллизии по дате загрузки
     * @param Collection|null $loads
     * @return bool
     */
    public static function isLoadsCollisionsPresent(Collection | null $loads): bool
    {
        if (is_null($loads)) return false;
        if ($loads->count() === 0 || $loads->count() === 1) return false;

        $dates = [];
        $loads->each(function ($load) use (&$dates) {
            // Проверяем, чтобы даты загрузок не были в одном году
            $dates[] = self::getOrderPeriod($load->load_at)->copy()->startOfYear();
        });
        // $dates = $loads->pluck('load_at')->toArray();

        $result = checkCarbonDuplicates($dates);
        return $result['has_duplicates'];
    }


    /**
     * ___ Возвращает Период Заявки по дате загрузки
     * @param string|Carbon|PlanLoad $entity
     * @return Carbon
     */
    public static function getOrderPeriod(string | Carbon | PlanLoad $entity): Carbon
    {
        $period = self::normalizeToCarbon($entity);
        return $period->copy()->startOfMonth();
    }


    /**
     * ___ Возвращает дату в виде Carbon
     * @noinspection PhpUndefinedFieldInspection
     * @param string|Carbon|PlanLoad $entity
     * @return Carbon
     */
    public static function normalizeToCarbon(string | Carbon | PlanLoad $entity ): Carbon
    {
        return match (true) {
            is_string($entity) => (function() use ($entity) {
                try {
                    return Carbon::parse($entity);
                } catch (\Exception $e) {
                    return Carbon::now();
                }
            })(),
            $entity instanceof Carbon => $entity,
            $entity instanceof PlanLoad => Carbon::parse($entity->load_at),
            default => Carbon::now(),
            // default => throw new \InvalidArgumentException(
            //     'Ожидается строка, Carbon или PlanLoad, получен: ' . get_debug_type($entity)
            // )
        };
    }

}
