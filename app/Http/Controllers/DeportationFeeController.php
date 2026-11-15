<?php

namespace App\Http\Controllers;

use App\Models\DeportationFee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DeportationFeeController extends Controller
{
    /**
     * عرض رسوم الترحيل لكل الأنواع
     */
    public function index(): JsonResponse
    {
        $fees = DeportationFee::whereIn('type', DeportationFee::TYPES)->get()->keyBy('type');

        // إرجاع الأنواع بالترتيب الثابت حتى لو لم يكن لأحدها صف بعد
        $data = collect(DeportationFee::TYPES)->map(fn ($type) => [
            'type' => $type,
            'amount' => (float) ($fees[$type]->amount ?? 0),
            'updated_at' => $fees[$type]->updated_at ?? null,
        ])->values();

        return response()->json(['data' => $data]);
    }

    /**
     * تعديل رسوم الترحيل (تنطبق على الاشتراكات الجديدة فقط)
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fees' => 'required|array|min:1',
            'fees.*.type' => ['required', 'string', Rule::in(DeportationFee::TYPES)],
            'fees.*.amount' => 'required|numeric|min:0|max:9999999999',
        ], [
            'fees.*.amount.required' => 'المبلغ مطلوب',
            'fees.*.amount.numeric' => 'المبلغ يجب أن يكون رقماً',
            'fees.*.amount.min' => 'المبلغ لا يمكن أن يكون سالباً',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['fees'] as $fee) {
                DeportationFee::updateOrCreate(
                    ['type' => $fee['type']],
                    ['amount' => $fee['amount']]
                );
            }
        });

        return $this->index()->setStatusCode(200);
    }
}
