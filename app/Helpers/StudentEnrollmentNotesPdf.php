<?php

namespace App\Helpers;

use TCPDF;
use Carbon\Carbon;

class StudentEnrollmentNotesPdf extends TCPDF
{
    public string $titleText = 'ملاحظات تسجيل الطالب';
    public string $studentName = '';
    public string $enrollmentInfo = '';

    public function Header()
    {
        $this->setRTL(true);
        
        // Place logo if available
        $logoPath = public_path('logo.png');
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 65, 5, 65, 55);
        }

        $this->SetFont('arial', '', 11);
        $this->Ln(4);
        
        // Organization header
        $this->Cell(0, 6, 'ولاية نهر النيل – محلية شندي', 0, 1, 'C');
        $this->Cell(0, 6, 'وزارة التربية و التعليم — الإدارة العامة للتعليم الخاص', 0, 1, 'C');
        $this->Cell(0, 6, 'رياض ومدارس الفنار التعليمية الخاصة (بنين & بنات)', 0, 1, 'C');

        // Title
        $this->SetTextColor(0, 0, 139);
        $this->SetFont('arial', 'B', 16);
        $this->Cell(0, 10, $this->titleText, 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);
        
        // Student info
        if (!empty($this->studentName)) {
            $this->SetFont('arial', 'B', 12);
            $this->Cell(0, 8, 'اسم الطالب: ' . $this->studentName, 0, 1, 'C');
        }
        
        if (!empty($this->enrollmentInfo)) {
            $this->SetFont('arial', '', 10);
            $this->Cell(0, 6, $this->enrollmentInfo, 0, 1, 'C');
        }
        
        $this->Ln(5);
    }

    public function Footer()
    {
        $this->SetY(-18);
        $this->SetFont('arial', '', 9);
        $this->Cell(0, 8, 'تاريخ الطباعة: ' . Carbon::now()->format('Y/m/d H:i'), 0, 0, 'L');
        $this->Cell(0, 8, 'صفحة ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'R');
    }

    public function generateNotesTable($notes)
    {
        // Ensure we're positioned after the header
        // $this->SetY($this->GetY() + 5);
        $this->setTopMargin(56);
        $this->Ln(5);
        
        $this->SetFont('arial', 'B', 10);
        
        // Table header
        $this->SetFillColor(240, 240, 240);
        $this->Cell(100, 8, 'الملاحظة', 1, 0, 'C', true);
        $this->Cell(40, 8, 'المستخدم', 1, 0, 'C', true);
        $this->Cell(40, 8, 'التاريخ', 1, 1, 'C', true);
        
        $this->SetFont('arial', '', 9);
        
        if (empty($notes)) {
            $this->Cell(180, 8, 'لا توجد ملاحظات', 1, 1, 'C');
            return;
        }
        
        foreach ($notes as $note) {
            // Note content (wrapped)
            $this->MultiCell(100, 8, $note['note'], 1, 'C', false, 0);
            
            // User name
            $this->Cell(40, 8, $note['user']['name'] ?? '-', 1, 0, 'C');
            
            // Date
            $date = $note['created_at'] ? Carbon::parse($note['created_at'])->format('Y/m/d') : '-';
            $this->Cell(40, 8, $date, 1, 1, 'C');
        }
    }
}
