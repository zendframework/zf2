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
 * @package    Zend_Mvc_Router
 * @subpackage Http
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Mvc\Router\Cli;

use Traversable,
    Zend\Stdlib\IteratorToArray,
    Zend\Stdlib\RequestDescription as Request,
    Zend\Mvc\Router\Exception,
    Zend\Cli\Request as CliRequest,
    Zend\Filter\FilterChain,
    Zend\Validator\ValidatorChain,
    Zend\Mvc\Exception\InvalidArgumentException
    ;

/**
 * Segment route.
 *
 * @package    Zend_Mvc_Router
 * @subpackage Http
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Simple implements Route
{
    const VALUE_NUMBER  = 'n';
    const VALUE_STRING  = 's';

    /**
     * Parts of the route.
     *
     * @var array
     */
    protected $parts;

    /**
     * Default values.
     *
     * @var array
     */
    protected $defaults;

    /**
     * Parameters' name aliases.
     *
     * @var array
     */
    protected $aliases;

    /**
     * List of assembled parameters.
     *
     * @var array
     */
    protected $assembledParams = array();

    /**
     * @var \Zend\Validator\ValidatorChain
     */
    protected $validators;

    /**
     * @var \Zend\Filter\FilterChain
     */
    protected $filters;

    /**
     * Create a new Simple CLI route.
     *
     * @param  string                                   $route
     * @param  array                                    $constraints
     * @param  array                                    $defaults
     * @param  array                                    $aliases
     * @param  null|array|Traversable|FilterChain       $filters
     * @param  null|array|Traversable|ValidatorChain    $validators
     * @return \Zend\Mvc\Router\Cli\Simple
     */
    public function __construct(
        $route,
        array $constraints = array(),
        array $defaults = array(),
        array $aliases = array(),
        $filters = null,
        $validators = null
    ){
        $this->defaults = $defaults;
        $this->constraints = $constraints;
        $this->aliases = $aliases;

        if($filters !== null){
            if($filters instanceof FilterChain){
                $this->filters = $filters;
            }elseif($filters instanceof Traversable){
                $this->filters = new FilterChain(array(
                    'filters' => IteratorToArray::convert($filters, false) 
                ));
            }elseif(is_array($filters)){
                $this->filters = new FilterChain(array(
                    'filters' => $filters 
                ));
            }else{
                throw new InvalidArgumentException('Cannot use '.gettype($filters).' as filters for '.__CLASS__);
            }
        }
        
        if($validators !== null){
            if($validators instanceof ValidatorChain){
                $this->validators = $validators;
            }elseif($validators instanceof Traversable || is_array($validators)){
                $this->validators = new ValidatorChain();
                foreach($validators as $v){
                    $this->validators->addValidator($v);
                }
            }else{
                throw new InvalidArgumentException('Cannot use '.gettype($validators).' as validators for '.__CLASS__);
            }
        }
        
        $this->parts = $this->parseRouteDefinition($route);
    }

    /**
     * factory(): defined by Route interface.
     *
     * @see    Route::factory()
     * @param  array|Traversable $options
     * @return Simple
     */
    public static function factory($options = array())
    {
        if ($options instanceof Traversable) {
            $options = IteratorToArray::convert($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
        }

        if (!isset($options['route'])) {
            throw new Exception\InvalidArgumentException('Missing "route" in options array');
        }

        foreach(array(
            'constraints',
            'defaults',
            'aliases',
        ) as $opt){
            if (!isset($options[$opt])) {
                $options[$opt] = array();
            }
        }

        if (!isset($options['validators'])) {
            $options['validators'] = null;
        }

        if (!isset($options['filters'])) {
            $options['filters'] = null;
        }


        return new static(
            $options['route'],
            $options['constraints'],
            $options['defaults'],
            $options['aliases'],
            $options['filters'],
            $options['validators']
        );
    }

    /**
     * Parse a route definition.
     *
     * @param  string $def
     * @return array
     */
    protected function parseRouteDefinition($def)
    {
        $def = trim($def);
        $pos        = 0;
        $length     = strlen($def);
        $parts      = array();

        while ($pos < $length) {
            /**
             * Mandatory long param
             *    --param
             *    --param=n
             *    --param=s
             */
            if (preg_match( '/\G--(?<name>[a-zA-Z0-9]+)(?:=(?<type>[ns]))?(?: +|$)/s', $def, $m, 0, $pos )) {
                $item = array(
                    'name'       => strtolower( $m['name'] ),
                    'short'      => false,
                    'literal'    => false,
                    'required'   => true,
                    'positional' => false,
                    'valueType'  => !empty($m['type']) ? $m['type'] : null,
                );
            }
            /**
             * Optional long param
             *    [--param]
             *    [--param=n]
             *    [--param=s]
             */
            elseif (preg_match(
                '/\G\[ *?--(?<name>[a-zA-Z0-9]+)(?:=(?<type>[ns]))? *?\](?: +|$)/s', $def, $m, 0, $pos
            )) {
                $item = array(
                    'name'       => strtolower( $m['name'] ),
                    'short'      => false,
                    'literal'    => false,
                    'required'   => false,
                    'positional' => false,
                    'valueType'  => !empty($m['type']) ? $m['type'] : null,
                );
            }
            /**
             * Mandatory short param
             *    -a
             *    -a=i
             *    -a=s
             *    -a=w
             */
            elseif (preg_match( '/\G-(?<name>[a-zA-Z0-9])(?:=(?<type>[ns]))?(?: +|$)/s', $def, $m, 0, $pos )) {
                $item = array(
                    'name'       => strtolower( $m['name'] ),
                    'short'      => true,
                    'literal'    => false,
                    'required'   => true,
                    'positional' => false,
                    'valueType'  => !empty($m['type']) ? $m['type'] : null,
                );
            }
            /**
             * Optional short param
             *    [-a]
             *    [-a=n]
             *    [-a=s]
             */
            elseif (preg_match('/\G\[ *?-(?<name>[a-zA-Z0-9])(?:=(?<type>[ns]))? *?\](?: +|$)/s', $def, $m, 0, $pos)) {
                $item = array(
                    'name'       => strtolower( $m['name'] ),
                    'short'      => true,
                    'literal'    => false,
                    'required'   => false,
                    'positional' => false,
                    'valueType'  => !empty($m['type']) ? $m['type'] : null,
                );
            }
            /**
             * Optional literal param alternative
             *    [something|somethingElse|anotherOne]
             *    [  something   |  somethingElse  |  anotherOne  ]
             */
            elseif (preg_match( '/
                \G
                \[
                    (?:
                        \ *?
                        (?<name>[a-z0-9][a-zA-Z0-9_]*?)
                        \ *?
                        (?:\||(?=\]))
                        \ *?
                    )+
                \]
                (?:\ +|$)
                /sx', $def, $m, 0, $pos
            )
            ) {
                $item = array(
                    'name'       => $m['name'],
                    'literal'    => true,
                    'required'   => false,
                    'positional' => true,
                    'alternative' => array(),
                );
            }
            /**
             * Optional literal param, i.e.
             *    [something]
             */
            elseif (preg_match( '/\G\[ *?(?<name>[a-z0-9][a-zA-Z0-9\_]*?) *?\](?: +|$)/s', $def, $m, 0, $pos )) {
                $item = array(
                    'name'       => $m['name'],
                    'literal'    => true,
                    'required'   => false,
                    'positional' => true,
                );
            }
            /**
             * Optional value param, i.e.
             *    [SOMETHING]
             */
            elseif (preg_match( '/\G\[(?<name>[A-Z0-9\_]+)\](?: +|$)/s', $def, $m, 0, $pos )) {
                $item = array(
                    'name'       => strtolower( $m['name'] ),
                    'literal'    => false,
                    'required'   => false,
                    'positional' => true,
                );
            }
            /**
             * Mandatory value param, i.e.
             *    SOMETHING
             */
            elseif (preg_match( '/\G(?<name>[A-Z0-9\_]+)(?: +|$)/s', $def, $m, 0, $pos )) {
                $item = array(
                    'name'       => strtolower( $m['name'] ),
                    'literal'    => false,
                    'required'   => true,
                    'positional' => true,
                );
            }
            /**
             * Mandatory literal param, i.e.
             *   something
             */
            elseif (preg_match( '/\G(?<name>[a-z0-9][a-zA-Z0-9\_]*?)(?: +|$)/s', $def, $m, 0, $pos )) {
                $item = array(
                    'name'       => $m['name'],
                    'literal'    => true,
                    'required'   => true,
                    'positional' => true,
                );
            }
            else {
                throw new Exception\InvalidArgumentException(
                    'Cannot understand CLI route at '.
                    ($pos > 0 ? '...' : '').
                    '"' . substr( $def, $pos ) . '"'
                );
            }

            $pos += strlen( $m[0] );
            $parts[] = $item;
        }

//        print_r($parts);
        return $parts;
    }

    /**
     * match(): defined by Route interface.
     *
     * @see     Route::match()
     * @param   Request             $request
     * @param   null|int            $pathOffset
     * @return  RouteMatch
     */
    public function match(Request $request, $pathOffset = null)
    {
        if (!$request instanceof CliRequest) {
            return null;
        }

        /** @var $request CliRequest */
        /** @var $params \Zend\Stdlib\ParametersDescription */
        $params = $request->getParams()->toArray();
        $matches = array();

        /**
         * Extract positional and named parts
         */
        $positional = $named = array();
        foreach($this->parts as &$part){
            if($part['positional'])
                $positional[] = &$part;
            else
                $named[] = &$part;
        }

        /**
         * Scan for named parts inside Cli params
         */
        foreach($named as &$part){
            /**
             * Prepare match regex
             */
            if($part['short']){
                if(isset($part['valueType'])){
                    $regex = '/^\-'.$part['name'].'(?:\=(?<value>.*?)$)?$/';
                }else{
                    $regex = '/^\-'.$part['name'].'$/';
                }
            }else{
                if(isset($part['valueType'])){
                    $regex = '/^\-{2,}'.$part['name'].'(?:\=(?<value>.*?)$)?$/';
                }else{
                    $regex = '/^\-{2,}'.$part['name'].'$/';
                }
            }

            /**
             * Look for param
             */
            $value = $param = null;
            for($x=0;$x<count($params);$x++){
                if(preg_match($regex,$params[$x],$m)){
                    // found param
                    $param = $params[$x];

                    // prevent further scanning of this param
                    array_splice($params,$x,1);

                    if(isset($m['value'])){
                        $value = $m['value'];
                    }

                    break;
                }
            }

            /**
             * Drop out if that was a mandatory param
             */
            if(!$param && $part['required']){
                return false;
            }

            /**
             * Try to retrieve value if it is expected
             */
            if(!$value && isset($part['valueType'])){
                if($x < count($params)+1){
                    // retrieve value from adjacent param
                    $value = $params[$x];

                    // prevent further scanning of this param
                    array_splice($params,$x,1);
                }else{
                    // there are no more params available
                    return false;
                }
            }

            /**
             * Validate the value type
             */
            if(isset($part['valueType'])){
                if($part['valueType'] == self::VALUE_NUMBER && !is_numeric($value)){
                    // value type mismatch
                    return false;
                }

                if(
                    isset($this->constraints[$part['name']]) &&
                    !preg_match($this->constraints[$part['name']],$value)
                ){
                    // constraint failed
                    return false;
                }
            }

            /**
             * Store the value
             */
            if(isset($part['valueType'])){
                $matches[$part['name']] = $value;
            }else{
                $matches[$part['name']] = true;
            }
        }


//        /**
//         * Go through all positional parts
//         */
//        $pos = 0;
//        for($x = 0;$x<count($positional);$x++){
//            $part = $positional[$x];
//
//            if($params->offsetExists($pos)){
//                // found value at exact position
//                $val = $params->get($pos);
//
//                /**
//                 * Check constraints
//                 */
//                if (
//                    isset($this->constraints[$part['name']]) &&
//                    !preg_match($this->constraints[$part['name']],$val)
//                ) {
//                    return null;
//                }
//
//                /**
//                 * Save matched value
//                 */
//
//            }else{
//                /**
//                 * We reached end of available positional params
//                 */
//
//            }
//
//        }

        return new RouteMatch(array_merge($this->defaults, $matches));
    }

    /**
     * assemble(): Defined by Route interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = array(), array $options = array())
    {
        $this->assembledParams = array();


    }

    /**
     * getAssembledParams(): defined by Route interface.
     *
     * @see    Route::getAssembledParams
     * @return array
     */
    public function getAssembledParams()
    {
        return $this->assembledParams;
    }
}
