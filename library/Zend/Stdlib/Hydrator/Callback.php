<?php
namespace Zend\Stdlib\Hydrator;

class Callback extends AbstractHydrator
{
    /**
     * @var callable
     */
    protected $extractorCallback;

    /**
     * @var callable
     */
    protected $hydratorCallback;

    /**
     * @param callable $hydratorCallback
     * @param callable $extractorCallback
     */
    public function __construct(callable $hydratorCallback, callable $extractorCallback)
    {
        parent::__construct();

        $this->extractorCallback = $extractorCallback;
        $this->hydratorCallback  = $hydratorCallback;
    }

    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object)
    {
        $array = call_user_func($this->hydratorCallback, $object);

        return $array;
    }

    /**
     * @param array  $data
     * @param object $object
     * @return Object
     */
    public function hydrate(array $data, $object)
    {
        $object = call_user_func($this->hydratorCallback, $data, $object);

        return $object;
    }
}
