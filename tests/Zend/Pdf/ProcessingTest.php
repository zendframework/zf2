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
 * @package    Zend_PDF
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Pdf;
use Zend\Pdf;
use Zend\Pdf\Color;

/** \Zend\Pdf\PdfDocument */

/** PHPUnit Test Case */

/**
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_PDF
 */
class ProcessingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Stores the original set timezone
     * @var string
     */
    private $_originaltimezone;

    public function setUp()
    {
        $this->_originaltimezone = date_default_timezone_get();
        date_default_timezone_set('GMT');
    }

    /**
     * Teardown environment
     */
    public function tearDown()
    {
        date_default_timezone_set($this->_originaltimezone);
    }

    public function testCreate()
    {
        $pdf = new Pdf\PdfDocument();

        // Add new page generated by \Zend\Pdf\PdfDocument object (page is attached to the specified the document)
        $pdf->pages[] = ($page1 = $pdf->newPage('A4'));

        // Add new page generated by \Zend\Pdf\Page object (page is not attached to the document)
        $pdf->pages[] = ($page2 = new Pdf\Page(Pdf\Page::SIZE_LETTER_LANDSCAPE));

        // Create new font
        $font = Pdf\Font::fontWithName(Pdf\Font::FONT_HELVETICA);

        // Apply font and draw text
        $page1->setFont($font, 36)
              ->setFillColor(Color\Html::color('#9999cc'))
              ->drawText('Helvetica 36 text string', 60, 500);

        // Use font object for another page
        $page2->setFont($font, 24)
              ->drawText('Helvetica 24 text string', 60, 500);

        // Use another font
        $page2->setFont(Pdf\Font::fontWithName(Pdf\Font::FONT_TIMES), 32)
              ->drawText('Times-Roman 32 text string', 60, 450);

        // Draw rectangle
        $page2->setFillColor(new Color\GrayScale(0.8))
              ->setLineColor(new Color\GrayScale(0.2))
              ->setLineDashingPattern(array(3, 2, 3, 4), 1.6)
              ->drawRectangle(60, 400, 500, 350);

        // Draw rounded rectangle
        $page2->setFillColor(new Color\GrayScale(0.9))
              ->setLineColor(new Color\GrayScale(0.5))
              ->setLineDashingPattern(Pdf\Page::LINE_DASHING_SOLID)
              ->drawRoundedRectangle(425, 350, 475, 400, 20);

        // Draw circle
        $page2->setLineDashingPattern(Pdf\Page::LINE_DASHING_SOLID)
              ->setFillColor(new Color\Rgb(1, 0, 0))
              ->drawCircle(85, 375, 25);

        // Draw sectors
        $page2->drawCircle(200, 375, 25, 2*M_PI/3, -M_PI/6)
              ->setFillColor(new Color\Cmyk(1, 0, 0, 0))
              ->drawCircle(200, 375, 25, M_PI/6, 2*M_PI/3)
              ->setFillColor(new Color\Rgb(1, 1, 0))
              ->drawCircle(200, 375, 25, -M_PI/6, M_PI/6);

        // Draw ellipse
        $page2->setFillColor(new Color\Rgb(1, 0, 0))
              ->drawEllipse(250, 400, 400, 350)
              ->setFillColor(new Color\Cmyk(1, 0, 0, 0))
              ->drawEllipse(250, 400, 400, 350, M_PI/6, 2*M_PI/3)
              ->setFillColor(new Color\Rgb(1, 1, 0))
              ->drawEllipse(250, 400, 400, 350, -M_PI/6, M_PI/6);

        // Draw and fill polygon
        $page2->setFillColor(new Color\Rgb(1, 0, 1));
        $x = array();
        $y = array();
        for ($count = 0; $count < 8; $count++) {
            $x[] = 140 + 25*cos(3*M_PI_4*$count);
            $y[] = 375 + 25*sin(3*M_PI_4*$count);
        }
        $page2->drawPolygon($x, $y,
                            Pdf\Page::SHAPE_DRAW_FILL_AND_STROKE,
                            Pdf\Page::FILL_METHOD_EVEN_ODD);

        // Draw line
        $page2->setLineWidth(0.5)
              ->drawLine(60, 375, 500, 375);

        $pdf->save(__DIR__ . '/_files/output.pdf');
        unset($pdf);

        $pdf1 = Pdf\PdfDocument::load(__DIR__ . '/_files/output.pdf');

        $this->assertTrue($pdf1 instanceof Pdf\PdfDocument);
        unset($pdf1);

        unlink(__DIR__ . '/_files/output.pdf');
    }

    public function testModify()
    {
        $pdf = Pdf\PdfDocument::load(__DIR__ . '/_files/pdfarchiving.pdf');

        // Reverse page order
        $pdf->pages = array_reverse($pdf->pages);

        // Mark page as modified
        foreach ($pdf->pages as $page){
            $page->saveGS();

            // Create new Style
            $page->setFillColor(new Color\Rgb(0, 0, 0.9))
                 ->setLineColor(new Color\GrayScale(0.2))
                 ->setLineWidth(3)
                 ->setLineDashingPattern(array(3, 2, 3, 4), 1.6)
                 ->setFont(Pdf\Font::fontWithName(Pdf\Font::FONT_HELVETICA_BOLD), 32);


            $page->rotate(0, 0, M_PI_2/3)
                 ->drawText('Modified by Zend Framework!', 150, 0)
                 ->restoreGS();
        }


        // Add new page generated by \Zend\Pdf\PdfDocument object (page is attached to the specified the document)
        $pdf->pages[] = ($page1 = $pdf->newPage('A4'));

        // Add new page generated by \Zend\Pdf\Page object (page is not attached to the document)
        $pdf->pages[] = ($page2 = new Pdf\Page(Pdf\Page::SIZE_LETTER_LANDSCAPE));

        // Create new font
        $font = Pdf\Font::fontWithName(Pdf\Font::FONT_HELVETICA);

        // Apply font and draw text
        $page1->setFont($font, 36)
              ->setFillColor(Color\Html::color('#9999cc'))
              ->drawText('Helvetica 36 text string', 60, 500);

        // Use font object for another page
        $page2->setFont($font, 24)
              ->drawText('Helvetica 24 text string', 60, 500);

        // Use another font
        $page2->setFont(Pdf\Font::fontWithName(Pdf\Font::FONT_TIMES), 32)
              ->drawText('Times-Roman 32 text string', 60, 450);

        // Draw rectangle
        $page2->setFillColor(new Color\GrayScale(0.8))
              ->setLineColor(new Color\GrayScale(0.2))
              ->setLineDashingPattern(array(3, 2, 3, 4), 1.6)
              ->drawRectangle(60, 400, 500, 350);

        // Draw rounded rectangle
        $page2->setFillColor(new Color\GrayScale(0.9))
              ->setLineColor(new Color\GrayScale(0.5))
              ->setLineDashingPattern(Pdf\Page::LINE_DASHING_SOLID)
              ->drawRoundedRectangle(425, 350, 475, 400, 20);

        // Draw circle
        $page2->setLineDashingPattern(Pdf\Page::LINE_DASHING_SOLID)
              ->setFillColor(new Color\Rgb(1, 0, 0))
              ->drawCircle(85, 375, 25);

        // Draw sectors
        $page2->drawCircle(200, 375, 25, 2*M_PI/3, -M_PI/6)
              ->setFillColor(new Color\Cmyk(1, 0, 0, 0))
              ->drawCircle(200, 375, 25, M_PI/6, 2*M_PI/3)
              ->setFillColor(new Color\Rgb(1, 1, 0))
              ->drawCircle(200, 375, 25, -M_PI/6, M_PI/6);

        // Draw ellipse
        $page2->setFillColor(new Color\Rgb(1, 0, 0))
              ->drawEllipse(250, 400, 400, 350)
              ->setFillColor(new Color\Cmyk(1, 0, 0, 0))
              ->drawEllipse(250, 400, 400, 350, M_PI/6, 2*M_PI/3)
              ->setFillColor(new Color\Rgb(1, 1, 0))
              ->drawEllipse(250, 400, 400, 350, -M_PI/6, M_PI/6);

        // Draw and fill polygon
        $page2->setFillColor(new Color\Rgb(1, 0, 1));
        $x = array();
        $y = array();
        for ($count = 0; $count < 8; $count++) {
            $x[] = 140 + 25*cos(3*M_PI_4*$count);
            $y[] = 375 + 25*sin(3*M_PI_4*$count);
        }
        $page2->drawPolygon($x, $y,
                            Pdf\Page::SHAPE_DRAW_FILL_AND_STROKE,
                            Pdf\Page::FILL_METHOD_EVEN_ODD);

        // Draw line
        $page2->setLineWidth(0.5)
              ->drawLine(60, 375, 500, 375);

        $pdf->save(__DIR__ . '/_files/output.pdf');

        unset($pdf);

        $pdf1 = Pdf\PdfDocument::load(__DIR__ . '/_files/output.pdf');

        $this->assertTrue($pdf1 instanceof Pdf\PdfDocument);
        unset($pdf1);

        unlink(__DIR__ . '/_files/output.pdf');
    }

    public function testInfoProcessing()
    {
        $pdf = Pdf\PdfDocument::load(__DIR__ . '/_files/pdfarchiving.pdf');

        $this->assertEquals($pdf->properties['Title'], 'PDF as a Standard for Archiving');
        $this->assertEquals($pdf->properties['Author'], 'Adobe Systems Incorporated');

        $metadata = $pdf->getMetadata();

        $metadataDOM = new \DOMDocument();
        $metadataDOM->loadXML($metadata);

        $xpath = new \DOMXPath($metadataDOM);
        $pdfPreffixNamespaceURI = $xpath->query('/rdf:RDF/rdf:Description')->item(0)->lookupNamespaceURI('pdf');
        $xpath->registerNamespace('pdf', $pdfPreffixNamespaceURI);

        $titleNodeset = $xpath->query('/rdf:RDF/rdf:Description/pdf:Title');
        $titleNode    = $titleNodeset->item(0);
        $this->assertEquals($titleNode->nodeValue, 'PDF as a Standard for Archiving');


        $pdf->properties['Title'] .= ' (modified)';
        $pdf->properties['New_Property'] = 'New property';

        $titleNode->nodeValue .= ' (modified using RDF data)';
        $pdf->setMetadata($metadataDOM->saveXML());

        $pdf->save(__DIR__ . '/_files/output.pdf');
        unset($pdf);


        $pdf1 = Pdf\PdfDocument::load(__DIR__ . '/_files/output.pdf');
        $this->assertEquals($pdf1->properties['Title'], 'PDF as a Standard for Archiving (modified)');
        $this->assertEquals($pdf1->properties['Author'], 'Adobe Systems Incorporated');
        $this->assertEquals($pdf1->properties['New_Property'], 'New property');

        $metadataDOM1 = new \DOMDocument();
        $metadataDOM1->loadXML($metadata);

        $xpath1 = new \DOMXPath($metadataDOM);
        $pdfPreffixNamespaceURI1 = $xpath1->query('/rdf:RDF/rdf:Description')->item(0)->lookupNamespaceURI('pdf');
        $xpath1->registerNamespace('pdf', $pdfPreffixNamespaceURI1);

        $titleNodeset1 = $xpath->query('/rdf:RDF/rdf:Description/pdf:Title');
        $titleNode1    = $titleNodeset->item(0);
        $this->assertEquals($titleNode1->nodeValue, 'PDF as a Standard for Archiving (modified using RDF data)');
        unset($pdf1);

        unlink(__DIR__ . '/_files/output.pdf');
    }

    public function testPageDuplicating()
    {
        $pdf = Pdf\PdfDocument::load(__DIR__ . '/_files/pdfarchiving.pdf');

        $srcPageCount = count($pdf->pages);

        $outputPageSet = array();
        foreach ($pdf->pages as $srcPage){
            $page = new Pdf\Page($srcPage);

            $outputPageSet[] = $srcPage;
            $outputPageSet[] = $page;

            $page->saveGS();

            // Create new Style
            $page->setFillColor(new Color\Rgb(0, 0, 0.9))
                 ->setLineColor(new Color\GrayScale(0.2))
                 ->setLineWidth(3)
                 ->setLineDashingPattern(array(3, 2, 3, 4), 1.6)
                 ->setFont(Pdf\Font::fontWithName(Pdf\Font::FONT_HELVETICA_BOLD), 32);


            $page->rotate(0, 0, M_PI_2/3);
            $page->drawText('Modified by Zend Framework!', 150, 0);
            $page->restoreGS();
        }


        // Add new page generated by Zend_PDF object (page is attached to the specified the document)
        $pdf->pages = $outputPageSet;

        $pdf->save(__DIR__ . '/_files/output.pdf');

        unset($pdf);

        $pdf1 = Pdf\PdfDocument::load(__DIR__ . '/_files/output.pdf');

        $this->assertTrue($pdf1 instanceof Pdf\PdfDocument);
        $this->assertEquals($srcPageCount*2, count($pdf1->pages));
        unset($pdf1);

        unlink(__DIR__ . '/_files/output.pdf');
    }

    public function testPageCloning()
    {
        $pdf  = Pdf\PdfDocument::load(__DIR__ . '/_files/pdfarchiving.pdf');
        $pdf1 = new Pdf\PdfDocument();

        $srcPageCount = count($pdf->pages);

        $outputPageSet = array();
        foreach ($pdf->pages as $srcPage){
            $page = clone $srcPage;

            $page->saveGS();

            // Create new Style
            $page->setFillColor(new Color\Rgb(0, 0, 0.9))
                 ->setLineColor(new Color\GrayScale(0.2))
                 ->setLineWidth(3)
                 ->setLineDashingPattern(array(3, 2, 3, 4), 1.6)
                 ->setFont(Pdf\Font::fontWithName(Pdf\Font::FONT_HELVETICA_BOLD), 32);


            $page->rotate(0, 0, M_PI_2/3);
            $page->drawText('Modified by Zend Framework!', 150, 0);
            $page->restoreGS();

            $pdf1->pages[] = $page;
        }

        $pdf1->save(__DIR__ . '/_files/output.pdf');

        unset($pdf);
        unset($pdf1);

        $pdf2 = Pdf\PdfDocument::load(__DIR__ . '/_files/output.pdf');

        $this->assertTrue($pdf2 instanceof Pdf\PdfDocument);
        $this->assertEquals($srcPageCount, count($pdf2->pages));
        unset($pdf2);

        unlink(__DIR__ . '/_files/output.pdf');
    }

    /**
     * @group ZF-3701
     */
    public function testZendPDFIsExtendableWithAccessToProperties()
    {
        $pdf = new ExtendedZendPDF();

        // Test accessing protected variables and their default content
        $this->assertEquals(array(), $pdf->_originalProperties);
        $this->assertEquals(array(), $pdf->_namedTargets);

        $pdfpage = new ExtendedZendPDFPage(Pdf\Page::SIZE_A4);
        // Test accessing protected variables and their default content
        $this->assertEquals(0, $pdfpage->_saveCount);
    }
}


class ExtendedZendPDF extends Pdf\PdfDocument
{
    public function __get($name) {
        if(isset($this->$name)) {
            return $this->$name;
        }
    }
}
class ExtendedZendPDFPage extends Pdf\Page
{
    public function __get($name) {
        if(isset($this->$name)) {
            return $this->$name;
        }
    }
}
