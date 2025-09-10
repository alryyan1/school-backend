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
            // $this->Image($logoPath, $this->getPageWidth() - 30, 5, 55, 45, '', '', 'T', false, 300, 'R');
            // Left logo (mirror)
            $this->Image($logoPath, 60, 5, 60, 45,);
        }

        $this->SetFont('arial', '', 11);
        $this->Ln(4);
        // Multi-line header organization text (centered)
        $this->Cell(0, 6, 'ولاية نهر النيل – محلية شندي', 0, 1, 'C');
        $this->Cell(0, 6, 'وزارة التربية و التعليم — الإدارة العامة للتعليم الخاص', 0, 1, 'C');
        $this->Cell(0, 6, 'رياض ومدارس الفنار التعليمية الخاصة (بنين & بنات)', 0, 1, 'C');

        // Title (red)
        $this->SetTextColor(200, 0, 0);
        $this->SetFont('arial', 'B', 16);
        $this->Cell(0, 10, $this->titleText, 0, 1, 'C');
        $this->SetTextColor(0, 0, 0);
        $this->Ln(2);
    }

    public function Footer()
    {
        $this->SetY(-18);
        $this->SetFont('arial', '', 9);
        $this->Cell(0, 8, '—', 0, 0, 'C');
    }
}


