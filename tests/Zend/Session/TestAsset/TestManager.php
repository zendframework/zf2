<?php
namespace ZendTest\Session\TestAsset;

use Zend\Session\AbstractManager,
    Zend\Session\Configuration as SessionConfiguration,
    Zend\Session\Storage as SessionStorage,
    Zend\Session\SaveHandler as SessionSaveHandler,
    Zend\EventManager\EventCollection;

class TestManager extends AbstractManager
{
    public $started = false;

    protected $_configDefaultClass = 'Zend\\Session\\Configuration\\StandardConfiguration';
    protected $_storageDefaultClass = 'Zend\\Session\\Storage\\ArrayStorage';

    public function start()
    {
        $this->started = true;
    }

    public function destroy()
    {
        $this->started = false;
    }

    public function stop()
    {}

    public function writeClose()
    {
        $this->started = false;
    }

    public function getName()
    {}

    public function setName($name)
    {}

    public function getId()
    {}

    public function setId($id)
    {}

    public function regenerateId()
    {}

    public function rememberMe($ttl = null)
    {}

    public function forgetMe()
    {}


    public function setValidatorChain(EventCollection $chain)
    {}

    public function getValidatorChain()
    {}

    public function isValid()
    {}


    public function sessionExists()
    {}

    public function expireSessionCookie()
    {}


    public function setConfig(SessionConfiguration $config)
    {
        $this->_setConfig($config);
    }

    public function setStorage(SessionStorage $storage)
    {
        $this->_setStorage($storage);
    }

    public function setSaveHandler(SessionSaveHandler $saveHandler)
    {
        $this->_setSaveHandler($saveHandler);
    }
}
