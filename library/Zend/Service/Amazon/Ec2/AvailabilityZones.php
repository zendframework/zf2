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
 * @package    Zend_Service_Amazon
 * @subpackage Ec2
 */

namespace Zend\Service\Amazon\Ec2;
use Zend\Service\Amazon;
use Zend\Service\Amazon\Ec2\Exception;

/**
 * An Amazon EC2 interface to query which Availibity Zones your account has access to.
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage Ec2
 */
class AvailabilityZones extends AbstractEc2
{
    /**
     * Describes availability zones that are currently available to the account
     * and their states.
     *
     * @param string|array $zoneName            Name of an availability zone.
     * @return array                            An array that contains all the return items.  Keys: zoneName and zoneState.
     */
    public function describe($zoneName = null)
    {
        $params = array();
        $params['Action'] = 'DescribeAvailabilityZones';

        if(is_array($zoneName) && !empty($zoneName)) {
            foreach($zoneName as $k=>$name) {
                $params['ZoneName.' . ($k+1)] = $name;
            }
        } elseif($zoneName) {
            $params['ZoneName.1'] = $zoneName;
        }

        $response = $this->sendRequest($params);

        $xpath  = $response->getXPath();
        $nodes  = $xpath->query('//ec2:item');

        $return = array();
        foreach ($nodes as $k => $node) {
            $item = array();
            $item['zoneName']   = $xpath->evaluate('string(ec2:zoneName/text())', $node);
            $item['zoneState']  = $xpath->evaluate('string(ec2:zoneState/text())', $node);

            $return[] = $item;
            unset($item);
        }

        return $return;
    }
}
