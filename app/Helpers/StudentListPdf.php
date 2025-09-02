<?php // app/Helpers/StudentListPdf.php (create this file/directory if needed)

namespace App\Helpers; // Adjust namespace if needed

use TCPDF;
use Carbon\Carbon;

class StudentListPdf extends TCPDF {

    public $reportTitle = 'قائمة الطلاب';
    public $filterInfo = ''; // Optional: To display applied filters

    // Page header
    public function Header() {
        // Draw logo from public/logo.png if available
        $logoPath = function_exists('public_path') ? public_path('logo.png') : null;
        if ($logoPath && @file_exists($logoPath)) {
            // x, y, width (auto height), type auto-detected
            // Place near top-right because document is RTL; adjust margins if needed
            $this->Image($logoPath, 60, 5, 60,60,);
        }

        $this->SetFont('arial', 'B', 12); // Use a font supporting Arabic
        $this->Cell(0, 9, $this->reportTitle, 0, true, 'C', 0, '', 0, false, 'M', 'M');
        if (!empty($this->filterInfo)) {
             $this->SetFont('arial', '', 9);
             $this->Cell(0, 6, $this->filterInfo, 0, true, 'C', 0, '', 0, false, 'M', 'M');
        }
        // Draw a line under the header
        // $this->Line($this->GetX(), $this->GetY(), $this->getPageWidth() - $this->getMargins()['right'], $this->GetY());
        $this->Ln(10);
    }

    // Page footer
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('arial', 'I', 8);
        $this->Cell(0, 10, 'تاريخ الطباعة: ' . Carbon::now()->format('Y/m/d H:i'), 0, false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 10, 'صفحة '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}