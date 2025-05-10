<?php

namespace App\Http\Resources\Manufacture\Cells\Fabric;

use App\Http\Resources\Client\CLientFabricOrderResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FabricOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

//        $fabricsExpense = $this->fabricsExpense;
//        $fabricsExpensedData = [];
//
//        foreach ($fabricsExpense as $fabricExpense) {
//            $fabricsExpensedData[] = new FabricOrderExpenseResource($fabricExpense);
//        }

        return [
            'active' => $this->active,
            'client' => new CLientFabricOrderResource($this->client),
            'closed_at' => $this->closed_at,
            'closed_by_user_id' => $this->closed_by_user_id,
            'code_1C' => $this->code_1C,
            'description' => $this->description,
            'expense_date' => Carbon::parse($this->expense_date)->format('d.m.Y'),
//            'expense_date' =>$this->expense_date,
            'id' => $this->id,
            'is_closed' => $this->is_closed,
            'order_no' => $this->order_no,
            'raw_text' => $this->raw_text,
            'time_1C' => $this->time_1C,
//            'type' => $this->type,
            'fabricsExpense' => new FabricOrderExpenseCollection($this->fabricsExpense),
//            'fabricsExpense' => $this->fabricsExpense,
//            'fabricsExpense' => $fabricsExpensedData,
//            'comment' => $this->comment,
//            'expense_date' => $this->expense_date,
//            'note' => $this->note,
        ];

        return parent::toArray($request);
    }
}
