<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Resources\Logs;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FabricTaskRollLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'log_at' => $this->log_at,
            'event' => $this->event,
            'ip' => $this->ip,
            'description' => $this->description,
            'reason' => $this->reason,
            'roll_position_before' => $this->roll_position_before,
            'roll_position_after' => $this->roll_position_after,
            'status_before' => $this->status_before,
            'status_after' => $this->status_after,
            'check_1C' => $this->check_1C,
            'uncheck_1C' => $this->uncheck_1C,

            'fabric_task_roll' => [
                'id' => $this->fabricTaskRoll->id,
                'fabric_roll_length' => $this->fabricTaskRoll->fabric_roll_length,
                'fabric' => [
                    'id' => $this->fabricTaskRoll->fabric->id,
                    'display_name' => $this->fabricTaskRoll->fabric->display_name,
                ],
            ],

            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'surname' => $this->user->surname,
                'patronymic' => $this->user->patronymic,

            ],

            'responsible' => [
                'id' => $this->responsible->id,
                'name' => $this->responsible->name,
                'surname' => $this->responsible->surname,
                'patronymic' => $this->responsible->patronymic,
            ],

            // '_' => parent::toArray($request)
        ];

    }
}
