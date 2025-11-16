<?php

namespace App\Http\Controllers;

use App\Models\DeportationPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeportationPathController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paths = DeportationPath::orderBy('name')->get();
        return response()->json(['data' => $paths]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:deportation_paths,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $path = DeportationPath::create($validator->validated());
        return response()->json(['data' => $path], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(DeportationPath $deportationPath)
    {
        return response()->json(['data' => $deportationPath]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeportationPath $deportationPath)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:deportation_paths,name,' . $deportationPath->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $deportationPath->update($validator->validated());
        return response()->json(['data' => $deportationPath]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeportationPath $deportationPath)
    {
        $deportationPath->delete();
        return response()->json(['message' => 'تم حذف المسار بنجاح'], 200);
    }
}
