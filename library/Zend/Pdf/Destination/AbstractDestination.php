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
 * @subpackage Zend_PDF_Destination
 */

namespace Zend\Pdf\Destination;
use Zend\Pdf\Exception;
use Zend\Pdf\InternalType;
use Zend\Pdf;

/**
 * Abstract PDF destination representation class
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Destination
 */
abstract class AbstractDestination extends Pdf\InternalStructure\NavigationTarget
{
    /**
     * Load Destination object from a specified resource
     *
     * @internal
     * @param $destinationArray
     * @return \Zend\Pdf\Destination\AbstractDestination
     */
    public static function load(InternalType\AbstractTypeObject $resource)
    {
        if ($resource->getType() == InternalType\AbstractTypeObject::TYPE_NAME  ||  $resource->getType() == InternalType\AbstractTypeObject::TYPE_STRING) {
            return new Named($resource);
        }

        if ($resource->getType() != InternalType\AbstractTypeObject::TYPE_ARRAY) {
            throw new Exception\CorruptedPdfException('An explicit destination must be a direct or an indirect array object.');
        }
        if (count($resource->items) < 2) {
            throw new Exception\CorruptedPdfException('An explicit destination array must contain at least two elements.');
        }

        switch ($resource->items[1]->value) {
            case 'XYZ':
                return new Zoom($resource);
                break;

            case 'Fit':
                return new Fit($resource);
                break;

            case 'FitH':
                return new FitHorizontally($resource);
                break;

            case 'FitV':
                return new FitVertically($resource);
                break;

            case 'FitR':
                return new FitRectangle($resource);
                break;

            case 'FitB':
                return new FitBoundingBox($resource);
                break;

            case 'FitBH':
                return new FitBoundingBoxHorizontally($resource);
                break;

            case 'FitBV':
                return new FitBoundingBoxVertically($resource);
                break;

            default:
                return new Unknown($resource);
                break;
        }
    }
}
