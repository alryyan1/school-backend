<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ExpenseListPdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

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
            'payment_method' => 'required|in:cash,bankak,fawri,ocash',
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
            'payment_method' => 'sometimes|required|in:cash,bankak,fawri,ocash',
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

    /**
     * Export expenses to Excel
     */
    public function exportExcel(Request $request)
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

        $total = $expenses->sum('amount');
        $count = $expenses->count();
        $average = $count > 0 ? $total / $count : 0;

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('المصروفات');

        // Workbook defaults & RTL
        $spreadsheet->getProperties()
            ->setCreator(config('app.name'))
            ->setTitle('تقرير المصروفات')
            ->setSubject('تقرير المصروفات');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
        $sheet->setRightToLeft(true);
        $sheet->getDefaultRowDimension()->setRowHeight(20);

        // Build filter info
        $filterInfo = [];
        if ($request->has('category_id') && $request->category_id) {
            $category = ExpenseCategory::find($request->category_id);
            if ($category) {
                $filterInfo[] = "الفئة: " . $category->name;
            }
        }
        if ($request->has('date_from') && $request->date_from) {
            $filterInfo[] = "من تاريخ: " . $request->date_from;
        }
        if ($request->has('date_to') && $request->date_to) {
            $filterInfo[] = "إلى تاريخ: " . $request->date_to;
        }
        if ($request->has('search') && $request->search) {
            $filterInfo[] = "البحث: " . $request->search;
        }

        // Title row (row 1)
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'تقرير المصروفات (' . now()->format('Y-m-d H:i') . ')');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F4E78']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Filter info row (row 2)
        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', empty($filterInfo) ? 'بدون فلاتر' : ('فلترة: ' . implode(' | ', $filterInfo)));
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']]
        ]);

        // Headers (row 3)
        $headers = [
            'A3' => 'رقم',
            'B3' => 'العنوان',
            'C3' => 'الفئة',
            'D3' => 'المبلغ',
            'E3' => 'التاريخ',
            'F3' => 'طريقة الدفع',
            'G3' => 'الوصف',
            'H3' => 'المستخدم'
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style headers
        $headerRange = 'A3:H3';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Add data rows
        $row = 4;
        $dataStartRow = $row;
        foreach ($expenses as $expense) {
            $sheet->setCellValue("A{$row}", $expense->id);
            $sheet->setCellValue("B{$row}", $expense->title);
            $sheet->setCellValue("C{$row}", $expense->expenseCategory->name ?? '');
            $sheet->setCellValue("D{$row}", number_format($expense->amount, 2));
            $sheet->setCellValue("E{$row}", date('Y-m-d', strtotime($expense->expense_date)));
            $sheet->setCellValue("F{$row}", $this->translatePaymentMethod($expense->payment_method));
            $sheet->setCellValue("G{$row}", $expense->description ?? '');
            $sheet->setCellValue("H{$row}", $expense->createdBy->name ?? 'غير محدد');

            // Style data rows
            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            $row++;
        }

        // Add auto-filter capability to headers and data
        $dataEndRow = $row - 1;
        if ($dataEndRow >= $dataStartRow) {
            $sheet->setAutoFilter("A3:H{$dataEndRow}");
        }

        // Summary section
        $summaryRow = $row + 1;
        $sheet->setCellValue("C{$summaryRow}", 'الإجمالي:');
        $sheet->setCellValue("D{$summaryRow}", number_format($total, 2));
        $sheet->getStyle("C{$summaryRow}:D{$summaryRow}")->getFont()->setBold(true);

        $summaryRow++;
        $sheet->setCellValue("C{$summaryRow}", 'عدد المصروفات:');
        $sheet->setCellValue("D{$summaryRow}", $count);
        $sheet->getStyle("C{$summaryRow}:D{$summaryRow}")->getFont()->setBold(true);

        $summaryRow++;
        $sheet->setCellValue("C{$summaryRow}", 'المتوسط:');
        $sheet->setCellValue("D{$summaryRow}", number_format($average, 2));
        $sheet->getStyle("C{$summaryRow}:D{$summaryRow}")->getFont()->setBold(true);

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Freeze panes below header
        $sheet->freezePane('A4');

        // Create writer and output
        $writer = new Xlsx($spreadsheet);
        $filename = 'expenses_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Translate payment method to Arabic.
     */
    private function translatePaymentMethod($method): string
    {
        $translations = [
            'cash' => 'نقدي',
            'bankak' => 'بنكاك',
            'fawri' => 'فوري',
            'ocash' => 'أوكاش',
        ];
        return $translations[$method] ?? $method;
    }
}
