<?php

namespace App\Http\Controllers;

use App\Models\OtherRevenue;
use App\Models\RevenueCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OtherRevenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = OtherRevenue::with(['revenueCategory', 'user']);

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('revenue_category_id', $request->category_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('revenue_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('revenue_date', '<=', $request->date_to);
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Search by description
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('desc', 'like', "%{$search}%");
        }

        // Sort
        $sortBy = $request->get('sort_by', 'revenue_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->get('per_page', 15);
        $revenues = $query->paginate($perPage);

        return response()->json([
            'data' => $revenues->items(),
            'pagination' => [
                'current_page' => $revenues->currentPage(),
                'last_page' => $revenues->lastPage(),
                'per_page' => $revenues->perPage(),
                'total' => $revenues->total(),
                'from' => $revenues->firstItem(),
                'to' => $revenues->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'desc' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'revenue_category_id' => 'required|exists:revenue_categories,id',
            'payment_method' => 'required|in:cash,bank',
            'revenue_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();

        $revenue = OtherRevenue::create($data);
        $revenue->load(['revenueCategory', 'user']);

        return response()->json(['data' => $revenue], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $revenue = OtherRevenue::with(['revenueCategory', 'user'])->findOrFail($id);

        return response()->json(['data' => $revenue]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $revenue = OtherRevenue::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'desc' => 'sometimes|required|string',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'revenue_category_id' => 'sometimes|required|exists:revenue_categories,id',
            'payment_method' => 'sometimes|required|in:cash,bank',
            'revenue_date' => 'sometimes|required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $revenue->update($request->all());
        $revenue->load(['revenueCategory', 'user']);

        return response()->json(['data' => $revenue]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $revenue = OtherRevenue::findOrFail($id);

        $revenue->delete();

        return response()->json(['message' => 'Revenue deleted successfully']);
    }

    /**
     * Get revenue statistics
     */
    public function statistics(Request $request)
    {
        $query = OtherRevenue::query();

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('revenue_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('revenue_date', '<=', $request->date_to);
        }

        $totalRevenues = $query->sum('amount');
        $revenueCount = $query->count();
        $averageRevenue = $revenueCount > 0 ? $totalRevenues / $revenueCount : 0;

        // Revenues by category
        $revenuesByCategory = $query->with('revenueCategory')
            ->get()
            ->groupBy('revenue_category_id')
            ->map(function ($revenues) {
                return [
                    'category_name' => optional($revenues->first()->revenueCategory)->name,
                    'total_amount' => $revenues->sum('amount'),
                    'count' => $revenues->count(),
                ];
            })
            ->values();

        // Totals by payment method
        $totalsByPaymentMethod = $query->get()
            ->groupBy('payment_method')
            ->map(function ($revenues, $method) {
                return [
                    'payment_method' => $method,
                    'total_amount' => $revenues->sum('amount'),
                ];
            })
            ->values();

        return response()->json([
            'total_revenues' => $totalRevenues,
            'revenue_count' => $revenueCount,
            'average_revenue' => round($averageRevenue, 2),
            'revenues_by_category' => $revenuesByCategory,
            'totals_by_payment_method' => $totalsByPaymentMethod,
        ]);
    }
}
