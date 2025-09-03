<?php

namespace App\Helpers;

use TCPDF;
use TCPDF_FONTS;
use Exception;

class StudentLedgerPdf extends TCPDF
{
    protected $enrollment;
    protected $ledgerEntries;
    protected $summary;
    protected $currentBalance;
    
    // Configuration
    private const DEFAULT_CURRENCY = 'جنيه';
    private const DEFAULT_DATE_FORMAT = 'd/m/Y';
    private const DEFAULT_HEADER_TITLE_AR = 'دفتر حسابات الطالب';
    private const DEFAULT_HEADER_TITLE_EN = 'Student Ledger Report';
    private const DEFAULT_TITLE_FONT_SIZE = 16;
    private const DEFAULT_SECTION_TITLE_FONT_SIZE = 12;
    private const DEFAULT_BASE_FONT_SIZE = 10;
    private const DEFAULT_STUDENT_NAME_FONT_SIZE = 14;
    private const DEFAULT_STUDENT_INFO_FONT_SIZE = 11;
    private const DEFAULT_HEADER_FONT_SIZE = 12;
    private const DEFAULT_FOOTER_FONT_SIZE = 8;
    private const DEFAULT_MARGIN_LEFT = 15;
    private const DEFAULT_MARGIN_TOP = 35;
    private const DEFAULT_MARGIN_RIGHT = 15;
    private const DEFAULT_HEADER_MARGIN = 10;
    private const DEFAULT_FOOTER_MARGIN = 10;
    private const DEFAULT_AUTO_PAGE_BREAK_MARGIN = 25;
    private const DEFAULT_IS_RTL = true;
    
    // Column widths for table
    private const COL_WIDTH_DATE = 25;
    private const COL_WIDTH_TYPE = 25;
    private const COL_WIDTH_DESCRIPTION = 50;
    private const COL_WIDTH_AMOUNT = 25;
    private const COL_WIDTH_BALANCE = 35;
    private const COL_WIDTH_REFERENCE = 25;
    
    // Runtime options
    protected $isRtl;
    protected $currency;
    protected $dateFormat;
    protected $fontFamily;

    public function __construct($enrollment, $ledgerEntries, $summary, $currentBalance, array $options = [])
    {
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $this->enrollment = $enrollment;
        $this->ledgerEntries = $ledgerEntries;
        $this->summary = $summary;
        $this->currentBalance = $currentBalance;

        $this->currency = $options['currency'] ?? self::DEFAULT_CURRENCY;
        $this->dateFormat = $options['date_format'] ?? self::DEFAULT_DATE_FORMAT;
        $this->isRtl = $options['rtl'] ?? self::DEFAULT_IS_RTL;

        $this->initializeDocumentMetadata();
        $this->initializeLayout();
        $this->initializeFonts();
    }
    
    public function Header()
    {
        // Title
        $this->SetFont($this->fontFamily, 'B', self::DEFAULT_TITLE_FONT_SIZE);
        $this->Cell(0, 15, self::DEFAULT_HEADER_TITLE_AR, 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(8);

        $this->renderHeaderStudentInfo();
        $this->drawSeparator(10, 18);
    }
    
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont($this->fontFamily, 'I', self::DEFAULT_FOOTER_FONT_SIZE);
        $this->Cell(0, 10, 'صفحة ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
    
    public function generateLedger()
    {
        // Add a page
        $this->AddPage();
        
        $this->renderSummarySection();
        $this->drawSeparator(0, 15);
        $this->renderLedgerTable();
    }
    
    private function getTransactionTypeText($type)
    {
        switch ($type) {
            case 'fee':
                return 'رسوم';
            case 'payment':
                return 'دفع';
            case 'discount':
                return 'خصم';
            case 'refund':
                return 'استرداد';
            case 'adjustment':
                return 'تعديل';
            default:
                return $type;
        }
    }

    private function initializeDocumentMetadata(): void
    {
        $studentName = $this->enrollment->student->student_name ?? 'غير محدد';
        $this->SetCreator('School Management System');
        $this->SetAuthor('School Management System');
        $this->SetTitle(self::DEFAULT_HEADER_TITLE_AR . ' - ' . $studentName);
        $this->SetSubject(self::DEFAULT_HEADER_TITLE_EN);
        $this->SetHeaderData('', 0, self::DEFAULT_HEADER_TITLE_AR, self::DEFAULT_HEADER_TITLE_EN);
    }

    private function initializeLayout(): void
    {
        $this->SetMargins(self::DEFAULT_MARGIN_LEFT, self::DEFAULT_MARGIN_TOP, self::DEFAULT_MARGIN_RIGHT);
        $this->SetHeaderMargin(self::DEFAULT_HEADER_MARGIN);
        $this->SetFooterMargin(self::DEFAULT_FOOTER_MARGIN);
        $this->SetAutoPageBreak(true, self::DEFAULT_AUTO_PAGE_BREAK_MARGIN);
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->setFontSubsetting(true);
        $this->SetDefaultMonospacedFont('courier');
        $this->setRTL($this->isRtl);
    }

    private function initializeFonts(): void
    {
        $this->fontFamily = 'dejavusans';

        $fontPath = function_exists('public_path') ? public_path('fonts/arial.ttf') : null;
        if ($fontPath && file_exists($fontPath)) {
            try {
                $registeredFamily = TCPDF_FONTS::addTTFfont($fontPath, 'TrueTypeUnicode', '', 96);
                if ($registeredFamily) {
                    $this->fontFamily = $registeredFamily;
                }
            } catch (Exception $e) {
                // Fallback will be used
            }
        }

        $this->SetFont($this->fontFamily, '', self::DEFAULT_BASE_FONT_SIZE);
        $this->setHeaderFont([$this->fontFamily, '', self::DEFAULT_HEADER_FONT_SIZE]);
        $this->setFooterFont([$this->fontFamily, '', self::DEFAULT_FOOTER_FONT_SIZE]);
    }

    private function renderHeaderStudentInfo(): void
    {
        $this->SetFont($this->fontFamily, 'B', self::DEFAULT_SECTION_TITLE_FONT_SIZE);
        $this->Cell(0, 10, 'معلومات الطالب:', 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln(6);

        // Student name larger
        $this->SetFont($this->fontFamily, 'B', self::DEFAULT_STUDENT_NAME_FONT_SIZE);
        $studentName = $this->enrollment->student->student_name ?? 'غير محدد';
        $this->Cell(0, 9, 'اسم الطالب: ' . $studentName, 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln(2);

        // Other student details slightly larger
        $this->SetFont($this->fontFamily, '', self::DEFAULT_STUDENT_INFO_FONT_SIZE);

        $registrationId = $this->enrollment->id ?? '-';
        $this->Cell(0, 8, 'رقم التسجيل: ' . $registrationId, 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln();

        if ($this->enrollment->school && $this->enrollment->school->name) {
            $this->Cell(0, 8, 'المدرسة: ' . $this->enrollment->school->name, 0, false, 'R', 0, '', 0, false, 'M', 'M');
            $this->Ln();
        }

        if ($this->enrollment->gradeLevel && $this->enrollment->gradeLevel->name) {
            $this->Cell(0, 8, 'المرحلة: ' . $this->enrollment->gradeLevel->name, 0, false, 'R', 0, '', 0, false, 'M', 'M');
            $this->Ln();
        }

        if ($this->enrollment->classroom && $this->enrollment->classroom->name) {
            $this->Cell(0, 8, 'الفصل: ' . $this->enrollment->classroom->name, 0, false, 'R', 0, '', 0, false, 'M', 'M');
            $this->Ln();
        }

        $this->Cell(0, 8, 'تاريخ التقرير: ' . date($this->dateFormat), 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln(12);
    }

    private function renderSummarySection(): void
    {
        $this->SetFont($this->fontFamily, 'B', self::DEFAULT_SECTION_TITLE_FONT_SIZE);
        $this->Cell(0, 10, 'ملخص الحساب:', 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln(8);

        $this->SetFont($this->fontFamily, '', self::DEFAULT_BASE_FONT_SIZE);

        $this->writeKeyValueRow('إجمالي الرسوم:', $this->formatMoney($this->summary['total_fees'] ?? 0));
        $this->writeKeyValueRow('إجمالي المدفوع:', $this->formatMoney($this->summary['total_payments'] ?? 0));
        $this->writeKeyValueRow('إجمالي الخصومات:', $this->formatMoney($this->summary['total_discounts'] ?? 0));
        $this->writeKeyValueRow('الرصيد الحالي:', $this->formatMoney($this->currentBalance ?? 0));
        $this->Ln(7);
    }

    private function renderLedgerTable(): void
    {
        $this->SetFont($this->fontFamily, 'B', self::DEFAULT_SECTION_TITLE_FONT_SIZE);
        $this->Cell(0, 10, 'تفاصيل المعاملات:', 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln(8);

        $this->renderLedgerTableHeader();
        $this->renderLedgerTableRows();
    }

    private function renderLedgerTableHeader(): void
    {
        $this->SetFont($this->fontFamily, 'B', 9);
        $this->SetFillColor(240, 240, 240);

        $this->Cell(self::COL_WIDTH_DATE, 8, 'التاريخ', 1, false, 'C', true, '', 0, false, 'M', 'M');
        $this->Cell(self::COL_WIDTH_TYPE, 8, 'النوع', 1, false, 'C', true, '', 0, false, 'M', 'M');
        $this->Cell(self::COL_WIDTH_DESCRIPTION, 8, 'الوصف', 1, false, 'C', true, '', 0, false, 'M', 'M');
        $this->Cell(self::COL_WIDTH_AMOUNT, 8, 'المبلغ', 1, false, 'C', true, '', 0, false, 'M', 'M');
        $this->Cell(self::COL_WIDTH_BALANCE, 8, 'الرصيد بعد المعاملة', 1, false, 'C', true, '', 0, false, 'M', 'M');
        $this->Cell(self::COL_WIDTH_REFERENCE, 8, 'رقم المرجع', 1, false, 'C', true, '', 0, false, 'M', 'M');
        $this->Ln();
    }

    private function renderLedgerTableRows(): void
    {
        $this->SetFont($this->fontFamily, '', 8);
        foreach ($this->ledgerEntries as $entry) {
            $dateText = isset($entry->transaction_date) ? date($this->dateFormat, strtotime($entry->transaction_date)) : '-';
            $this->Cell(self::COL_WIDTH_DATE, 8, $dateText, 1, false, 'C', false, '', 0, false, 'M', 'M');

            $typeText = $this->getTransactionTypeText($entry->transaction_type ?? '');
            $this->Cell(self::COL_WIDTH_TYPE, 8, $typeText, 1, false, 'C', false, '', 0, false, 'M', 'M');

            $description = $entry->description ?: 'غير محدد';
            $this->Cell(self::COL_WIDTH_DESCRIPTION, 8, $description, 1, false, 'R', false, '', 0, false, 'M', 'M');

            $amountNumeric = $entry->amount ?? 0;
            $amountSigned = ($entry->transaction_type === 'fee' ? '+' : '-') . number_format((float)$amountNumeric, 2);
            $this->Cell(self::COL_WIDTH_AMOUNT, 8, $amountSigned, 1, false, 'C', false, '', 0, false, 'M', 'M');

            $balanceText = number_format((float)($entry->balance_after ?? 0), 2);
            $this->Cell(self::COL_WIDTH_BALANCE, 8, $balanceText, 1, false, 'C', false, '', 0, false, 'M', 'M');

            $reference = $entry->reference_number ?: '-';
            $this->Cell(self::COL_WIDTH_REFERENCE, 8, $reference, 1, false, 'C', false, '', 0, false, 'M', 'M');
            $this->Ln();
        }
    }

    private function writeKeyValueRow(string $label, string $value, int $labelWidth = 50, int $valueWidth = 40): void
    {
        $this->Cell($labelWidth, 8, $label, 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Cell($valueWidth, 8, $value, 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->Ln();
    }

    private function drawSeparator(int $spaceBefore = 8, int $spaceAfter = 8): void
    {
        if ($spaceBefore > 0) {
            $this->Ln($spaceBefore);
        }
        $currentY = $this->GetY();
        $this->Line($this->lMargin, $currentY, $this->w - $this->rMargin, $currentY);
        if ($spaceAfter > 0) {
            $this->Ln($spaceAfter);
        }
    }

    private function formatMoney($amount): string
    {
        return number_format((float)$amount, 2) . ' ' . $this->currency;
    }
}
