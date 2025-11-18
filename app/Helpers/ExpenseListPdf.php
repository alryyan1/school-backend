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
        parent::__construct('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->expenses = $expenses;
        $this->summary = $summary;
        $this->dateFormat = $dateFormat;

        $this->setRTL(true);
        // Optimized margins for landscape: smaller side margins, standard top/bottom
        $this->SetMargins(10, 30, 10);
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
            $this->Image($logoPath, 50, 5, 50);
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

        // Calculate available width for landscape orientation
        $availableWidth = $this->w - $this->lMargin - $this->rMargin;
        
        // Define column width proportions for landscape (total should be ~1.0)
        // Date: 8%, Category: 24% (doubled), Payment: 10%, Amount: 10%, User: 12%, Description: 36%
        $colProportions = [
            'date' => 0.08,
            'category' => 0.24,
            'payment' => 0.10,
            'amount' => 0.10,
            'user' => 0.12,
            'description' => 0.36
        ];
        
        // Calculate actual column widths
        $colWidths = [
            'date' => $availableWidth * $colProportions['date'],
            'category' => $availableWidth * $colProportions['category'],
            'payment' => $availableWidth * $colProportions['payment'],
            'amount' => $availableWidth * $colProportions['amount'],
            'user' => $availableWidth * $colProportions['user'],
            'description' => $availableWidth * $colProportions['description']
        ];

        // Headers
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(220, 220, 220);
        $this->SetFillColor(245, 247, 250);
        $this->Cell($colWidths['date'], 8, 'التاريخ', 1, 0, 'C', true);
        $this->Cell($colWidths['category'], 8, 'الفئة', 1, 0, 'C', true);
        $this->Cell($colWidths['payment'], 8, 'طريقة الدفع', 1, 0, 'C', true);
        $this->Cell($colWidths['amount'], 8, 'المبلغ', 1, 0, 'C', true);
        $this->Cell($colWidths['user'], 8, 'المستخدم', 1, 0, 'C', true);
        $this->Cell($colWidths['description'], 8, 'الوصف', 1, 1, 'C', true);

        $this->SetFont($this->fontFamily, '', 9);
        $rowFill = false;
        $descWidth = $colWidths['description'];
        $lineHeight = 6;
        
        foreach ($this->expenses as $expense) {
            $date = $expense->expense_date ? date($this->dateFormat, strtotime($expense->expense_date)) : '-';
            $category = $expense->expenseCategory->name ?? '-';
            $payment = $expense->payment_method === 'cash' ? 'نقدي' : ($expense->payment_method === 'bankak' ? 'بنكاك' : ($expense->payment_method ?? '-'));
            $title = $expense->title ?? '';
            $desc = $expense->description ?? '';
            $combined = trim($title . ' - ' . $desc, ' -');
            $amount = number_format((float)$expense->amount, 2);
            $user = $expense->createdBy->name ?? '-';

            // Calculate MultiCell height first (without drawing) to check if row fits
            $measureY = $this->GetY();
            $this->MultiCell($descWidth, $lineHeight, $combined, 0, 'R', false, 0);
            $multiCellHeight = $this->GetY() - $measureY;
            $this->SetY($measureY); // Reset to start position
            
            // Ensure minimum height
            $rowHeight = max($lineHeight, $multiCellHeight);
            
            // Check if row fits on current page (with margin for page break)
            // Use a safety margin to ensure we don't cut off rows
            $safetyMargin = 10;
            $availableHeight = $this->h - $this->GetY() - $this->bMargin - $safetyMargin;
            if ($rowHeight > $availableHeight && $this->GetY() > $this->tMargin + 20) {
                // Row doesn't fit, add new page
                $this->AddPage();
                // Redraw table header on new page
                $this->SetFont($this->fontFamily, 'B', 10);
                $this->SetFillColor(245, 247, 250);
                $this->SetTextColor(0, 0, 0);
                $this->SetDrawColor(220, 220, 220);
                $this->Cell($colWidths['date'], 8, 'التاريخ', 1, 0, 'C', true);
                $this->Cell($colWidths['category'], 8, 'الفئة', 1, 0, 'C', true);
                $this->Cell($colWidths['payment'], 8, 'طريقة الدفع', 1, 0, 'C', true);
                $this->Cell($colWidths['amount'], 8, 'المبلغ', 1, 0, 'C', true);
                $this->Cell($colWidths['user'], 8, 'المستخدم', 1, 0, 'C', true);
                $this->Cell($colWidths['description'], 8, 'الوصف', 1, 1, 'C', true);
                $this->SetFont($this->fontFamily, '', 9);
            }

            // Store starting position after potential page break
            $startX = $this->GetX();
            $startY = $this->GetY();
            
            // Set fill color for alternating rows
            if ($rowFill) {
                $this->SetFillColor(252, 252, 252);
            } else {
                $this->SetFillColor(255, 255, 255);
            }
            $rowFill = !$rowFill;
            
            // Draw all cells except description with the calculated row height
            // Date
            $this->Cell($colWidths['date'], $rowHeight, $date, 1, 0, 'C', true);
            
            // Category
            $this->Cell($colWidths['category'], $rowHeight, $category, 1, 0, 'R', true);
            
            // Payment method
            $this->Cell($colWidths['payment'], $rowHeight, $payment, 1, 0, 'C', true);
            
            // Amount
            $this->Cell($colWidths['amount'], $rowHeight, $amount, 1, 0, 'C', true);
            
            // User
            $this->Cell($colWidths['user'], $rowHeight, $user, 1, 0, 'R', true);
            
            // Description (MultiCell) - last column, draw with border
            $descX = $this->GetX();
            $this->MultiCell($descWidth, $lineHeight, $combined, 1, 'R', true, ln: 1);
            
            // Get the actual Y position after MultiCell (this is the real height used)
            $actualEndY = $this->GetY();
            $actualRowHeight = $actualEndY - $startY;
            
            // Ensure we have a valid height
            if ($actualRowHeight < $lineHeight) {
                $actualRowHeight = $lineHeight;
            }
            
            // If actual height differs from calculated, redraw all previous cells
            if (abs($actualRowHeight - $rowHeight) > 0.1) {
                $this->SetXY($startX, $startY);
                $this->Cell($colWidths['date'], $actualRowHeight, $date, 1, 0, 'C', true);
                $this->Cell($colWidths['category'], $actualRowHeight, $category, 1, 0, 'R', true);
                $this->Cell($colWidths['payment'], $actualRowHeight, $payment, 1, 0, 'C', true);
                $this->Cell($colWidths['amount'], $actualRowHeight, $amount, 1, 0, 'C', true);
                $this->Cell($colWidths['user'], $actualRowHeight, $user, 1, 0, 'R', true);
                // Update actualEndY after redrawing
                $actualEndY = $startY + $actualRowHeight;
            }
            
            // Move to the next row: use the actual end Y position
            // This ensures we're at the correct position for the next iteration
            $this->SetY($actualEndY);
            
            // Check if we're too close to the bottom margin and need a page break
            $remainingHeight = $this->h - $this->GetY() - $this->bMargin;
            if ($remainingHeight < ($lineHeight * 2)) {
                // Not enough space for another row, add new page
                $this->AddPage();
                // Redraw table header on new page
                $this->SetFont($this->fontFamily, 'B', 10);
                $this->SetFillColor(245, 247, 250);
                $this->SetTextColor(0, 0, 0);
                $this->SetDrawColor(220, 220, 220);
                $this->Cell($colWidths['date'], 8, 'التاريخ', 1, 0, 'C', true);
                $this->Cell($colWidths['category'], 8, 'الفئة', 1, 0, 'C', true);
                $this->Cell($colWidths['payment'], 8, 'طريقة الدفع', 1, 0, 'C', true);
                $this->Cell($colWidths['amount'], 8, 'المبلغ', 1, 0, 'C', true);
                $this->Cell($colWidths['user'], 8, 'المستخدم', 1, 0, 'C', true);
                $this->Cell($colWidths['description'], 8, 'الوصف', 1, 1, 'C', true);
                $this->SetFont($this->fontFamily, '', 9);
            }
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


