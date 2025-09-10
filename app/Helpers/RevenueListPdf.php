<?php

namespace App\Helpers;

use TCPDF;
use TCPDF_FONTS;

class RevenueListPdf extends TCPDF
{
    protected $students;
    protected $globalSummary = [];
    protected $perEnrollmentSummaries = [];
    protected $fontFamily = 'dejavusans';
    protected $reportTitle = 'تقرير الإيرادات - رسوم الطلاب';
    protected $logoPath = null;

    public function __construct($students, array $summary = [])
    {
        parent::__construct('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->students = $students;
        // Accept either a plain global summary or a structured array with by_enrollment
        $this->globalSummary = $summary;
        if (isset($summary['by_enrollment']) && is_array($summary['by_enrollment'])) {
            $this->perEnrollmentSummaries = $summary['by_enrollment'];
        }

        $this->setRTL(true);
        $this->SetMargins(15, 30, 15);
        $this->SetHeaderMargin(10);
        $this->SetFooterMargin(10);
        $this->SetAutoPageBreak(true, 20);
        $this->initializeFonts();

        // Resolve logo path if available
        if (function_exists('public_path')) {
            $possible = public_path('logo.png');
            if (file_exists($possible)) {
                $this->logoPath = $possible;
            }
        }
    }

    public function Header()
    {
        // Top bar
        if ($this->logoPath) {
            // Left: logo
            $this->Image($this->logoPath, $this->lMargin + 50, 5, 60,40);
        }

        // Title center
        $this->SetFont($this->fontFamily, 'B', 16);
        $this->SetTextColor(33, 33, 33);
        $this->Cell(0, 10, $this->reportTitle, 0, 1, 'C');

        // Sub header: printed at date/time (right aligned)
        $this->SetFont($this->fontFamily, '', 9);
        $this->SetTextColor(117, 117, 117);
        $this->Cell(0, 6, 'تاريخ الطباعة: ' . date('Y-m-d H:i'), 0, 1, 'R');

        // Divider
        $this->SetDrawColor(224, 224, 224);
        // $this->Line($this->lMargin, $this->GetY(), $this->w - $this->rMargin, $this->GetY());
        $this->Ln(3);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont($this->fontFamily, 'I', 8);
        $this->Cell(0, 10, 'صفحة ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C');
    }

    public function render()
    {
        $this->AddPage('L');
        $this->renderSummary();
        $this->Ln(4);
        $this->renderTable();
    }

    protected function renderSummary(): void
    {
        $this->SetFont($this->fontFamily, 'B', 11);
        $this->Cell(0, 8, 'الملخص', 0, 1, 'R');
        $this->SetFont($this->fontFamily, '', 10);

        $expected = $this->formatCurrency($this->globalSummary['total_expected'] ?? 0);
        $paid = $this->formatCurrency($this->globalSummary['total_paid'] ?? 0);
        $balance = $this->formatCurrency($this->globalSummary['total_balance'] ?? 0);
        $count = (int)($this->globalSummary['count'] ?? 0);

        // Styled summary boxes (2 columns)
        $this->SetFillColor(250, 250, 250);
        $this->SetDrawColor(230, 230, 230);
        $this->SetTextColor(66, 66, 66);
        $w = ($this->w - $this->lMargin - $this->rMargin);
        $col = $w / 2;
        $rowH = 8;

        $this->Cell($col, $rowH, 'إجمالي المتوقع: ' . $expected, 1, 0, 'R', true);
        $this->Cell($col, $rowH, 'إجمالي المدفوع: ' . $paid, 1, 1, 'R', true);
        $this->Cell($col, $rowH, 'إجمالي المتبقي: ' . $balance, 1, 0, 'R', true);
        $this->Cell($col, $rowH, 'عدد الطلاب: ' . number_format($count, 0), 1, 1, 'R', true);
    }

    protected function renderTable(): void
    {
        $this->Ln(4);
        $this->SetFont($this->fontFamily, 'B', 10);
        // Header styling
        $this->SetFillColor(33, 150, 243); // blue
        $this->SetTextColor(255, 255, 255);
        $this->SetDrawColor(197, 197, 197);

        // Calculate usable width (page width minus left and right margins)
        $usableWidth = $this->w - $this->lMargin - $this->rMargin;
        // Define column width proportions that sum to 1.0
        $proportions = [
            'enrollment' => 0.08,
            'student' => 0.22,
            'school' => 0.14,
            'grade' => 0.09,
            'classroom' => 0.09,
            'debit' => 0.12,
            'credit' => 0.10,
            'discount' => 0.08,
            'balance' => 0.08,
        ];
        // Map to actual widths
        $w = array_map(function ($p) use ($usableWidth) { return $usableWidth * $p; }, $proportions);

        // Headers
        $this->Cell($w['enrollment'], 8, 'التسجيل', 1, 0, 'C', true);
        $this->Cell($w['student'], 8, 'اسم الطالب', 1, 0, 'C', true);
        $this->Cell($w['school'], 8, 'المدرسة', 1, 0, 'C', true);
        $this->Cell($w['grade'], 8, 'المرحلة', 1, 0, 'C', true);
        $this->Cell($w['classroom'], 8, 'الفصل', 1, 0, 'C', true);
        $this->Cell($w['debit'], 8, 'مدين', 1, 0, 'C', true);
        $this->Cell($w['credit'], 8, 'دائن', 1, 0, 'C', true);
        $this->Cell($w['discount'], 8, 'خصم', 1, 0, 'C', true);
        $this->Cell($w['balance'], 8, 'المتبقي', 1, 1, 'C', true);

        // Rows
        $this->SetFont($this->fontFamily, '', 9);
        $this->SetTextColor(33, 33, 33);
        $fill = false;

        $grandDebit = 0.0;
        $grandCredit = 0.0;
        $grandDiscount = 0.0;
        $grandBalance = 0.0;

        // Sort students by first enrollment id desc (fallback 0)
        $studentsSorted = $this->students;
        if (is_object($this->students) && method_exists($this->students, 'sortByDesc')) {
            $studentsSorted = $this->students->sortByDesc(function ($student) {
                $first = $student->enrollments->sortByDesc(function ($en) {
                    return $en->created_at?->timestamp ?? 0;
                })->first();
                return $first->id ?? 0;
            });
        }

        foreach ($studentsSorted as $student) {
            // First enrollment for display details (may be null)
            $firstEnrollment = $student->enrollments->sortByDesc(function ($en) {
                return $en->created_at?->timestamp ?? 0;
            })->first();

            // Per-enrollment totals via summaries if available; fallback to legacy calc
            $debit = 0.0;     // total fees
            $credit = 0.0;    // total payments
            $discount = 0.0;  // total discounts
            $balance = 0.0;   // remaining
            if ($firstEnrollment && isset($this->perEnrollmentSummaries[$firstEnrollment->id])) {
                $sum = $this->perEnrollmentSummaries[$firstEnrollment->id];
                $debit = (float)($sum['total_fees'] ?? 0);
                $credit = (float)($sum['total_payments'] ?? 0);
                $discount = (float)($sum['total_discounts'] ?? 0);
                $refunds = (float)($sum['total_refunds'] ?? 0);
                $adjustments = (float)($sum['total_adjustments'] ?? 0);
                $balance = max($debit - $credit - $discount + $refunds + $adjustments, 0);
            } else {
                // Legacy fallback using feeInstallments sums
                foreach ($student->enrollments as $enrollment) {
                    $debit += (float) ($enrollment->feeInstallments?->sum('amount_due') ?? 0);
                    $credit += (float) ($enrollment->feeInstallments?->sum('amount_paid') ?? 0);
                }
                $balance = max($debit - $credit, 0);
            }

            $grandDebit += $debit;
            $grandCredit += $credit;
            $grandDiscount += $discount;
            $grandBalance += $balance;

            $enrollmentId = $firstEnrollment->id ?? '-';
            $studentName = $student->student_name ?? '-';
            $schoolName = $firstEnrollment->school->name ?? '-';
            $gradeName = $firstEnrollment->gradeLevel->name ?? '-';
            $classroomName = $firstEnrollment->classroom->name ?? '-';

            // Zebra stripe
            if ($fill) {
                $this->SetFillColor(250, 250, 250);
            } else {
                $this->SetFillColor(255, 255, 255);
            }
            $fill = !$fill;

            $this->Cell($w['enrollment'], 8, (string)$enrollmentId, 1, 0, 'C', true);
            $this->Cell($w['student'], 8, $studentName, 1, 0, 'R', true);
            $this->Cell($w['school'], 8, $schoolName, 1, 0, 'R', true);
            $this->Cell($w['grade'], 8, $gradeName, 1, 0, 'R', true);
            $this->Cell($w['classroom'], 8, $classroomName, 1, 0, 'R', true);
            $this->Cell($w['debit'], 8, $this->formatCurrency($debit), 1, 0, 'C', true);
            $this->Cell($w['credit'], 8, $this->formatCurrency($credit), 1, 0, 'C', true);
            $this->Cell($w['discount'], 8, $this->formatCurrency($discount), 1, 0, 'C', true);
            $this->Cell($w['balance'], 8, $this->formatCurrency($balance), 1, 1, 'C', true);
        }

        // Totals row
        $this->SetFont($this->fontFamily, 'B', 10);
        $this->SetFillColor(236, 239, 241);
        $this->SetTextColor(33, 33, 33);
        $this->Cell($w['enrollment'] + $w['student'] + $w['school'] + $w['grade'] + $w['classroom'], 9, 'الإجمالي', 1, 0, 'R', true);
        $this->Cell($w['debit'], 9, $this->formatCurrency($grandDebit), 1, 0, 'C', true);
        $this->Cell($w['credit'], 9, $this->formatCurrency($grandCredit), 1, 0, 'C', true);
        $this->Cell($w['discount'], 9, $this->formatCurrency($grandDiscount), 1, 0, 'C', true);
        $this->Cell($w['balance'], 9, $this->formatCurrency($grandBalance), 1, 1, 'C', true);
    }

    protected function initializeFonts(): void
    {
        $this->SetFont($this->fontFamily, '', 10);
        $this->setHeaderFont([$this->fontFamily, '', 12]);
        $this->setFooterFont([$this->fontFamily, '', 8]);

        $fontPath = function_exists('public_path') ? public_path('fonts/arial.ttf') : null;
        if ($fontPath && file_exists($fontPath)) {
            try {
                $family = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
                if ($family) {
                    $this->fontFamily = $family;
                }
            } catch (\Exception $e) {
                // keep default font
            }
        }
    }

    protected function formatCurrency($amount): string
    {
        $value = (float)($amount ?? 0);
        return number_format($value, 0);
    }
}


