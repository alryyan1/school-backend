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
    
    // Configuration - Casual simple styling
    private const DEFAULT_CURRENCY = 'جنيه';
    private const DEFAULT_DATE_FORMAT = 'd/m/Y';
    private const DEFAULT_HEADER_TITLE_AR = 'دفتر حسابات الطالب';
    private const DEFAULT_HEADER_TITLE_EN = 'Student Ledger Report';
    private const DEFAULT_TITLE_FONT_SIZE = 14;
    private const DEFAULT_SECTION_TITLE_FONT_SIZE = 11;
    private const DEFAULT_BASE_FONT_SIZE = 10;
    private const DEFAULT_STUDENT_NAME_FONT_SIZE = 12;
    private const DEFAULT_STUDENT_INFO_FONT_SIZE = 10;
    private const DEFAULT_HEADER_FONT_SIZE = 10;
    private const DEFAULT_FOOTER_FONT_SIZE = 8;
    private const DEFAULT_MARGIN_LEFT = 15;
    private const DEFAULT_MARGIN_TOP = 20;
    private const DEFAULT_MARGIN_RIGHT = 15;
    private const DEFAULT_HEADER_MARGIN = 15;
    private const DEFAULT_FOOTER_MARGIN = 10;
    private const DEFAULT_AUTO_PAGE_BREAK_MARGIN = 15;
    private const DEFAULT_IS_RTL = true;
    
    // Simple color scheme - casual gray tones
    private const COLOR_TEXT = [0, 0, 0];           // Black
    private const COLOR_TEXT_LIGHT = [100, 100, 100]; // Gray
    private const COLOR_BORDER = [200, 200, 200];   // Light gray
    private const COLOR_TABLE_HEADER = [240, 240, 240]; // Very light gray
    
    // Column widths for table (adjusted for payment method column)
    private const COL_WIDTH_DATE = 22;
    private const COL_WIDTH_TYPE = 20;
    private const COL_WIDTH_DESCRIPTION = 45;
    private const COL_WIDTH_AMOUNT = 25;
    private const COL_WIDTH_PAYMENT = 20;
    private const COL_WIDTH_BALANCE = 30;
    private const COL_WIDTH_REFERENCE = 22;
    
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
        // Simple header - just title (appears on every page)
        $this->SetFont($this->fontFamily, 'B', self::DEFAULT_TITLE_FONT_SIZE);
        $this->SetTextColor(...self::COLOR_TEXT);
        $this->SetY(10);
        $this->Cell(0, 8, self::DEFAULT_HEADER_TITLE_AR, 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }
    
    public function Footer()
    {
        $this->SetY(-12);
        $this->SetFont($this->fontFamily, '', self::DEFAULT_FOOTER_FONT_SIZE);
        $this->SetTextColor(...self::COLOR_TEXT_LIGHT);
        $this->Cell(0, 8, 'صفحة ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
    
    public function generateLedger()
    {
        // Add a page
        $this->AddPage();
        
        // Render student info only on first page (not in header)
        $this->renderHeaderStudentInfo();
        $this->Ln(5);
        
        $this->renderSummarySection();
        $this->Ln(5);
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
    
    private function getPaymentMethodText($method)
    {
        switch ($method) {
            case 'cash':
                return 'نقداً';
            case 'bankak':
                return 'بنكك';
            case 'Fawri':
                return 'فوري';
            case 'OCash':
                return 'أوكاش';
            default:
                return '-';
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
        // Simple plain text student info
        $this->SetFont($this->fontFamily, 'B', self::DEFAULT_STUDENT_NAME_FONT_SIZE);
        $this->SetTextColor(...self::COLOR_TEXT);
        $studentName = $this->enrollment->student->student_name ?? 'غير محدد';
        $this->Cell(0, 7, 'اسم الطالب: ' . $studentName, 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln(4);
        
        $this->SetFont($this->fontFamily, '', self::DEFAULT_STUDENT_INFO_FONT_SIZE);
        $this->SetTextColor(...self::COLOR_TEXT);
        
        $registrationId = $this->enrollment->id ?? '-';
        $this->Cell(0, 6, 'رقم التسجيل: ' . $registrationId, 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln();
        
        if ($this->enrollment->school && $this->enrollment->school->name) {
            $this->Cell(0, 6, 'المدرسة: ' . $this->enrollment->school->name, 0, false, 'R', 0, '', 0, false, 'M', 'M');
            $this->Ln();
        }
        
        if ($this->enrollment->gradeLevel && $this->enrollment->gradeLevel->name) {
            $this->Cell(0, 6, 'المرحلة: ' . $this->enrollment->gradeLevel->name, 0, false, 'R', 0, '', 0, false, 'M', 'M');
            $this->Ln();
        }
        
        if ($this->enrollment->classroom && $this->enrollment->classroom->name) {
            $this->Cell(0, 6, 'الفصل: ' . $this->enrollment->classroom->name, 0, false, 'R', 0, '', 0, false, 'M', 'M');
            $this->Ln();
        }
        
        $this->SetFont($this->fontFamily, '', 9);
        $this->SetTextColor(...self::COLOR_TEXT_LIGHT);
        $this->Cell(0, 6, 'تاريخ التقرير: ' . date($this->dateFormat), 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln(8);
    }

    private function renderSummarySection(): void
    {
        // Simple text-based summary
        $this->SetFont($this->fontFamily, 'B', self::DEFAULT_SECTION_TITLE_FONT_SIZE);
        $this->SetTextColor(...self::COLOR_TEXT);
        $this->Cell(0, 8, 'ملخص الحساب', 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln(6);
        
        $this->SetFont($this->fontFamily, '', self::DEFAULT_BASE_FONT_SIZE);
        $this->SetTextColor(...self::COLOR_TEXT);
        
        // إجمالي الرسوم
        $this->Cell(30, 7, 'إجمالي الرسوم:', 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Cell(20, 7, number_format((float)($this->summary['total_fees'] ?? 0), 2), 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->Ln();
        
        // إجمالي المدفوع
        $this->Cell(30, 7, 'إجمالي المدفوع:', 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Cell(20, 7, number_format((float)($this->summary['total_payments'] ?? 0), 2), 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->Ln();
        
        // إجمالي الخصومات
        $this->Cell(30, 7, 'إجمالي الخصومات:', 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Cell(20, 7, number_format((float)($this->summary['total_discounts'] ?? 0), 2), 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->Ln();
        
        // الرصيد الحالي (bold)
        $this->SetFont($this->fontFamily, 'B', self::DEFAULT_BASE_FONT_SIZE);
        $this->Cell(30, 7, 'الرصيد الحالي:', 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Cell(20, 7, number_format((float)($this->currentBalance ?? 0), 2), 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->Ln(5);
    }

    private function renderLedgerTable(): void
    {
        $this->SetFont($this->fontFamily, 'B', self::DEFAULT_SECTION_TITLE_FONT_SIZE);
        $this->SetTextColor(...self::COLOR_TEXT);
        $this->Cell(0, 8, 'تفاصيل المعاملات', 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Ln(6);

        $this->renderLedgerTableHeader();
        $this->renderLedgerTableRows();
    }

    private function renderLedgerTableHeader(): void
    {
        $this->SetFont($this->fontFamily, 'B', 9);
        $this->SetFillColor(...self::COLOR_TABLE_HEADER);
        $this->SetTextColor(...self::COLOR_TEXT);
        $this->SetDrawColor(...self::COLOR_BORDER);
        $this->SetLineWidth(0.2);

        $this->Cell(self::COL_WIDTH_DATE, 8, 'التاريخ', 1, false, 'C', true, '', 0, false, 'M', 'M');
        $this->Cell(self::COL_WIDTH_TYPE, 8, 'النوع', 1, false, 'C', true, '', 0, false, 'M', 'M');
        $this->Cell(self::COL_WIDTH_DESCRIPTION, 8, 'الوصف', 1, false, 'C', true, '', 0, false, 'M', 'M');
        $this->Cell(self::COL_WIDTH_AMOUNT, 8, 'المبلغ', 1, false, 'C', true, '', 0, false, 'M', 'M');
        $this->Cell(self::COL_WIDTH_PAYMENT, 8, 'طريقة الدفع', 1, false, 'C', true, '', 0, false, 'M', 'M');
        $this->Cell(self::COL_WIDTH_BALANCE, 8, 'الرصيد', 1, false, 'C', true, '', 0, false, 'M', 'M');
        $this->Cell(self::COL_WIDTH_REFERENCE, 8, 'رقم المرجع', 1, false, 'C', true, '', 0, false, 'M', 'M');
        $this->Ln();
    }

    private function renderLedgerTableRows(): void
    {
        $this->SetFont($this->fontFamily, '', 9);
        $this->SetDrawColor(...self::COLOR_BORDER);
        $this->SetLineWidth(0.1);
        $this->SetTextColor(...self::COLOR_TEXT);
        
        foreach ($this->ledgerEntries as $entry) {
            $dateText = isset($entry->transaction_date) ? date($this->dateFormat, strtotime($entry->transaction_date)) : '-';
            $this->Cell(self::COL_WIDTH_DATE, 7, $dateText, 1, false, 'C', false, '', 0, false, 'M', 'M');

            $typeText = $this->getTransactionTypeText($entry->transaction_type ?? '');
            $this->Cell(self::COL_WIDTH_TYPE, 7, $typeText, 1, false, 'C', false, '', 0, false, 'M', 'M');

            $description = $entry->description ?: 'غير محدد';
            $this->Cell(self::COL_WIDTH_DESCRIPTION, 7, $description, 1, false, 'R', false, '', 0, false, 'M', 'M');

            $amountNumeric = $entry->amount ?? 0;
            $amountSigned = ($entry->transaction_type === 'fee' ? '+' : '-') . number_format((float)$amountNumeric, 2);
            $this->Cell(self::COL_WIDTH_AMOUNT, 7, $amountSigned, 1, false, 'C', false, '', 0, false, 'M', 'M');

            $paymentMethod = $this->getPaymentMethodText($entry->payment_method ?? null);
            $this->Cell(self::COL_WIDTH_PAYMENT, 7, $paymentMethod, 1, false, 'C', false, '', 0, false, 'M', 'M');

            $balanceText = number_format((float)($entry->balance_after ?? 0), 2);
            $this->Cell(self::COL_WIDTH_BALANCE, 7, $balanceText, 1, false, 'C', false, '', 0, false, 'M', 'M');

            $reference = $entry->reference_number ?: '-';
            $this->Cell(self::COL_WIDTH_REFERENCE, 7, $reference, 1, false, 'C', false, '', 0, false, 'M', 'M');
            $this->Ln();
        }
    }

    private function writeKeyValueRow(string $label, string $value, int $labelWidth = 50, int $valueWidth = 40, bool $isBold = false): void
    {
        $this->SetFont($this->fontFamily, $isBold ? 'B' : '', self::DEFAULT_BASE_FONT_SIZE);
        $this->Cell($labelWidth, 7, $label, 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Cell($valueWidth, 7, $value, 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->Ln();
    }

    private function formatMoney($amount): string
    {
        return number_format((float)$amount, 2) . ' ' . $this->currency;
    }
}
