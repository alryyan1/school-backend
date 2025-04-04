<?php
// app/Http/Controllers/SchoolController.php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\SchoolResource;
use Illuminate\Support\Facades\Storage;

class SchoolController extends Controller
{
    public function index()
    {
        $schools = School::all();
        return SchoolResource::collection($schools);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // If you want to allow logo uploads here
            'basic_info' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $filename = time() . '.' . $logo->getClientOriginalExtension();
            $path = $logo->storeAs('schools', $filename, 'public');
            $data['logo'] = $path;
        }

        $school = School::create($data);

        return new SchoolResource($school);
    }

    public function show(School $school)
    {
        if (!$school) {
            return response()->json(['message' => 'School not found'], 404);
        }
        return new SchoolResource($school);
    }

    public function update(Request $request, School $school)
    {
         if (!$school) {
            return response()->json(['message' => 'School not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'basic_info' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        if ($request->hasFile('logo')) {
            // Delete the old logo if it exists
            if ($school->logo) {
                Storage::disk('public')->delete($school->logo);
            }

            $logo = $request->file('logo');
            $filename = time() . '.' . $logo->getClientOriginalExtension();
            $path = $logo->storeAs('schools', $filename, 'public');
            $data['logo'] = $path;
        }

        $school->update($data);

        return new SchoolResource($school);
    }

    public function destroy(School $school)
    {
         if (!$school) {
            return response()->json(['message' => 'School not found'], 404);
        }

        // Delete the logo if it exists
        if ($school->logo) {
            Storage::disk('public')->delete($school->logo);
        }

        $school->delete();

        return response()->json(['message' => 'School deleted'], 204);
    }
}