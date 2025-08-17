<?php // app/Http/Resources/StudentFeePaymentResource.php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentFeePaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fee_installment_id' => $this->fee_installment_id, // <-- New FK
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'notes' => $this->notes,
            'payment_method_id' => $this->payment_method_id,
            'payment_method_name' => optional($this->whenLoaded('paymentMethod'))?->name,
            // Optional: Load installment details
            // 'fee_installment' => new FeeInstallmentResource($this->whenLoaded('feeInstallment')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
