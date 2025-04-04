<?php
// app/Http/Controllers/EmployeeController.php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\EmployeeResource; // If you choose to use API Resources

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
            'phone_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Add image validation

            'salary' => 'nullable|numeric', // Or 'nullable|regex:/^\d+(\.\d{1,2})?$/' for more specific decimal validation
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $data = $request->all();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension(); // Generate a unique filename
            $path = $image->storeAs('employees', $filename, 'public'); // Store the image in the 'public/employees' directory

            $data['image'] = $path; // Save the file path in the database
        }

        $employee = Employee::create($request->all());

    }

    public function show(Employee $employee)
    {
         if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404); // Not Found
        }
    }

    public function update(Request $request, Employee $employee)
    {
         if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404); // Not Found
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'salary' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee->update($request->all());

    }

    public function destroy(Employee $employee)
    {
         if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404); // Not Found
        }
        $employee->delete();

        return response()->json(['message' => 'Employee deleted'], 204);
    }
}
