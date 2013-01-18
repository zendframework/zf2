<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\RowGateway\Feature;

use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\Adapter\Driver\StatementInterface;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Update;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventsCapableInterface;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage RowGateway
 */
class EventFeature extends AbstractFeature implements EventsCapableInterface
{
    /**
     * @var EventManagerInterface
     */
    protected $eventManager = null;

    /**
     * @var null
     */
    protected $event = null;

    /**
     * @param EventManagerInterface        $eventManager
     * @param EventFeature\RowGatewayEvent $rowGatewayEvent
     */
    public function __construct(
        EventManagerInterface $eventManager = null,
        EventFeature\RowGatewayEvent $rowGatewayEvent = null
    ) {
        $this->eventManager = ($eventManager instanceof EventManagerInterface)
                            ? $eventManager
                            : new EventManager;

        $this->eventManager->setIdentifiers(array(
            'Zend\Db\RowGateway\RowGateway',
        ));

        $this->event = ($rowGatewayEvent) ?: new EventFeature\RowGatewayEvent();
    }

    /**
     * Retrieve composed event manager instance
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Retrieve composed event instance
     *
     * @return EventFeature\RowGatewayEvent
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Initialize feature and trigger "preInitialize" event
     *
     * Ensures that the composed RowGateway has identifiers based on the
     * class name, and that the event target is set to the RowGateway
     * instance. It then triggers the "preInitialize" event.
     *
     * @return void
     */
    public function preInitialize()
    {
        if (get_class($this->rowGateway) != 'Zend\Db\RowGateway\RowGateway') {
            $this->eventManager->addIdentifiers(get_class($this->rowGateway));
        }

        $this->event->setTarget($this->rowGateway);
        $this->event->setName(__FUNCTION__);
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "postInitialize" event
     *
     * @return void
     */
    public function postInitialize()
    {
        $this->event->setName(__FUNCTION__);
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "preInsert" event
     *
     * Triggers the "preInsert" event mapping the following parameters:
     * - $insert as "insert"
     *
     * @param  Insert $insert
     * @return void
     */
    public function preInsert(Insert $insert)
    {
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array('insert' => $insert));
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "postInsert" event
     *
     * Triggers the "postInsert" event mapping the following parameters:
     * - $statement as "statement"
     * - $result as "result"
     *
     * @param  StatementInterface $statement
     * @param  ResultInterface    $result
     * @return void
     */
    public function postInsert(StatementInterface $statement, ResultInterface $result)
    {
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array(
            'statement' => $statement,
            'result' => $result,
        ));
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "preUpdate" event
     *
     * Triggers the "preUpdate" event mapping the following parameters:
     * - $update as "update"
     *
     * @param  Update $update
     * @return void
     */
    public function preUpdate(Update $update)
    {
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array('update' => $update));
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "postUpdate" event
     *
     * Triggers the "postUpdate" event mapping the following parameters:
     * - $statement as "statement"
     * - $result as "result"
     *
     * @param  StatementInterface $statement
     * @param  ResultInterface    $result
     * @return void
     */
    public function postUpdate(StatementInterface $statement, ResultInterface $result)
    {
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array(
            'statement' => $statement,
            'result' => $result,
        ));
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "preDelete" event
     *
     * Triggers the "preDelete" event mapping the following parameters:
     * - $delete as "delete"
     *
     * @param  Delete $delete
     * @return void
     */
    public function preDelete(Delete $delete)
    {
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array('delete' => $delete));
        $this->eventManager->trigger($this->event);
    }

    /**
     * Trigger the "postDelete" event
     *
     * Triggers the "postDelete" event mapping the following parameters:
     * - $statement as "statement"
     * - $result as "result"
     *
     * @param  StatementInterface $statement
     * @param  ResultInterface    $result
     * @return void
     */
    public function postDelete(StatementInterface $statement, ResultInterface $result)
    {
        $this->event->setName(__FUNCTION__);
        $this->event->setParams(array(
            'statement' => $statement,
            'result' => $result,
        ));
        $this->eventManager->trigger($this->event);
    }
}
