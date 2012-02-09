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
 * @package    Zend\Service\WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace Zend\Service\WindowsAzure\Log\Formatter;

use Zend\Log\Formatter as FormatterInterface,
        Zend\Service\WindowsAzure\Storage\DynamicTableEntity;

/**
 * @category   Zend
 * @package    Zend\Service\WindowsAzure
 * @subpackage Log
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class WindowsAzure implements FormatterInterface
{
	/**
	 * Write a message to the table storage
	 *
	 * @param  array $event
	 * @return Zend\Service\WindowsAzure\Storage\DynamicTableEntity
	 */
	public function format($event)
	{
		// partition key is the current date, represented as YYYYMMDD
		// row key will always be the current timestamp. These values MUST be hardcoded.
		$logEntity = new DynamicTableEntity(date('Ymd'), microtime(true));
		// Windows Azure automatically adds the timestamp, but the timezone is most of the time
		// different compared to the origin server's timezone, so add this timestamp aswell.
		$event['server_timestamp'] = $event['timestamp'];
		unset($event['timestamp']);

		foreach ($event as $key => $value) {
			 if ((is_object($value) && !method_exists($value,'__toString'))
                || is_array($value)) {

                $value = gettype($value);
            }

			$logEntity->{$key} = $value;
		}

		return $logEntity;
	}
}