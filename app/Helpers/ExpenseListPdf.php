<?php

namespace App\Helpers;

use TCPDF;
use TCPDF_FONTS;

class ExpenseListPdf extends TCPDF
{
    protected $expenses;
    protected $summary;
    protected $dateFormat;
    protected $fontFamily = 'dejavusans';
    protected $reportTitle = 'تقرير المصروفات';
    protected $brandColor = [33, 150, 243]; // Blue accent

    public function __construct($expenses, array $summary = [], string $dateFormat = 'd-m-Y')
    {
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->expenses = $expenses;
        $this->summary = $summary;
        $this->dateFormat = $dateFormat;

        $this->setRTL(true);
        $this->SetMargins(15, 30, 15);
        $this->SetHeaderMargin(10);
        $this->SetFooterMargin(10);
        $this->SetAutoPageBreak(true, 20);
        $this->initializeFonts();
    }

    public function Header()
    {
        // Logo (if available)
        $logoPath = function_exists('public_path') ? public_path('logo.png') : null;
        if ($logoPath && file_exists($logoPath)) {
            $this->Image($logoPath, $this->lMargin, 10, 20);
        }

        // Title and date range
        $this->SetFont($this->fontFamily, 'B', 16);
        $this->SetTextColor($this->brandColor[0], $this->brandColor[1], $this->brandColor[2]);
        $this->Cell(0, 10, $this->reportTitle, 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);
        $this->SetFont($this->fontFamily, '', 10);
        $dateFrom = $this->summary['date_from'] ?? null;
        $dateTo = $this->summary['date_to'] ?? null;
        if ($dateFrom || $dateTo) {
            $from = $dateFrom ? date($this->dateFormat, strtotime($dateFrom)) : '—';
            $to = $dateTo ? date($this->dateFormat, strtotime($dateTo)) : '—';
            $this->Cell(0, 6, 'الفترة: ' . $from . ' إلى ' . $to, 0, 1, 'C');
        }

        // Divider
        $this->SetDrawColor($this->brandColor[0], $this->brandColor[1], $this->brandColor[2]);
        $this->Line($this->lMargin, $this->GetY(), $this->w - $this->rMargin, $this->GetY());
        $this->Ln(4);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont($this->fontFamily, 'I', 8);
        $this->Cell(0, 10, 'صفحة ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C');
    }

    public function render()
    {
        $this->AddPage();
        $this->renderSummary();
        $this->Ln(4);
        $this->renderTable();
    }

    protected function renderSummary(): void
    {
        // Summary cards style
        $this->SetFillColor(248, 249, 250);
        $this->SetDrawColor(230, 230, 230);
        $this->SetFont($this->fontFamily, 'B', 11);
        $this->Cell(0, 8, 'الملخص', 0, 1, 'R');
        $this->SetFont($this->fontFamily, '', 10);

        $total = number_format((float)($this->summary['total'] ?? 0), 2);
        $count = (int)($this->summary['count'] ?? 0);
        $avg = number_format((float)($this->summary['average'] ?? 0), 2);

        // Three-column summary
        $colWidth = ($this->w - $this->lMargin - $this->rMargin) / 3;
        $this->Cell($colWidth, 10, 'إجمالي المصروفات: ' . $total, 1, 0, 'C', true);
        $this->Cell($colWidth, 10, 'عدد السجلات: ' . $count, 1, 0, 'C', true);
        $this->Cell($colWidth, 10, 'متوسط المصروف: ' . $avg, 1, 1, 'C', true);

        // Totals by payment method, if provided
        if (!empty($this->summary['totals_by_payment_method']) && is_array($this->summary['totals_by_payment_method'])) {
            $this->Ln(2);
            $this->SetFont($this->fontFamily, 'B', 10);
            $this->Cell(0, 8, 'الإجمالي حسب طريقة الدفع', 0, 1, 'R');
            $this->SetFont($this->fontFamily, '', 10);
            foreach ($this->summary['totals_by_payment_method'] as $item) {
                $name = $item['payment_method'] === 'cash' ? 'نقدي' : ($item['payment_method'] === 'bankak' ? 'بنكاك' : ($item['payment_method'] ?? '-'));
                $amt = number_format((float)($item['total_amount'] ?? 0), 2);
                $this->Cell(0, 6, $name . ': ' . $amt, 0, 1, 'R');
            }
        }
    }

    protected function renderTable(): void
    {
        $this->Ln(4);
        $this->SetFont($this->fontFamily, 'B', 10);
        $this->SetFillColor(240, 240, 240);

        // Headers
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(220, 220, 220);
        $this->SetFillColor(245, 247, 250);
        $this->Cell(22, 8, 'التاريخ', 1, 0, 'C', true);
        $this->Cell(26, 8, 'الفئة', 1, 0, 'C', true);
        $this->Cell(18, 8, 'طريقة الدفع', 1, 0, 'C', true);
        $this->Cell(72, 8, 'الوصف', 1, 0, 'C', true);
        $this->Cell(24, 8, 'المبلغ', 1, 0, 'C', true);
        $this->Cell(24, 8, 'المستخدم', 1, 1, 'C', true);

        $this->SetFont($this->fontFamily, '', 9);
        $rowFill = false;
        foreach ($this->expenses as $expense) {
            $date = $expense->expense_date ? date($this->dateFormat, strtotime($expense->expense_date)) : '-';
            $category = $expense->expenseCategory->name ?? '-';
            $payment = $expense->payment_method === 'cash' ? 'نقدي' : ($expense->payment_method === 'bankak' ? 'بنكاك' : ($expense->payment_method ?? '-'));
            $title = $expense->title ?? '';
            $desc = $expense->description ?? '';
            $combined = trim($title . ' - ' . $desc, ' -');
            $amount = number_format((float)$expense->amount, 2);
            $user = $expense->createdBy->name ?? '-';

            // alternating background
            if ($rowFill) {
                $this->SetFillColor(252, 252, 252);
            } else {
                $this->SetFillColor(255, 255, 255);
            }
            $rowFill = !$rowFill;

            $this->Cell(22, 8, $date, 1, 0, 'C', true);
            $this->Cell(26, 8, $category, 1, 0, 'R', true);
            $this->Cell(18, 8, $payment, 1, 0, 'C', true);
            $this->Cell(72, 8, $combined, 1, 0, 'R', true);
            $this->Cell(24, 8, $amount, 1, 0, 'C', true);
            $this->Cell(24, 8, $user, 1, 1, 'R', true);
        }
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
                // ignore and keep default
            }
        }
    }
}


