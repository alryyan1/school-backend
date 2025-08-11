<?php

namespace App\Helpers;

use TCPDF;
use Carbon\Carbon;
use TCPDF_FONTS;

class TermsConditionsPdf extends TCPDF
{
    // Use a custom property name to avoid conflicting with TCPDF::$title (untyped in parent)
    public $titleText = 'لوائح القبول والضوابط التربوية';

    // Header
    public function Header(): void
    {
        // Ensure Arabic font is available
        $fontPath = public_path('\\fonts') . '\\arial.ttf';
        TCPDF_FONTS::addTTFfont($fontPath);
        $font = 'arial';

        $this->SetFont($font, 'B', 14);
        $this->Cell(0, 10, $this->titleText, 0, 1, 'C');
        $this->Ln(2);
        // Divider
        $this->Line($this->GetX(), $this->GetY(), $this->getPageWidth() - $this->getMargins()['right'], $this->GetY());
        $this->Ln(3);
    }

    // Footer
    public function Footer(): void
    {
        $this->SetY(-15);
        $fontPath = public_path('\\fonts') . '\\arial.ttf';
        TCPDF_FONTS::addTTFfont($fontPath);
        $font = 'arial';
        $this->SetFont($font, '', 9);
        $this->Cell(0, 10, 'تاريخ الطباعة: ' . Carbon::now()->format('Y/m/d H:i'), 0, 0, 'L');
        $this->Cell(0, 10, 'صفحة ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'R');
    }

    /**
     * Write the terms and conditions content (Arabic, RTL).
     */
    public function addTermsBody(): void
    {
        $this->setRTL(true);
        $fontPath = public_path('\\fonts') . '\\arial.ttf';
        TCPDF_FONTS::addTTFfont($fontPath);
        $font = 'arial';
        $this->SetFont($font, '', 12);

        $content = <<<TXT
1) التقيد بالأداب الدينية والتمسك بها داخل وخارج المدرسة.

2) الالتزام بالسلوك القويم مع الإدارة والمعلمين والموظفين والعمال والزملاء.

3) الالتزام بالزي المدرسي طيلة العام الدراسي ويجب ان لا يكون ضيقا ولاق قصيرا ويطرد من المدرسة في حالة عدم الالتزام بعد الاتصال على ولي الامر.

4) الظهور بمظهر شاذ وغير لائق كحلاقة راس شاذة وغير مرتبة ومهذبة واطالة الاظافر ولبس السلاسل والخاتم والذهب وغير ذلك يطرد من المدرسة في حالة عدم الالتزام بعد الاتصال على ولي الامر.

5) الالتزام بالخروج من الفصل اثناء الحصص الا بعد نهاية اليوم الدراسي او خلال الفسحة.

6) عدم حمل وتبادل اشرطة الكاست والاسطوانات والذواكر والفلاشات في المدرسة.

7) الالتزام بالحضور للمدرسة بالوقت المحدد وعد الخروج منها الا بعد نهاية الدوام.

8) الالتزام بعدم حمل المقتنيات الثمينة والالكترونية خاصة الموبايلات والتي ستصادر وتسلم الى نهاية العام الدراسي.

9) الالتزام باحضار جميع الأدوات المدرسية وان لا يستخدم الدفتر او الكراس الا لمادة واحدة.

10) الالتزام بالواجبات المدرسية لكل مادة وعرضها وتسليمها لمعلم المادة لتصحيحها وستكون هناك متابعة إدارية من مشرف الصف والإدارة.

11) الالزام بالجلوس في الفصل وفي المكان التي تحدده إدارة المدرسة.

12) اذا كان هناك طالب له ظرف خاص او حالة مرضية دائمة يجب توضيع ذلك بتقرير طبي يسلم للوحدة. الصحية بالمدرسة وفي حالة غياب الطالب بسبب حالة مرضية يجب الاتصال بإدارة المدرسة واعلامها بذلك.

13) كل طالب في الصف الثالث الثانوي يجب ان يحضر كل المواد الاجبارية.

14) الامتحانات الموحدة من الولاية او المحلية او امتحانات الشهادة يتحمل الطالب رسومها وذلك على حسب ما تحدده الولاية او المحلية وهي خارج الرسوم المدرسية.

15) على جميع الطلاب المحافظة على مقتنيات واثاث المدرسة فهي ملك للجميع وعدم الكتابة على الحوائط والادراج وكل من يخالف ذلك يعرض نفسه لعقوبة الفصل.

16) في حال عدم الالتزام بما جاء أعلاه من تعليمات يحق للمدرسة بفصل الطالب ولا يسترج رسومه.

17) لا ترد الرسوم باي حال من الأحوال حيث يترتب عليها حجز مقعد دراسي او مقعد ترحيل.
TXT;

        // Write content with proper wrapping
        $this->MultiCell(0, 8, $content, 0, 'R', false, 1, null, null, true, 0, false, true, 0, 'T', false);

        $this->Ln(8);
        $this->SetFont($font, '', 12);
        $this->Cell(0, 8, 'توقيع ولي الأمر: ______________________    التاريخ: ____/____/______', 0, 1, 'R');
    }
}


