<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ExpenseListPdf;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Expense::with(['expenseCategory', 'createdBy']);

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('expense_category_id', $request->category_id);
        }


        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        // Search by title or description
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'expense_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->get('per_page', 15);
        $expenses = $query->paginate($perPage);

        return response()->json([
            'data' => $expenses->items(),
            'pagination' => [
                'current_page' => $expenses->currentPage(),
                'last_page' => $expenses->lastPage(),
                'per_page' => $expenses->perPage(),
                'total' => $expenses->total(),
                'from' => $expenses->firstItem(),
                'to' => $expenses->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'payment_method' => 'required|in:cash,bankak',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['created_by'] = Auth::id();

        $expense = Expense::create($data);
        $expense->load(['expenseCategory', 'createdBy']);

        return response()->json(['data' => $expense], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $expense = Expense::with(['expenseCategory', 'createdBy'])->findOrFail($id);

        return response()->json(['data' => $expense]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
   
        $expense = Expense::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'sometimes|required|numeric|min:0.01',
            'expense_category_id' => 'sometimes|required|exists:expense_categories,id',
            'expense_date' => 'sometimes|required|date',
            'payment_method' => 'sometimes|required|in:cash,bankak',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        // return $data;
        $expense->update($data);
        $expense->load(['expenseCategory', 'createdBy']);

        return response()->json(['data' => $expense]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expense = Expense::findOrFail($id);

        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully']);
    }

    /**
     * Get expense statistics
     */
    public function statistics(Request $request)
    {
        $query = Expense::query();


        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('expense_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('expense_date', '<=', $request->date_to);
        }

        $totalExpenses = $query->sum('amount');
        $expenseCount = $query->count();
        $averageExpense = $expenseCount > 0 ? $totalExpenses / $expenseCount : 0;

        // Expenses by category
        $expensesByCategory = $query->with('expenseCategory')
            ->get()
            ->groupBy('expense_category_id')
            ->map(function ($expenses) {
                return [
                    'category_name' => optional($expenses->first()->expenseCategory)->name,
                    'total_amount' => $expenses->sum('amount'),
                    'count' => $expenses->count(),
                ];
            })
            ->values();

        // Totals by payment method
        $totalsByPaymentMethod = $query->get()
            ->groupBy('payment_method')
            ->map(function ($expenses, $method) {
                return [
                    'payment_method' => $method,
                    'total_amount' => $expenses->sum('amount'),
                ];
            })
            ->values();


        return response()->json([
            'total_expenses' => $totalExpenses,
            'expense_count' => $expenseCount,
            'average_expense' => round($averageExpense, 2),
            'expenses_by_category' => $expensesByCategory,
            'totals_by_payment_method' => $totalsByPaymentMethod,
        ]);
    }

    /**
     * Generate a PDF listing of expenses
     */
    public function pdf(Request $request)
    {
        $query = Expense::with(['expenseCategory', 'createdBy']);

        if ($request->has('category_id') && $request->category_id) {
            $query->where('expense_category_id', $request->category_id);
        }
        if ($request->has('date_from') && $request->date_from) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where('expense_date', '<=', $request->date_to);
        }
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort_by', 'expense_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $expenses = $query->orderBy($sortBy, $sortOrder)->get();

        $total = (clone $query)->sum('amount');
        $count = (clone $query)->count();
        $average = $count > 0 ? $total / $count : 0;
        $totalsByPaymentMethod = collect($expenses)
            ->groupBy('payment_method')
            ->map(function ($items, $method) {
                return [
                    'payment_method' => $method,
                    'total_amount' => $items->sum('amount'),
                ];
            })
            ->values()
            ->all();

        $pdf = new ExpenseListPdf($expenses, [
            'total' => $total,
            'count' => $count,
            'average' => round($average, 2),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'totals_by_payment_method' => $totalsByPaymentMethod,
        ], 'd-m-Y');
        $pdf->SetTitle('تقرير المصروفات');
        $pdf->render();

        return response($pdf->Output('expenses.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="expenses.pdf"');
    }

    /**
     * Web route: display expenses PDF inline in browser
     */
    public function pdfWeb(Request $request)
    {
        $query = Expense::with(['expenseCategory', 'createdBy']);

        if ($request->has('category_id') && $request->category_id) {
            $query->where('expense_category_id', $request->category_id);
        }
        if ($request->has('date_from') && $request->date_from) {
            $query->where('expense_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where('expense_date', '<=', $request->date_to);
        }
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sortBy = $request->get('sort_by', 'expense_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $expenses = $query->orderBy($sortBy, $sortOrder)->get();

        $total = (clone $query)->sum('amount');
        $count = (clone $query)->count();
        $average = $count > 0 ? $total / $count : 0;
        $totalsByPaymentMethod = collect($expenses)
            ->groupBy('payment_method')
            ->map(function ($items, $method) {
                return [
                    'payment_method' => $method,
                    'total_amount' => $items->sum('amount'),
                ];
            })
            ->values()
            ->all();

        $pdf = new ExpenseListPdf($expenses, [
            'total' => $total,
            'count' => $count,
            'average' => round($average, 2),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'totals_by_payment_method' => $totalsByPaymentMethod,
        ], 'd-m-Y');
        $pdf->SetTitle('تقرير المصروفات');
        $pdf->render();

        return response($pdf->Output('expenses.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="expenses.pdf"');
    }
}
