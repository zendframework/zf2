<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mail\Protocol\Smtp\Auth;

use Zend\Mail\Protocol\Smtp;

/**
 * Performs XOAUTH2 authentication
 */
class XOAuth2 extends Smtp
{
    /**
     * XOAUTH2 email
     *
     * @var string
     */
    private $email;


    /**
     * XOAUTH2 access token
     * @var string
     */
    private $accessToken;


    /**
     * Constructor.
     *
     * @param  string $host   (Default: 127.0.0.1)
     * @param  int    $port   (Default: null)
     * @param  array  $config Auth-specific parameters
     */
    public function __construct($host = '127.0.0.1', $port = null, $config = null)
    {
        $origConfig = $config;
        if (is_array($host)) {
            if (is_array($config)) {
                $config = array_replace_recursive($host, $config);
            } else {
                $config = $host;
            }
        }

        if (is_array($config)) {
            if (isset($config['email'])) {
                $this->setEmail($config['email']);
            }
            if (isset($config['accessToken'])) {
                $this->setAccessToken($config['accessToken']);
            }
        }

        parent::__construct($host, $port, $origConfig);
    }


    /**
     * Perform XOAUTH2 authentication with supplied credentials
     *
     */
    public function auth()
    {
        parent::auth();

        $xoauthString = sprintf(
            "user=%s\1auth=Bearer %s\1\1",
            $this->email,
            $this->accessToken
        );

        $xoauth = sprintf(
            'AUTH XOAUTH2 %s',
            base64_encode($xoauthString)
        );

        $this->_send($xoauth);
        $this->_expect(235);
        $this->auth = true;
    }

    /**
     * Set value for email
     *
     * @param string $email
     * @return XOAuth2
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Set value for access token
     *
     * @param string $accessToken
     * @return XOAuth2
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }
}
