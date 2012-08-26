<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace Zend\I18n\Translator\Loader;

use Zend\Db\Adapter\Adapter as DbAdapter;
use Zend\Db\Sql\Sql;
use Zend\I18n\Translator\Loader\LoaderInterface;
use Zend\I18n\Translator\Plural\Rule as PluralRule;
use Zend\I18n\Translator\TextDomain;

/**
 * Db loader.
 *
 * @category   Zend
 * @package    Zend_I18n
 * @subpackage Translator
 */
class Database implements LoaderInterface
{

    /**
     * Current dbadapter.
     *
     * @var resource
     */
    protected $dbAdapter;

    /**
     * load(): defined by LoaderInterface.
     *
     * @see    LoaderInterface::load()
     * @param  string $dbAdapter
     * @param  string $locale
     * @return TextDomain
     * @throws Exception\InvalidArgumentException
     */
    public function load($dbAdapter, $locale)
    {
        $this->dbAdapter = $dbAdapter;
        $textDomain = new TextDomain();
        $sql        = new Sql($this->dbAdapter);

        $select = $sql->select();
        $select->from('locale');
        $select->columns(array('locale_plural_forms'));
        $select->where(array('locale_id' => $locale));

        $localeInformation = $this->dbAdapter->query(
            $sql->getSqlStringForSqlObject($select),
            DbAdapter::QUERY_MODE_EXECUTE
        );

        if (!count($localeInformation)) {
            return $textDomain;
        }

        $localeInformation = reset($localeInformation);

        $textDomain->setPluralRules(
            PluralRule::fromString($localeInformation['locale_plural_forms'])
        );

        $select = $sql->select();
        $select->from('message');
        $select->columns(array(
            'message_key',
            'message_translation',
            'message_plural_index'
        ));
        $select->where(array(
            'locale_id'      => $locale,
            'message_domain' => $filename
        ));

        $messages = $this->dbAdapter->query(
            $sql->getSqlStringForSqlObject($select),
            DbAdapter::QUERY_MODE_EXECUTE
        );

        foreach ($messages as $message) {
            if (isset($textDomain[$message['message_key']])) {
                if (!is_array($textDomain[$message['message_key']])) {
                    $textDomain[$message['message_key']] = array(
                        $message['message_plural_index'] => $textDomain[$message['message_key']]
                    );
                }

                $textDomain[$message['message_key']][$message['message_plural_index']]
                    = $message['message_translation'];
            } else {
                $textDomain[$message['message_key']] = $message['message_translation'];
            }
        }

        return $textDomain;
    }
}