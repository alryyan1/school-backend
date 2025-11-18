<?php

namespace App\Http\Controllers;

use App\Models\OtherRevenue;
use App\Models\RevenueCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Helpers\OtherRevenueListPdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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

    /**
     * Generate a PDF listing of other revenues
     */
    public function pdfWeb(Request $request)
    {
        $query = OtherRevenue::with(['revenueCategory', 'user']);

        if ($request->has('category_id') && $request->category_id) {
            $query->where('revenue_category_id', $request->category_id);
        }
        if ($request->has('date_from') && $request->date_from) {
            $query->where('revenue_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where('revenue_date', '<=', $request->date_to);
        }
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('desc', 'like', "%{$search}%");
        }

        $sortBy = $request->get('sort_by', 'revenue_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $revenues = $query->orderBy($sortBy, $sortOrder)->get();

        $total = (clone $query)->sum('amount');
        $count = (clone $query)->count();
        $average = $count > 0 ? $total / $count : 0;
        $totalsByPaymentMethod = collect($revenues)
            ->groupBy('payment_method')
            ->map(function ($items, $method) {
                return [
                    'payment_method' => $method,
                    'total_amount' => $items->sum('amount'),
                ];
            })
            ->values()
            ->all();

        $pdf = new OtherRevenueListPdf($revenues, [
            'total' => $total,
            'count' => $count,
            'average' => round($average, 2),
            'date_from' => $request->get('date_from'),
            'date_to' => $request->get('date_to'),
            'totals_by_payment_method' => $totalsByPaymentMethod,
        ], 'd-m-Y');
        $pdf->SetTitle('تقرير الإيرادات الأخرى');
        $pdf->render();

        return response($pdf->Output('other_revenues.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="other_revenues.pdf"');
    }

    /**
     * Export other revenues to Excel
     */
    public function exportExcel(Request $request)
    {
        $query = OtherRevenue::with(['revenueCategory', 'user']);

        if ($request->has('category_id') && $request->category_id) {
            $query->where('revenue_category_id', $request->category_id);
        }
        if ($request->has('date_from') && $request->date_from) {
            $query->where('revenue_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where('revenue_date', '<=', $request->date_to);
        }
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('desc', 'like', "%{$search}%");
        }

        $sortBy = $request->get('sort_by', 'revenue_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $revenues = $query->orderBy($sortBy, $sortOrder)->get();

        $total = $revenues->sum('amount');
        $count = $revenues->count();
        $average = $count > 0 ? $total / $count : 0;

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('الإيرادات الأخرى');

        // Workbook defaults & RTL
        $spreadsheet->getProperties()
            ->setCreator(config('app.name'))
            ->setTitle('تقرير الإيرادات الأخرى')
            ->setSubject('تقرير الإيرادات الأخرى');
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
        $sheet->setRightToLeft(true);
        $sheet->getDefaultRowDimension()->setRowHeight(20);

        // Build filter info
        $filterInfo = [];
        if ($request->has('category_id') && $request->category_id) {
            $category = RevenueCategory::find($request->category_id);
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
        if ($request->has('payment_method') && $request->payment_method) {
            $filterInfo[] = "طريقة الدفع: " . ($request->payment_method === 'cash' ? 'نقدي' : 'بنكي');
        }
        if ($request->has('search') && $request->search) {
            $filterInfo[] = "البحث: " . $request->search;
        }

        // Title row (row 1)
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'تقرير الإيرادات الأخرى (' . now()->format('Y-m-d H:i') . ')');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1F4E78']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Filter info row (row 2)
        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', empty($filterInfo) ? 'بدون فلاتر' : ('فلترة: ' . implode(' | ', $filterInfo)));
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '666666']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']]
        ]);

        // Headers (row 3)
        $headers = [
            'A3' => 'رقم',
            'B3' => 'الوصف',
            'C3' => 'الفئة',
            'D3' => 'المبلغ',
            'E3' => 'التاريخ',
            'F3' => 'طريقة الدفع',
            'G3' => 'المستخدم'
        ];
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style headers
        $headerRange = 'A3:G3';
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
        foreach ($revenues as $revenue) {
            $sheet->setCellValue("A{$row}", $revenue->id);
            $sheet->setCellValue("B{$row}", $revenue->desc);
            $sheet->setCellValue("C{$row}", $revenue->revenueCategory->name ?? '');
            $sheet->setCellValue("D{$row}", number_format($revenue->amount, 2));
            $sheet->setCellValue("E{$row}", date('Y-m-d', strtotime($revenue->revenue_date)));
            $sheet->setCellValue("F{$row}", $revenue->payment_method === 'cash' ? 'نقدي' : 'بنكي');
            $sheet->setCellValue("G{$row}", $revenue->user->name ?? 'غير محدد');

            // Style data rows
            $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
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
            $sheet->setAutoFilter("A3:G{$dataEndRow}");
        }

        // Summary section
        $summaryRow = $row + 1;
        $sheet->setCellValue("C{$summaryRow}", 'الإجمالي:');
        $sheet->setCellValue("D{$summaryRow}", number_format($total, 2));
        $sheet->getStyle("C{$summaryRow}:D{$summaryRow}")->getFont()->setBold(true);

        $summaryRow++;
        $sheet->setCellValue("C{$summaryRow}", 'عدد الإيرادات:');
        $sheet->setCellValue("D{$summaryRow}", $count);
        $sheet->getStyle("C{$summaryRow}:D{$summaryRow}")->getFont()->setBold(true);

        $summaryRow++;
        $sheet->setCellValue("C{$summaryRow}", 'المتوسط:');
        $sheet->setCellValue("D{$summaryRow}", number_format($average, 2));
        $sheet->getStyle("C{$summaryRow}:D{$summaryRow}")->getFont()->setBold(true);

        // Auto-size columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Freeze panes below header
        $sheet->freezePane('A4');

        // Create writer and output
        $writer = new Xlsx($spreadsheet);
        $filename = 'other_revenues_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
