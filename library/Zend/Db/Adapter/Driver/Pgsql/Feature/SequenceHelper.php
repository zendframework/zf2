<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter\Driver\Pgsql\Feature;

use Zend\Db\TableGateway\Feature\AbstractFeature;
use Zend\Db\Metadata\Metadata;

class SequenceHelper extends AbstractFeature
{
    public function postInitialize()
    {

        if ($this->tableGateway->adapter->getPlatform()->getName() != 'PostgreSQL') {
            // Only postgres require a sequence name
            return;
        }

        // Try to get primary key from the Metadata feature
        $metadata = $this->tableGateway->featureSet->getFeatureByClassName('Zend\Db\TableGateway\Feature\MetadataFeature');
        if ($metadata === false || !isset($metadata->sharedData['metadata'])) {
            throw new Exception\RuntimeException('The MetadataFeature could not be consulted to find the Primary Key');
        }

        if (is_array($metadata->sharedData['metadata']['primaryKey'])) {
            throw new Exception\RuntimeException('Can not build the sequence name with a multi-columns PK');
        }

        $primaryKey = $metadata->sharedData['metadata']['primaryKey'];

        // build the sequence name {table}_{primary_key}_seq
        $sequence = $this->tableGateway->getTable() . '_' . $primaryKey . '_seq';
        $this->tableGateway->setSequence($sequence);
    }
}
