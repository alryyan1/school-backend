<?php // app/Http/Controllers/TransportRouteController.php
namespace App\Http\Controllers;

use App\Models\TransportRoute;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\TransportRouteResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TransportRouteController extends Controller
{
    // --- Standard CRUD for Routes ---
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), ['school_id' => 'required|integer|exists:schools,id']);
        if ($validator->fails()) return response()->json(['message' => 'معرف المدرسة مطلوب', 'errors' => $validator->errors()], 422);
        // Eager load relations and count students
        $routes = TransportRoute::with(['school', 'driver'])->withCount('studentAssignments')
            ->where('school_id', $request->input('school_id'))
            ->orderBy('name')->get();
        return TransportRouteResource::collection($routes);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_id' => 'required|integer|exists:schools,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'driver_id' => 'nullable|integer|exists:users,id', // Check if user has 'driver' role?
            'fee_amount' => 'nullable|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ]);
        if ($validator->fails()) return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        $data = $validator->validated();
        $data['is_active'] = $request->boolean('is_active', true); // Default active
        $route = TransportRoute::create($data);
        return new TransportRouteResource($route->load(['school', 'driver']));
    }
    public function show(TransportRoute $transportRoute)
    {
        return new TransportRouteResource($transportRoute->load(['school', 'driver'])->loadCount('studentAssignments'));
    }
    public function update(Request $request, TransportRoute $transportRoute)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'driver_id' => 'nullable|integer|exists:users,id',
            'fee_amount' => 'nullable|numeric|min:0',
            'is_active' => 'sometimes|boolean',
            // Cannot change school_id easily
        ]);
        if ($validator->fails()) return response()->json(['message' => 'خطأ في التحقق', 'errors' => $validator->errors()], 422);
        $transportRoute->update($validator->validated());
        return new TransportRouteResource($transportRoute->fresh()->load(['school', 'driver']));
    }
    public function destroy(TransportRoute $transportRoute)
    {
        if ($transportRoute->studentAssignments()->exists()) {
            return response()->json(['message' => 'لا يمكن حذف المسار لوجود طلاب مسجلين به.'], 409); // Conflict
        }
        $transportRoute->delete();
        return response()->json(['message' => 'تم حذف المسار بنجاح.'], 200);
    }
}
