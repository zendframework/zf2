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
     * Current db.
     *
     * @var Adapter
     */
    protected $db;

    /**
     * locale table name.
     *
     * @var string
     */
    protected $locale_table_name;

    /**
     * Messages table name.
     *
     * @var string
     */
    protected $messages_table_name;

    /**
     * load(): defined by LoaderInterface.
     *
     * @see    LoaderInterface::load()
     * @param  string $db
     * @param  string $locale
     * @return TextDomain
     * @throws Exception\InvalidArgumentException
     */
    public function load($options, $locale)
    {
        $this->db = $options['dbconnection'];
        $this->locale_table_name = $options['locale_table_name'];
        $this->messages_table_name = $options['messages_table_name'];

        
        $textDomain = new TextDomain();
        $sql        = new Sql($this->db);

        $select = $sql->select();
        $select->from($this->locale_table_name);
        $select->columns(array('locale_plural_forms'));
        $select->where(array('locale_id' => $locale));

        $localeInformation = $this->db->query(
            $sql->getSqlStringForSqlObject($select),
            DbAdapter::QUERY_MODE_EXECUTE
        );

        if (!count($localeInformation)) {
            return $textDomain;
        }


        $localeInformation = $localeInformation->current();

        if ( !is_null($localeInformation['locale_plural_forms']) ) {
            $textDomain->setPluralRule = PluralRule::fromString($localeInformation['locale_plural_forms']);
        }

        $localeInformation = reset($localeInformation);

        $select = $sql->select();
        $select->from($this->messages_table_name);
        $select->columns(array(
            'message_key',
            'message_translation',
            'message_plural_index'
        ));
        $select->where(array(
            'locale_id'      => $locale,
            // 'message_domain' => $textDomain
        ));

        $messages = $this->db->query(
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