<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-webat this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Session\SaveHandler;

use Zend\Config\Config as Configuration,
    Zend\Session\SaveHandler as Savable,
    Zend\Session\Exception\InvalidArgumentException,
    Zend\Db\TableGateway\TableGateway as BaseTableGateway,
    Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet,
    Zend\Db\Sql\Where;

/**
 * TableGateway session save handler
 *
 * @category   Zend
 * @package    Zend_Session
 * @subpackage SaveHandler
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TableGateway extends BaseTableGateway implements Savable
{

    /**
     * Sessions table `data`
     *
     * @var string $dataColumn
     */
    protected $dataColumn = 'data';

    /**
     * Session lifetime
     *
     * @var integer $lifetime
     */
    protected $lifetime = 0;

    /**
     * Sessions table primary key
     *
     * @var string $primary
     */
    protected $primary = 'sessions_id';

    /**
     * The row of the session in the database
     *
     * @var Zend\Db\ResultSet\Row $row
     */
    protected $row;

    /**
     * The rowset of the session in the database
     *
     * @var Zend\Db\ResultSet\ResultSet $rowset
     */
    protected $rowset;

    /**
     * Session name
     *
     * @var string $sessionName
     */
    protected $sessionName = '';

    /**
     * Session save path
     *
     * @var string $sessionSavePath
     */
    protected $sessionSavePath = '';

    /**
     * Sessions table name
     *
     * @var string $tableName
     */
    protected $tableName = 'sessions';

    /**
     * Sessions table last modification `timestamp`
     *
     * @var string $timestampColumn
     */
    protected $timestampColumn = 'timestamp';

    ############################################################################
    #
    # Class constructor
    #
    ############################################################################

    /**
     * Constructor
     *
     * $configuration is an instance of Zend\Config\Config as Configuration. You
     * may use the longhand or shorthand notation.
     *
     * These are the configuration options for Zend\Session\SaveHandler\TableGateway:
     *
     * tableName         => (string) Session the table name.
     *
     * primary           => (string) Session table primary key. At this time a only a single key is supported.
     *
     * timestampColumn   => (string) Session table timestamp column. This is the last time the session was accessed by the client.
     *
     * dataColumn        => (string) Session table data column. This holds the php serialized session. @see serialize()
     *
     * lifetime          => (integer) Session lifetime (optional; default: ini_get('session.gc_maxlifetime'))
     *
     * Unrecognized configuration options will throw an exception.
     *
     * This is a functional sessions table CREATE statement that uses the
     * default settings for this save handler:
     *
     * <code>
     *  CREATE TABLE IF NOT EXISTS `sessions` (
     *    `sessions_id` binary(32) NOT NULL,
     *    `timestamp` int(10) NOT NULL,
     *    `data` longtext NOT NULL,
     *    PRIMARY KEY (`sessions_id`)
     *  );
     * </code>
     *
     * @param  Adapter            $adapter The database adapter
     * @param  Configuration      OPTIONAL User provided configuration
     *
     * @uses setTableName()
     * @uses setPrimary()
     * @uses setTimestampColumn()
     * @uses setDataColumn()
     * @uses setLifetime()
     * @uses setup()
     *
     * @throws \Zend\Session\Exception\InvalidArgumentException
     */
    public function __construct(Adapter $adapter, Configuration $configuration = null)
    {
        if (!is_null($configuration)) {

            // Loop through the options
            foreach ($configuration as $key => $value) {
                do {
                    switch ($key) {
                        case 'tableName':
                            $this->setTableName($value);
                            break;
                        case 'primary':
                            $this->setPrimary($value);
                            break;
                        case 'timestampColumn':
                            $this->setTimestampColumn($value);
                            break;
                        case 'dataColumn':
                            $this->setDataColumn($value);
                            break;
                        case 'lifetime':
                            $this->setLifetime($value);
                            break;
                        default:
                            $message = 'Invalid argument passed in Configuration: [' . $key . '] => [' . $value . ']';
                            throw new \Zend\Session\Exception\InvalidArgumentException($message);
                            break 2;
                    }
                } while (false);
            }
        }

        // At this time, no other options are allowed to be passed to the adapter.
        parent::__construct($this->getTableName(), $adapter);

        $this->setup();
    }

    /**
     * Setup required settings
     *
     * @uses getLifetime()
     * @uses resetLifetime()
     *
     * @return Zend\Session\SaveHandler\TableGateway
     */
    public function setup()
    {
        $lifetime = $this->getLifetime();

        if (empty($lifetime)) {
            $this->resetLifetime();
        }

        return $this;
    }

    ############################################################################
    #
    # Class helper methods
    #
    ############################################################################

    /**
     * Get table data column
     *
     * @see $dataColumn
     *
     * @return string
     */
    public function getDataColumn()
    {
        return $this->dataColumn;
    }

    /**
     * Set table data column
     *
     * @see $dataColumn
     *
     * @param string $dataColumn
     * @return Zend\Session\SaveHandler\TableGateway
     * @throws \Zend\Session\Exception\InvalidArgumentException
     */
    protected function setDataColumn($dataColumn)
    {
        $this->dataColumn = (string) $dataColumn;

        if ($this->dataColumn == '') {
            $message = 'The data column cannot be empty.';
            throw new \Zend\Session\Exception\InvalidArgumentException($message);
        }

        return $this;
    }

    /**
     * Get session lifetime
     *
     * @see $lifetime
     *
     * @return integer
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * Reset session lifetime.
     *
     * This will return lifetime to the default:
     * (integer) ini_get('session.gc_maxlifetime')
     *
     * @see $lifetime
     *
     * @uses setLifetime()
     *
     * @return Zend\Session\SaveHandler\TableGateway
     */
    public function resetLifetime()
    {
        $this->setLifetime();

        return $this;
    }

    /**
     * Set session lifetime.
     *
     * If $lifetime is empty, it defaults to session.gc_maxlifetime
     *
     * @see $lifetime
     *
     * @param integer $lifetime
     * @return Zend\Session\SaveHandler\TableGateway
     * @throws \Zend\Session\Exception\InvalidArgumentException
     */
    public function setLifetime($lifetime = 0)
    {
        settype($lifetime, 'integer');

        if ($lifetime == 0) {
            $this->lifetime = (integer) ini_get('session.gc_maxlifetime');
        } elseif ($lifetime < 0) {
            $message = 'Lifetime must be greater than 0.';
            throw new \Zend\Session\Exception\InvalidArgumentException($message);
        } else {
            $this->lifetime = $lifetime;
        }

        return $this;
    }

    /**
     * Get the primary key of the session table.
     *
     * @see $primary
     *
     * @return string
     */
    public function getPrimary()
    {
        return $this->primary;
    }

    /**
     * Set the primary key of the session table.
     *
     * Only a single key is allowed.
     *
     * @see $primary
     *
     * @param string $primary
     * @return Zend\Session\SaveHandler\TableGateway
     * @throws \Zend\Session\Exception\InvalidArgumentException
     */
    protected function setPrimary($primary)
    {
        if (!is_string($primary)) {
            $message = 'The primary key must be a string. Multiple keys are not supported at this time.';
            throw new \Zend\Session\Exception\InvalidArgumentException($message);
        }

        $this->primary = (string) $primary;

        if ($this->primary == '') {
            $message = 'The primary key cannot be empty.';
            throw new \Zend\Session\Exception\InvalidArgumentException($message);
        }

        return $this;
    }

    /**
     * Set name of the session table.
     *
     * The table name must be set on class instantiation.
     *
     * @see $tableName
     *
     * @param string $tableName
     * @return Zend\Session\SaveHandler\TableGateway
     * @throws \Zend\Session\Exception\InvalidArgumentException
     */
    protected function setTableName($tableName)
    {
        $this->tableName = (string) $tableName;

        if ($this->tableName == '') {
            $message = 'The table name cannot be empty.';
            throw new \Zend\Session\Exception\InvalidArgumentException($message);
        }

        return $this;
    }

    /**
     * Get timestamp column of the session table.
     *
     * @see $timestampColumn
     *
     * @return string
     */
    public function getTimestampColumn()
    {
        return $this->timestampColumn;
    }

    /**
     * Set timestamp column of the session table.
     *
     * @see $timestampColumn
     *
     * @param string $timestampColumn The timestamp column of the session table.
     * @return Zend\Session\SaveHandler\TableGateway
     * @throws \Zend\Session\Exception\InvalidArgumentException
     */
    protected function setTimestampColumn($timestampColumn)
    {
        $this->timestampColumn = (string) $timestampColumn;

        if ($this->timestampColumn == '') {
            $message = 'The timestamp column cannot be empty.';
            throw new \Zend\Session\Exception\InvalidArgumentException($message);
        }

        return $this;
    }

    /**
     * Check to see if the session is expired
     *
     * @see $row
     *
     * @uses getLifetime()
     * @uses getTimestampColumn()
     *
     * @return boolean Returns true if the session is expired.
     */
    public function isSessionExpired()
    {
        $timestamp = 0;

        if (isset($this->row) && isset($this->row->{$this->getTimestampColumn()})) {
            $timestamp = (integer) $this->row->{$this->getTimestampColumn()};
        }

        $timestampPlusLifetime = $timestamp + $this->getLifetime();

        // Check to see if $timestampPlusLifetime is less than the current time.
        return $timestampPlusLifetime < time();
    }

    /**
     * Get session name
     *
     * @see $sessionName
     *
     * @return string
     */
    public function getSessionName()
    {
        return $this->sessionName;
    }

    /**
     * Get the session row from the database and set it in @see $this->row
     *
     * @see $rowset
     * @see $row
     *
     * @uses getPrimary()
     * @uses select()
     *
     * @param string $id The session id
     * @return Zend\Session\SaveHandler\TableGateway
     */
    public function setSessionRow($id)
    {
        // Zend\Db\ResultSet\ResultSet
        $this->rowset = $this->select(array($this->getPrimary() => $id));

        // Zend\Db\ResultSet\Row
        $this->row = $this->rowset->current();

        return $this;
    }

    /**
     * Get session save path
     *
     * @see $sessionSavePath
     *
     * @return string
     */
    public function getSessionSavePath()
    {
        return $this->sessionSavePath;
    }

    ############################################################################
    #
    # Session methods
    #
    ############################################################################

    /**
     * Close session
     *
     * @return boolean
     */
    public function close()
    {
        return true;
    }

    /**
     * Destroy session
     *
     * @see \Zend\Db\Sql\Where
     *
     * @uses delete()
     * @uses getPrimary()
     *
     * @param string $id
     * @return boolean
     */
    public function destroy($id)
    {
        $where = new \Zend\Db\Sql\Where();
        $where->equalTo($this->getPrimary(), $id);

        if ($this->delete($where)) {
            return true;
        }

        return false;
    }

    /**
     * Garbage Collection
     *
     * @see \Zend\Db\Sql\Where
     *
     * @uses delete()
     * @uses getLifetime()
     * @uses getTimestampColumn()
     *
     * @todo Is it okay for this method to return false?
     *
     * @param integer $maxlifetime This parameter is unused.
     * @return boolean
     */
    public function gc($maxlifetime = null)
    {
        $where = new \Zend\Db\Sql\Where();

        /*
         * See if $timestamp is less than the current time minus the
         * lifetime.
         */
        $where->lessThanOrEqualTo($this->getTimestampColumn(), time() - $this->getLifetime());

        if ($this->delete($where)) {
            return true;
        }

        return false;
    }

    /**
     * Open Session
     *
     * @see $sessionSavePath
     * @see $sessionName
     *
     * @param string $sessionSavePath The session save path
     * @param string $sessionName     The session name
     * @return boolean
     */
    public function open($sessionSavePath, $sessionName)
    {
        $this->sessionSavePath = $sessionSavePath;
        $this->sessionName     = $sessionName;

        return true;
    }

    /**
     * Read session data
     *
     * Return an empty string if the session was not found.
     * Return an empty string if the session was destroyed.
     *
     * @see $row
     *
     * @uses destroy()
     * @uses getDataColumn()
     * @uses isSessionExpired()
     * @uses setSessionRow()
     *
     * @param string $id The session id
     * @return string
     */
    public function read($id)
    {

        $this->setSessionRow($id);

        if (!empty($this->row)) {

            // If session has not expired
            if ($this->isSessionExpired()) {

                $this->destroy($id);
            } else {

                return $this->row->{$this->getDataColumn()};

            }
        }

        return '';
    }

    /**
     * Write session data
     *
     * @see $row
     *
     * @uses insert()
     * @uses getDataColumn()
     * @uses getPrimary()
     * @uses getTimestampColumn()
     * @uses update()
     *
     * @param string $id   The session id
     * @param string $data The session data
     * @return boolean
     */
    public function write($id, $data)
    {
        $return = false;

        if (!empty($this->row)) {

            $update = array(
                $this->getTimestampColumn() => time(),
                $this->getDataColumn()      => $data,
            );

            // Update the current session in the sessions table
            if ($this->update($update, array($this->getPrimary() => $id))) {
                $return = true;
            }

        } else {

            $insert = array(
                $this->getTimestampColumn() => time(),
                $this->getPrimary()    => $id,
                $this->getDataColumn()      => (string) $data,
            );

            // Insert a new session in the sessions table
            if ($this->insert($insert)) {
                $return = true;
            }
        }

        return $return;
    }
}
