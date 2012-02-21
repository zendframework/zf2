<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Pdf
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

// set include_path to library/ directory only -- see ticket #11
set_include_path( dirname(dirname(dirname(__DIR__)))
                  . DIRECTORY_SEPARATOR . 'library' );

require_once 'Zend/Pdf.php';
require_once 'Zend/Pdf/Style.php';
require_once 'Zend/Pdf/Color/Cmyk.php';
require_once 'Zend/Pdf/Color/Html.php';
require_once 'Zend/Pdf/Color/GrayScale.php';
require_once 'Zend/Pdf/Color/Rgb.php';
require_once 'Zend/Pdf/Page.php';
require_once 'Zend/Pdf/Font.php';


if (!isset($argv[1])) {
    echo "USAGE: php demo.php <pdf_file> [<output_pdf_file>]\n";
    exit;
}

try {
    $pdf = Zend_Pdf::load($argv[1]);
} catch (Zend_Pdf_Exception $e) {
    if ($e->getMessage() == 'Can not open \'' . $argv[1] . '\' file for reading.') {
        // Create new PDF if file doesn't exist
        $pdf = new Zend_Pdf();

        if (!isset($argv[2])) {
            // force complete file rewriting (instead of updating)
            $argv[2] = $argv[1];
        }
    } else {
        // Throw an exception if it's not the "Can't open file" exception
        throw $e;
    }
}

//------------------------------------------------------------------------------------
// Reverse page order
$pdf->pages = array_reverse($pdf->pages);

// Create new Style
$style = new Zend_Pdf_Style();
$style->setFillColor(new Zend_Pdf_Color_Rgb(0, 0, 0.9));
$style->setLineColor(new Zend_Pdf_Color_GrayScale(0.2));
$style->setLineWidth(3);
$style->setLineDashingPattern(array(3, 2, 3, 4), 1.6);
$style->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD), 32);

try {
    // Create new image object
    require_once 'Zend/Pdf/Image.php';
    $stampImage = Zend_Pdf_Image::imageWithPath(__DIR__ . '/stamp.jpg');
} catch (Zend_Pdf_Exception $e) {
    // Example of operating with image loading exceptions.
    if ($e->getMessage() != 'Image extension is not installed.' &&
        $e->getMessage() != 'JPG support is not configured properly.') {
        throw $e;
    }
    $stampImage = null;
}

// Mark page as modified
foreach ($pdf->pages as $page){
    $page->saveGS()
         ->setAlpha(0.25)
         ->setStyle($style)
         ->rotate(0, 0, M_PI_2/3);

    $page->saveGS();
    $page->clipCircle(550, -10, 50);
    if ($stampImage != null) {
        $page->drawImage($stampImage, 500, -60, 600, 40);
    }
    $page->restoreGS();

    $page->drawText('Modified by Zend Framework!', 150, 0)
         ->restoreGS();
}

// Add new page generated by Zend_Pdf object (page is attached to the specified the document)
$pdf->pages[] = ($page1 = $pdf->newPage('A4'));

// Add new page generated by Zend_Pdf_Page object (page is not attached to the document)
$pdf->pages[] = ($page2 = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_LETTER_LANDSCAPE));

// Create new font
$font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);

// Apply font and draw text
$page1->setFont($font, 36)
      ->setFillColor(Zend_Pdf_Color_Html::color('#9999cc'))
      ->drawText('Helvetica 36 text string', 60, 500);

// Use font object for another page
$page2->setFont($font, 24)
      ->drawText('Helvetica 24 text string', 60, 500);

// Use another font
$page2->setFont(Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_TIMES), 32)
      ->drawText('Times-Roman 32 text string', 60, 450);

// Draw rectangle
$page2->setFillColor(new Zend_Pdf_Color_GrayScale(0.8))
      ->setLineColor(new Zend_Pdf_Color_GrayScale(0.2))
      ->setLineDashingPattern(array(3, 2, 3, 4), 1.6)
      ->drawRectangle(60, 400, 400, 350);

// Draw circle
$page2->setLineDashingPattern(Zend_Pdf_Page::LINE_DASHING_SOLID)
      ->setFillColor(new Zend_Pdf_Color_Rgb(1, 0, 0))
      ->drawCircle(85, 375, 25);

// Draw sectors
$page2->drawCircle(200, 375, 25, 2*M_PI/3, -M_PI/6)
      ->setFillColor(new Zend_Pdf_Color_Cmyk(1, 0, 0, 0))
      ->drawCircle(200, 375, 25, M_PI/6, 2*M_PI/3)
      ->setFillColor(new Zend_Pdf_Color_Rgb(1, 1, 0))
      ->drawCircle(200, 375, 25, -M_PI/6, M_PI/6);

// Draw ellipse
$page2->setFillColor(new Zend_Pdf_Color_Rgb(1, 0, 0))
      ->drawEllipse(250, 400, 400, 350)
      ->setFillColor(new Zend_Pdf_Color_Cmyk(1, 0, 0, 0))
      ->drawEllipse(250, 400, 400, 350, M_PI/6, 2*M_PI/3)
      ->setFillColor(new Zend_Pdf_Color_Rgb(1, 1, 0))
      ->drawEllipse(250, 400, 400, 350, -M_PI/6, M_PI/6);

// Draw and fill polygon
$page2->setFillColor(new Zend_Pdf_Color_Rgb(1, 0, 1));
$x = array();
$y = array();
for ($count = 0; $count < 8; $count++) {
    $x[] = 140 + 25*cos(3*M_PI_4*$count);
    $y[] = 375 + 25*sin(3*M_PI_4*$count);
}
$page2->drawPolygon($x, $y,
                    Zend_Pdf_Page::SHAPE_DRAW_FILL_AND_STROKE,
                    Zend_Pdf_Page::FILL_METHOD_EVEN_ODD);

// ---------- Draw figures in modified coordination system -----------------------------------

// Coordination system movement
$page2->saveGS();
$page2->translate(60, 250); // Shift coordination system

// Draw rectangle
$page2->setFillColor(new Zend_Pdf_Color_GrayScale(0.8))
      ->setLineColor(new Zend_Pdf_Color_GrayScale(0.2))
      ->setLineDashingPattern(array(3, 2, 3, 4), 1.6)
      ->drawRectangle(0, 50, 340, 0);

// Draw circle
$page2->setLineDashingPattern(Zend_Pdf_Page::LINE_DASHING_SOLID)
      ->setFillColor(new Zend_Pdf_Color_Rgb(1, 0, 0))
      ->drawCircle(25, 25, 25);

// Draw sectors
$page2->drawCircle(140, 25, 25, 2*M_PI/3, -M_PI/6)
      ->setFillColor(new Zend_Pdf_Color_Cmyk(1, 0, 0, 0))
      ->drawCircle(140, 25, 25, M_PI/6, 2*M_PI/3)
      ->setFillColor(new Zend_Pdf_Color_Rgb(1, 1, 0))
      ->drawCircle(140, 25, 25, -M_PI/6, M_PI/6);

// Draw ellipse
$page2->setFillColor(new Zend_Pdf_Color_Rgb(1, 0, 0))
      ->drawEllipse(190, 50, 340, 0)
      ->setFillColor(new Zend_Pdf_Color_Cmyk(1, 0, 0, 0))
      ->drawEllipse(190, 50, 340, 0, M_PI/6, 2*M_PI/3)
      ->setFillColor(new Zend_Pdf_Color_Rgb(1, 1, 0))
      ->drawEllipse(190, 50, 340, 0, -M_PI/6, M_PI/6);

// Draw and fill polygon
$page2->setFillColor(new Zend_Pdf_Color_Rgb(1, 0, 1));
$x = array();
$y = array();
for ($count = 0; $count < 8; $count++) {
    $x[] = 80 + 25*cos(3*M_PI_4*$count);
    $y[] = 25 + 25*sin(3*M_PI_4*$count);
}
$page2->drawPolygon($x, $y,
                    Zend_Pdf_Page::SHAPE_DRAW_FILL_AND_STROKE,
                    Zend_Pdf_Page::FILL_METHOD_EVEN_ODD);

// Draw line
$page2->setLineWidth(0.5)
      ->drawLine(0, 25, 340, 25);

$page2->restoreGS();


// Coordination system movement, skewing and scaling
$page2->saveGS();
$page2->translate(60, 150)     // Shift coordination system
      ->skew(0, 0, 0, -M_PI/9) // Skew coordination system
      ->scale(0.9, 0.9);       // Scale coordination system

// Draw rectangle
$page2->setFillColor(new Zend_Pdf_Color_GrayScale(0.8))
      ->setLineColor(new Zend_Pdf_Color_GrayScale(0.2))
      ->setLineDashingPattern(array(3, 2, 3, 4), 1.6)
      ->drawRectangle(0, 50, 340, 0);

// Draw circle
$page2->setLineDashingPattern(Zend_Pdf_Page::LINE_DASHING_SOLID)
      ->setFillColor(new Zend_Pdf_Color_Rgb(1, 0, 0))
      ->drawCircle(25, 25, 25);

// Draw sectors
$page2->drawCircle(140, 25, 25, 2*M_PI/3, -M_PI/6)
      ->setFillColor(new Zend_Pdf_Color_Cmyk(1, 0, 0, 0))
      ->drawCircle(140, 25, 25, M_PI/6, 2*M_PI/3)
      ->setFillColor(new Zend_Pdf_Color_Rgb(1, 1, 0))
      ->drawCircle(140, 25, 25, -M_PI/6, M_PI/6);

// Draw ellipse
$page2->setFillColor(new Zend_Pdf_Color_Rgb(1, 0, 0))
      ->drawEllipse(190, 50, 340, 0)
      ->setFillColor(new Zend_Pdf_Color_Cmyk(1, 0, 0, 0))
      ->drawEllipse(190, 50, 340, 0, M_PI/6, 2*M_PI/3)
      ->setFillColor(new Zend_Pdf_Color_Rgb(1, 1, 0))
      ->drawEllipse(190, 50, 340, 0, -M_PI/6, M_PI/6);

// Draw and fill polygon
$page2->setFillColor(new Zend_Pdf_Color_Rgb(1, 0, 1));
$x = array();
$y = array();
for ($count = 0; $count < 8; $count++) {
    $x[] = 80 + 25*cos(3*M_PI_4*$count);
    $y[] = 25 + 25*sin(3*M_PI_4*$count);
}
$page2->drawPolygon($x, $y,
                    Zend_Pdf_Page::SHAPE_DRAW_FILL_AND_STROKE,
                    Zend_Pdf_Page::FILL_METHOD_EVEN_ODD);

// Draw line
$page2->setLineWidth(0.5)
      ->drawLine(0, 25, 340, 25);

$page2->restoreGS();

//------------------------------------------------------------------------------------

if (isset($argv[2])) {
    $pdf->save($argv[2]);
} else {
    $pdf->save($argv[1], true /* update */);
}
