<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\TableGateway\Feature;

use Zend\Db\TableGateway\AbstractTableGateway;

/**
 * Class FeatureSet
 */
class FeatureSet
{
    /**
     * @const string
     */
    const APPLY_HALT = 'halt';

    /**
     * @var AbstractTableGateway
     */
    protected $tableGateway = null;

    /**
     * @var AbstractFeature[]
     */
    protected $features = array();

    /**
     * @var array
     */
    protected $magicSpecifications = array();

    /**
     * @param array $features
     */
    public function __construct(array $features = array())
    {
        if ($features) {
            $this->addFeatures($features);
        }
    }

    /**
     * @param AbstractTableGateway $tableGateway
     */
    public function setTableGateway(AbstractTableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        foreach ($this->features as $feature) {
            $feature->setTableGateway($this->tableGateway);
        }
        return $this;
    }

    /**
     * @param string $featureClassName
     */
    public function getFeatureByClassName($featureClassName)
    {
        $feature = false;
        foreach ($this->features as $potentialFeature) {
            if ($potentialFeature instanceof $featureClassName) {
                $feature = $potentialFeature;
                break;
            }
        }
        return $feature;
    }

    /**
     * @param array $features
     */
    public function addFeatures(array $features)
    {
        foreach ($features as $feature) {
            $this->addFeature($feature);
        }
        return $this;
    }

    /**
     * @param AbstractFeature $feature
     */
    public function addFeature(AbstractFeature $feature)
    {
        $this->features[] = $feature;
        $feature->setTableGateway($feature);
        return $this;
    }

    /**
     * @param string $method
     * @param array $args
     */
    public function apply($method, $args)
    {
        foreach ($this->features as $feature) {
            if (method_exists($feature, $method)) {
                $return = call_user_func_array(array($feature, $method), $args);
                if ($return === self::APPLY_HALT) {
                    break;
                }
            }
        }
    }

    /**
     * @param string $property
     * @return bool
     */
    public function canCallMagicGet($property)
    {
        return false;
    }

    /**
     * @param string $property
     * @return mixed
     */
    public function callMagicGet($property)
    {
        $return = null;
        return $return;
    }

    /**
     * @param string $property
     * @return bool
     */
    public function canCallMagicSet($property)
    {
        return false;
    }

    /**
     * @param $property
     * @param $value
     * @return mixed
     */
    public function callMagicSet($property, $value)
    {
        $return = null;
        return $return;
    }

    /**
     * @param string $method
     * @return bool
     */
    public function canCallMagicCall($method)
    {
        foreach ($this->features as $feature) {
            if (method_exists($feature, $method)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return null|mixed
     */
    public function callMagicCall($method, $arguments)
    {
        foreach ($this->features as $feature) {
            if (method_exists($feature, $method)) {
                return call_user_func_array(array($feature, $method), $arguments);
            }
        }
        return null;
    }
}
