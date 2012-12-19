<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\Router\Http;

use Traversable;
use Zend\Mvc\Router\Http\RouteInterface;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Exception;
use Zend\Http\Request as HttpRequest;

/**
 * Cookie route.
 *
 * This route will check the request for the
 * presence of a cookie, it's absence, or a check
 * it against a regex expression and route the request
 * accordingly.
 *
 *
 * @package    Zend\Mvc\Router\Http
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Cookie
    implements RouteInterface
{

    /**
     * The names of the cookies to look for
     *
     * @var array
     */
    private $cookies;

    /**
     * Constraints for parameters.
     *
     * @var array
     */
    private $constraints;

    /**
     * Default values.
     *
     * @var array
     */
    private $defaults;

    /**
     *
     * @param string|array $cookies
     * @param array        $constraints
     * @param array        $defaults
     */
    public function __construct( $cookies,
                                 array $constraints = array( ),
                                 array $defaults = array( ) )
    {
        $this->cookies     = (array) $cookies;
        $this->defaults    = $defaults;
        $this->constraints = $constraints;
    }

    /**
     * factory(): defined by RouteInterface interface.
     *
     * @see    Route::factory()
     * @param  array|\Traversable                                  $options
     * @throws \Zend\Mvc\Router\Exception\InvalidArgumentException
     * @return Cookie
     */
    public static function factory( $options = array( ) )
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray( $options );
        } elseif ( !is_array( $options ) ) {
            throw new Exception\InvalidArgumentException( __METHOD__ . ' expects an array or Traversable set of options' );
        }

        if ( !isset( $options[ 'cookies' ] ) ) {
            throw new Exception\InvalidArgumentException( 'Missing "cookie" in options array' );
        }

        if ( !isset( $options[ 'constraints' ] ) ) {
            $options[ 'constraints' ] = array( );
        }

        if ( !isset( $options[ 'defaults' ] ) ) {
            $options[ 'defaults' ] = array( );
        }

        return new static( $options[ 'cookies' ], $options[ 'constraints' ], $options[ 'defaults' ] );
    }

    /**
     * assemble(): Defined by RouteInterface interface.
     *
     * @see    Route::assemble()
     * @param  array                              $params
     * @param  array                              $options
     * @return mixed
     * @throws Exception\InvalidArgumentException
     */
    public function assemble( array $params = array( ),
                              array $options = array( ) )
    {
        //cookie's don't contribute to the path
        return '';
    }

    /**
     * getAssembledParams(): defined by RouteInterface interface.
     *
     * @see    Route::getAssembledParams
     * @return array
     */
    public function getAssembledParams()
    {
        return array( );
    }

    /**
     * match(): defined by RouteInterface interface.
     *
     * @see    Route::match()
     * @param  Request    $request
     * @return RouteMatch
     */
    public function match( Request $request )
    {
        /* @var $request HttpRequest */
        if ( !method_exists( $request, 'getCookie' ) ) {
            return null;
        }

        $results = array( );

        /* @var $cookie \Zend\Http\Header\Cookie */
        //if there's no cookie
        if ( $request->getCookie() === false) {
            $incomingCookies = array();
        } else {
            $incomingCookies = $request->getCookie()->getArrayCopy();
        }

        $results = array( );

        foreach ($this->cookies as $cookie) {
            if ( !array_key_exists( $cookie, $this->constraints ) ) {
                $this->constraints[ $cookie ] = true;
            }

            if ( is_bool( $this->constraints[ $cookie ] ) ) {
                if( $this->constraints[ $cookie ] === true && !array_key_exists( $cookie,
                                                                                 $incomingCookies ) )
                {
                    return null;
                } elseif( $this->constraints[ $cookie ] === false && array_key_exists( $cookie,
                                                                                     $incomingCookies ) )
                {
                    return null;
                }

                $results[ $cookie ] = $this->constraints[ $cookie ];
                continue;
            } elseif ( is_string( $this->constraints[ $cookie ] ) ) {
                if ( !array_key_exists( $cookie, $incomingCookies ) ) {
                    return null;
                }

                $result = preg_match( $this->constraints[ $cookie ],
                                      $incomingCookies[ $cookie ], $matches );

                if (!$result) {
                    return null;
                }
            } else {
                return null;
            }
        }

        return new RouteMatch( array_merge( $this->defaults, $results ) );
    }

}
