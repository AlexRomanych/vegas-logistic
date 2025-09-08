<?php

namespace App\Http\Resources\Manufacture\Cells\Fabric;

use App\Http\Resources\Worker\WorkerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FabricTaskRollMovingResource extends JsonResource
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
            'fabric' => [
                'fabric_id' => $this->fabric->id,
                'display_name' => $this->fabric->display_name,
            ],
            'status' => $this->roll_status,

            'start_at' => $this->start_at, // ? Carbon::parse($this->start_at)->format('d.m.Y H:i:s') : null,

            'finish_at' => $this->finish_at,
            'finish_by' => [
                'id' => $this->finishBy->id,
                'name' => $this->finishBy->name,
                'surname' => $this->finishBy->surname,
                'patronymic' => $this->finishBy->patronymic,
            ],

            'move_to_cut_at' => $this->move_to_cut_at,
            'move_to_cut_by' => [
                'id' => $this->moveToCutBy->id,
                'name' => $this->moveToCutBy->name,
                'surname' => '',
                'patronymic' => '',
//                'surname' => $this->moveToCutBy->surname,
//                'patronymic' => $this->moveToCutBy->patronymic,
            ],
//            'move_to_cut_by' => [
//                'id' => $this->moveToCutBy->id,
//                'name' => $this->moveToCutBy->name,
//                'surname' => $this->moveToCutBy->surname,
//                'patronymic' => $this->moveToCutBy->patronymic,
//            ],

            'receipt_to_cut_at' => $this->receipt_to_cut_at,
            'receipt_to_cut_by' => [
                'id' => $this->receiptToCutBy->id,
                'name' => $this->receiptToCutBy->name,
                'surname' => '',
                'patronymic' => '',
//                'surname' => $this->receiptToCutBy->surname,
//                'patronymic' => $this->receiptToCutBy->patronymic,
            ],
//            'receipt_to_cut_by' => [
//                'id' => $this->receiptToCutBy->id,
//                'name' => $this->receiptToCutBy->name,
//                'surname' => $this->receiptToCutBy->surname,
//                'patronymic' => $this->receiptToCutBy->patronymic,
//            ],


            'registration_1C_at' => $this->registration_1C_at,
            'registration_1C_by' => [
                'id' => $this->registration1CBy->id,
                'name' => $this->registration1CBy->name,
                'surname' => '',
                'patronymic' => '',
//                'surname' => $this->registration1CBy->surname,
//                'patronymic' => $this->registration1CBy->patronymic,
            ],
            'isRegistered_1C' => $this->is_registered,
            //'isRegistered_1C' => parent::toArray($request),
//            'registration_1C_by' => [
//                'id' => $this->registration1CBy->id,
//                'name' => $this->registration1CBy->name,
//                'surname' => $this->registration1CBy->surname,
//                'patronymic' => $this->registration1CBy->patronymic,
//            ],




            'duration' => $this->duration,
            'descr' => $this->description ?? '',
            'comment' => $this->comment ?? '',
            'note' => $this->note ?? '',
            'textile_length' => (float)$this->textile_roll_length,
            'rate' => (float)$this->translate_rate,

//            'user' => $this->user->name,
//            'rolling' => $this->rolling,
//            'resume_at' => $this->resume_at,
//            'paused_at' => $this->paused_at,
//            'status_prev' => $this->roll_status_previous,
//            'position' => $this->roll_position,
//            'movable' => $this->movable,
//            'false_reason' => $this->false_reason,
//            'productivity' => (float)$this->productivity,
//            '__source' => parent::toArray($request)

        ];


//        return parent::toArray($request);
    }
}
