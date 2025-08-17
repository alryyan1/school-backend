<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return response()->json(['data' => PaymentMethod::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        // Restrict to users who can record payments
        if (!auth()->user() || !auth()->user()->can('record fee payments')) {
            return response()->json(['message' => 'غير مخول لإضافة طرق الدفع'], 403);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:payment_methods,name',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Validation Error', 'errors' => $validator->errors()], 422);
        }
        $method = PaymentMethod::create($validator->validated());
        return response()->json(['data' => $method], 201);
    }
}


