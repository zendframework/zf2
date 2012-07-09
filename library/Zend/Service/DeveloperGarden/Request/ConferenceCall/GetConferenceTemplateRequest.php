<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @author     Marco Kaiser
 */
class Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceTemplateRequest
    extends Zend_Service_DeveloperGarden_Request_AbstractRequest
{
    /**
     * the template id
     *
     * @var string
     */
    public $templateId = null;

    /**
     * constructor
     *
     * @param integer $environment
     * @param string $templateId
     */
    public function __construct($environment, $templateId)
    {
        parent::__construct($environment);
        $this->setTemplateId($templateId);
    }

    /**
     * set the template id
     *
     * @param string $templateId
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceTemplateRequest
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
        return $this;
    }
}
