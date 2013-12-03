<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

use Traversable;
use Zend\Math\Rand;
use Zend\Session\Container as SessionContainer;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\Result\ValidationResult;

/**
 * CSRF validator
 *
 * Accepted options are:
 *      - hash
 *      - name
 *      - salt
 *      - session
 *      - timeout
 */
class Csrf extends AbstractValidator
{
    /**
     * Error codes
     */
    const NOT_SAME = 'notSame';

    /**
     * Validation error messages templates
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_SAME => "The form submitted did not originate from the expected site",
    );

    /**
     * Static cache of the session names to generated hashes
     *
     * @var array
     */
    protected static $hashCache;

    /**
     * Actual hash used.
     *
     * @var mixed
     */
    protected $hash;

    /**
     * Name of CSRF element (used to create non-colliding hashes)
     *
     * @var string
     */
    protected $name = 'csrf';

    /**
     * Salt for CSRF token
     *
     * @var string
     */
    protected $salt = 'salt';

    /**
     * Session container to use
     *
     * @var SessionContainer
     */
    protected $session;

    /**
     * TTL for CSRF token
     *
     * @var int|null
     */
    protected $timeout = 300;

    /**
     * Retrieve CSRF token
     *
     * If no CSRF token currently exists, or should be regenerated,
     * generates one.
     *
     * @param  bool $regenerate
     * @return string
     */
    public function getHash($regenerate = false)
    {
        if ((null === $this->hash) || $regenerate) {
            if ($regenerate) {
                $this->hash = null;
            } else {
                $this->hash = $this->getValidationToken();
            }

            if (null === $this->hash) {
                $this->generateHash();
            }
        }

        return $this->hash;
    }

    /**
     * Set CSRF name
     *
     * @param  string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = (string) $name;
    }

    /**
     * Get CSRF name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Salt for CSRF token
     *
     * @param  string $salt
     * @return void
     */
    public function setSalt($salt)
    {
        $this->salt = (string) $salt;
    }

    /**
     * Retrieve salt for CSRF token
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set session container
     *
     * @param  SessionContainer $session
     * @return void
     */
    public function setSession(SessionContainer $session)
    {
        $this->session = $session;

        if ($this->hash) {
            $this->initCsrfToken();
        }
    }

    /**
     * Get session container
     *
     * Instantiate session container if none currently exists
     *
     * @return SessionContainer
     */
    public function getSession()
    {
        if (null === $this->session) {
            // Using fully qualified name, to ensure polyfill class alias is used
            $this->session = new SessionContainer($this->getSessionName());
        }

        return $this->session;
    }

    /**
     * Get session namespace for CSRF token
     *
     * Generates a session namespace based on salt, element name, and class.
     *
     * @return string
     */
    public function getSessionName()
    {
        return str_replace('\\', '_', __CLASS__) . '_'
            . $this->getSalt() . '_'
            . strtr($this->getName(), array('[' => '_', ']' => ''));
    }

    /**
     * Set timeout for CSRF session token
     *
     * @param  int $ttl
     * @return void
     */
    public function setTimeout($ttl)
    {
        $this->timeout = (int) $ttl;
    }

    /**
     * Get CSRF session token timeout
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($data, $context = null)
    {
        $hash = $this->getValidationToken();

        if ($data !== $hash) {
            return $this->buildErrorValidationResult($data, self::NOT_SAME);
        }

        return new ValidationResult($data);
    }

    /**
     * Initialize CSRF token in session
     *
     * @return void
     */
    protected function initCsrfToken()
    {
        $session = $this->getSession();
        //$session->setExpirationHops(1, null);
        $timeout = $this->getTimeout();

        if (null !== $timeout) {
            $session->setExpirationSeconds($timeout);
        }

        $session->hash = $this->getHash();
    }

    /**
     * Generate CSRF token
     *
     * Generates CSRF token and stores both in {@link $hash} and element
     * value.
     *
     * @return void
     */
    protected function generateHash()
    {
        if (isset(static::$hashCache[$this->getSessionName()])) {
            $this->hash = static::$hashCache[$this->getSessionName()];
        } else {
            $this->hash = md5($this->getSalt() . Rand::getBytes(32) .  $this->getName());
            static::$hashCache[$this->getSessionName()] = $this->hash;
        }

        $this->initCsrfToken();
    }

    /**
     * Get validation token
     *
     * Retrieve token from session, if it exists.
     *
     * @return null|string
     */
    protected function getValidationToken()
    {
        $session = $this->getSession();

        if (isset($session->hash)) {
            return $session->hash;
        }

        return null;
    }
}
