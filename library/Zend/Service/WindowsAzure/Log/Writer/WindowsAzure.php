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

namespace Zend\Service\WindowsAzure\Log\Writer;

use Zend\Log\Writer\AbstractWriter;

class WindowsAzure extends AbstractWriter
{
	/**
	 * @var Zend\Service\Log\Formatter\Interface
	 */
	protected $_formatter;

	/**
	 * Connection to a windows Azure 
	 * 
	 * @var Zend\Service\Service\WindowsAzure\Storage\Table
	 */
	protected $_tableStorageConnection = null;

	/**
	 * Name of the table to use for logging purposes
	 *
	 * @var string
	 */
	protected $_tableName = null;

	/**
	 * Whether to keep all messages to be logged in an external buffer until the script ends and
	 * only then to send the messages in batch to the logging component.
	 *
	 * @var bool
	 */
	protected $_bufferMessages = false;

	/**
	 * If message buffering is activated, it will store all the messages in this buffer and only
	 * write them to table storage (in a batch transaction) when the script ends.
	 *
	 * @var array
	 */
	protected $_messageBuffer = array();

	/**
	 * @param Zend\Service\Service\WindowsAzure\Storage\Table $tableStorageConnection
	 * @param string $tableName
	 * @param bool   $createTable create the Windows Azure table for logging if it does not exist
	 */
	public function __construct(Zend\Service\WindowsAzure\Storage\Table $tableStorageConnection,
		$tableName, $createTable = true, $bufferMessages = true)
	{
		if ($tableStorageConnection == null) {
			throw new Zend\Log\Exception\InvalidArgumentException('No connection to the Windows Azure tables provided.');
		}

		if (!is_string($tableName)) {
			throw new Zend\Log\Exception\InvalidArgumentException('Provided Windows Azure table name must be a string.');
		}

		$this->_tableStorageConnection = $tableStorageConnection;
		$this->_tableName              = $tableName;

		// create the logging table if it does not exist. It will add some overhead, so it's optional
		if ($createTable) {
			$this->_tableStorageConnection->createTableIfNotExists($this->_tableName);
		}

		// keep messages to be logged in an internal buffer and only send them over the wire when
		// the script execution ends
		if ($bufferMessages) {
			$this->_bufferMessages = $bufferMessages;
		}

		$this->_formatter = new Zend\Service\WindowsAzure\Log\Formatter\WindowsAzure();
	}

	/**
	 * If the log messages have been stored in the internal buffer, just send them
	 * to table storage.
	 */
	public function shutdown() {
		parent::shutdown();
		if ($this->_bufferMessages) {
			$batch = $this->_tableStorageConnection->startBatch();
			foreach ($this->_messageBuffer as $logEntity) {
				$this->_tableStorageConnection->insertEntity($this->_tableName, $logEntity);
			}
			$batch->commit();
		}
	}

	/**
     * Create a new instance of Zend\Service\Log\Writer\WindowsAzure
     *
     * @param  array $config
     * @return Zend\Service\Log\Writer\WindowsAzure
     * @throws Zend\Service\Log\Exception
     */
    static public function factory($config)
    {
        $config = self::_parseConfig($config);
        $config = array_merge(array(
            'connection'  => null,
            'tableName'   => null,
            'createTable' => true,
        ), $config);

        return new self(
            $config['connection'],
            $config['tableName'],
            $config['createTable']
        );
    }

    /**
     * The only formatter accepted is already  loaded in the constructor
     *
     * @todo enable custom formatters using the WindowsAzure\Storage\DynamicTableEntity class
     */
    public function setFormatter (Zend\Log\Formatter $formatter)
    {
        throw new Zend\Log\Exception\RuntimeException(get_class($this) . ' does not support formatting');
    }

	/**
	 * Write a message to the table storage. If buffering is activated, then messages will just be
	 * added to an internal buffer.
	 *
	 * @param  array $event
	 * @return void
	 * @todo   format the event using a formatted, not in this method
	 */
	protected function _write($event)
	{
		$logEntity = $this->_formatter->format($event);

		if ($this->_bufferMessages) {
			$this->_messageBuffer[] = $logEntity;
		} else {
			$this->_tableStorageConnection->insertEntity($this->_tableName, $logEntity);
		}
	}
}