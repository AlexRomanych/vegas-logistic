<?php

namespace App\Models\Abstract;

use Illuminate\Database\Eloquent\Model;

abstract class CellGroup extends Model
{
/*
      public string $name                             - название группы
      public array $cellList                          - список всех ячеек группы
      public array $parentGroupList                   - список всех родителей группы (те группы, с которых могут приходить матрасы)
      public array $childGroupList                    - список всех потомков группы (те группы, куда могут уходить матрасы)
      protected $startProcessPoint                    - точка начала процесса группы:
                                                            окончание другого процесса (конец другой группы)
                                                            дата внесения в 1С
                                                            расчетная дата, например за пару дней до загрузки
*/
}
