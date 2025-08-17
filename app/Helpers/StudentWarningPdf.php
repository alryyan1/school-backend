<?php

namespace App\Helpers;

use TCPDF;

class StudentWarningPdf extends TCPDF
{
    public string $titleText = 'إشعار تحذيري';

    public function Header()
    {
        $this->setRTL(true);
        // Place logos right and left if available
        $logoPath = public_path('logo.png');
        if (file_exists($logoPath)) {
            // Right logo
            $this->Image($logoPath, $this->getPageWidth() - 35, 12, 25, 0, '', '', 'T', false, 300, 'R');
            // Left logo (mirror)
            $this->Image($logoPath, 10, 12, 25, 0, '', '', 'T', false, 300, 'L');
        }

        $this->SetFont('dejavusans', '', 11);
        $this->Ln(4);
        // Multi-line header organization text (centered)
        $this->Cell(0, 6, 'ولاية نهر النيل – محلية شندي', 0, 1, 'C');
        $this->Cell(0, 6, 'وزارة التربية و التوجيه — الإدارة العامة للتعليم الخاص', 0, 1, 'C');
        $this->Cell(0, 6, 'رياض ومدارس الفَان التعليمية الخاصة (بنين & بنات)', 0, 1, 'C');

        // Title (red)
        $this->SetTextColor(200, 0, 0);
        $this->SetFont('dejavusans', 'B', 16);
        $this->Cell(0, 10, $this->titleText, 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);
        $this->Ln(2);
    }

    public function Footer()
    {
        $this->SetY(-18);
        $this->SetFont('dejavusans', '', 9);
        $this->Cell(0, 8, '—', 0, 0, 'C');
    }
}


