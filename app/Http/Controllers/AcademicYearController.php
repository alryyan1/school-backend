<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Http\Resources\AcademicYearResource;
use Illuminate\Support\Facades\DB; // Import DB Facade for transactions
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // For exists rule

class AcademicYearController extends Controller
{
    // Helper function to handle setting is_current flag
    private function setCurrentFlag(AcademicYear $academicYear, bool $isCurrent)
    {
        if ($isCurrent) {
            // Use transaction to ensure atomicity
            DB::transaction(function () use ($academicYear) {
                // Set all other years for the same school to false
                AcademicYear::where('school_id', $academicYear->school_id)
                    ->where('id', '!=', $academicYear->id)
                    ->update(['is_current' => false]);

                // Update the target year (ensure it's saved if it's a new instance)
                 if ($academicYear->exists) {
                    $academicYear->update(['is_current' => true]);
                 } else {
                     // If it's a new record being created, the flag is already set
                     // But we still need to unset others *before* saving the new one
                 }
            });
            // Refresh the model instance if updated within transaction
            $academicYear->refresh();
        }
        // If setting to false, just update this one record (or rely on create/update)
         else if ($academicYear->exists) {
            $academicYear->update(['is_current' => false]);
         }
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // Allow filtering by school
    {
        // Optional: Authorization check
        // $this->authorize('viewAny', AcademicYear::class);

        $query = AcademicYear::with('school')->latest(); // Eager load school

        // Optional: Filter by school_id if provided
        if ($request->has('school_id')) {
            $query->where('school_id', $request->input('school_id'));
        }

        // No pagination for now as requested (but easy to add back)
        $academicYears = $query->get();

        return AcademicYearResource::collection($academicYears);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Optional: Authorization check
        // $this->authorize('create', AcademicYear::class);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after:start_date',
            'is_current' => 'required|boolean',
            'school_id' => ['required', 'integer', Rule::exists('schools', 'id')], // Ensure school exists
            'enrollment_fee' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $shouldBeCurrent = $validatedData['is_current'];

         // Important: Unset other 'current' flags *before* creating the new one if needed
         if ($shouldBeCurrent) {
              // Use transaction to unset others first
             DB::transaction(function () use ($validatedData) {
                 AcademicYear::where('school_id', $validatedData['school_id'])
                             ->update(['is_current' => false]);
             });
         }

        $academicYear = AcademicYear::create($validatedData);

        // No need to call setCurrentFlag again here as create already set it

        return new AcademicYearResource($academicYear->load('school')); // Load school relationship
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear) // Route model binding
    {
        // Optional: Authorization check
        // $this->authorize('view', $academicYear);

        return new AcademicYearResource($academicYear->load('school')); // Eager load school
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        // Optional: Authorization check
        // $this->authorize('update', $academicYear);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date_format:Y-m-d',
            'end_date' => ['sometimes', 'required', 'date_format:Y-m-d',
                           // Ensure end_date is after start_date, considering which date is being updated
                           function ($attribute, $value, $fail) use ($request, $academicYear) {
                                $startDate = $request->input('start_date', $academicYear->start_date->format('Y-m-d'));
                                if ($startDate && $value <= $startDate) {
                                    $fail('تاريخ النهاية يجب أن يكون بعد تاريخ البداية.');
                                }
                           }
                          ],
            'is_current' => 'sometimes|required|boolean',
            'enrollment_fee' => 'sometimes|nullable|numeric|min:0',
            // Generally, changing the school_id might be restricted
            // 'school_id' => ['sometimes', 'required', 'integer', Rule::exists('schools', 'id')],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        // Handle 'is_current' flag update
        if (isset($validatedData['is_current'])) {
            $this->setCurrentFlag($academicYear, $validatedData['is_current']);
            // Remove from validated data if handled separately by setCurrentFlag's update
             if ($validatedData['is_current'] === false) {
                 // If setting to false, we let the main update handle it
             } else {
                 // If setting to true, setCurrentFlag already updated it
                 unset($validatedData['is_current']); // Avoid redundant update if logic handles it
             }

        }

        // Update remaining fields if any
        if (!empty($validatedData)) {
            $academicYear->update($validatedData);
        }


        return new AcademicYearResource($academicYear->fresh()->load('school')); // Return fresh data
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear)
    {
        // Optional: Authorization check
        // $this->authorize('delete', $academicYear);

        // Add checks here if deletion should be prevented based on related data (e.g., enrollments)
        // if ($academicYear->enrollments()->exists()) { // Assuming relationship exists
        //     return response()->json(['message' => 'لا يمكن حذف السنة الدراسية لوجود تسجيلات مرتبطة بها.'], 409); // Conflict
        // }

        $academicYear->delete();

        return response()->json(['message' => 'تم حذف السنة الدراسية بنجاح'], 200);
    }
}