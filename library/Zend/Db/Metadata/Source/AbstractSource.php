<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Metadata\Source;

use Zend\Db\Metadata\MetadataInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Metadata\Object;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Metadata
 */
abstract class AbstractSource implements MetadataInterface
{
    const DEFAULT_SCHEMA = '__DEFAULT_SCHEMA__';

    /**
     *
     * @var Adapter
     */
    protected $adapter = null;

    /**
     *
     * @var string
     */
    protected $defaultSchema = null;

    /**
     *
     * @var array
     */
    protected $data = array();

    /**
     * Constructor
     *
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->defaultSchema = ($adapter->getDefaultSchema()) ?: self::DEFAULT_SCHEMA;
    }

    /**
     * Get schemas
     *
     */
    public function getSchemas()
    {
        $this->loadSchemaData();

        return $this->data['schemas'];
    }

    /**
     * Get table names
     *
     * @param  string $schema
     * @return string[]
     */
    public function getTableNames($schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $this->loadTableNameData($schema);

        return array_keys($this->data['table_names'][$schema]);
    }

    /**
     * Get tables
     *
     * @param  string $schema
     * @return Object\TableObject[]
     */
    public function getTables($schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $tables = array();
        foreach ($this->getTableNames($schema) as $tableName) {
            $tables[] = $this->getTable($tableName, $schema);
        }
        return $tables;
    }

    /**
     * Get table
     *
     * @param  string $tableName
     * @param  string $schema
     * @return Object\TableObject
     */
    public function getTable($tableName, $schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $this->loadTableNameData($schema);

        if (!isset($this->data['table_names'][$schema][$tableName])) {
            throw new \Exception('Table "' . $tableName . '" does not exist');
        }

        $data = $this->data['table_names'][$schema][$tableName];
        switch ($data['table_type']) {
            case 'BASE TABLE':
                $table = new Object\BaseTableObject($tableName);
                break;
            case 'VIEW':
                $table = new Object\ViewObject($tableName);
                $table->setViewDefinition($data['view_definition']);
                $table->setCheckOption($data['check_option']);
                $table->setIsUpdatable($data['is_updatable']);
                break;
            default:
                throw new \Exception('Table "' . $tableName . '" is of an unsupported type "' . $data['table_type'] . '"');
        }
        $table->setColumns($this->getColumns($tableName, $schema));
        return $table;
    }

    /**
     * Get table names
     *
     * @param  string $schema
     * @return string[]
     */
    public function getBaseTableNames($schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $this->loadTableNameData($schema);

        $baseTableNames = array();
        foreach ($this->data['table_names'][$schema] as $tableName => $data) {
            if ('BASE TABLE' == $data['table_type']) {
                $baseTableNames[] = $tableName;
            }
        }
        return $baseTableNames;
    }

    /**
     * Get tables
     *
     * @param  string $schema
     * @return Object\TableObject[]
     */
    public function getBaseTables($schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $baseTables = array();
        foreach ($this->getBaseTableNames($schema) as $tableName) {
            $baseTables[] = $this->getTable($tableName, $schema);
        }
        return $baseTables;
    }

    /**
     * Get table
     *
     * @param  string $baseTableName
     * @param  string $schema
     * @return Object\TableObject
     */
    public function getBaseTable($baseTableName, $schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $this->loadTableNameData($schema);

        $tableNames = $this->data['table_names'][$schema];
        if (isset($tableNames[$baseTableName]) && 'BASE TABLE' == $tableNames[$baseTableName]['table_type']) {
            return $this->getTable($baseTableName, $schema);
        }
        throw new \Exception('Base Table "' . $baseTableName . '" does not exist');
    }

    /**
     * Get view names
     *
     * @param string $schema
     */
    public function getViewNames($schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $this->loadTableNameData($schema);

        $viewNames = array();
        foreach ($this->data['table_names'][$schema] as $tableName => $data) {
            if ('VIEW' == $data['table_type']) {
                $viewNames[] = $tableName;
            }
        }
        return $viewNames;
    }

    /**
     * Get views
     *
     * @param string $schema
     */
    public function getViews($schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $views = array();
        foreach ($this->getViewNames($schema) as $tableName) {
            $views[] = $this->getTable($tableName, $schema);
        }
        return $views;
    }

    /**
     * Get view
     *
     * @param string $viewName
     * @param string $schema
     */
    public function getView($viewName, $schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $this->loadTableNameData($schema);

        $tableNames = $this->data['table_names'][$schema];
        if (isset($tableNames[$viewName]) && 'VIEW' == $tableNames[$viewName]['table_type']) {
            return $this->getTable($viewName, $schema);
        }
        throw new \Exception('View "' . $viewName . '" does not exist');
    }

    /**
     * Gt column names
     *
     * @param  string $table
     * @param  string $schema
     * @return array
     */
    public function getColumnNames($table, $schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $this->loadColumnData($table, $schema);

        if (!isset($this->data['columns'][$schema][$table])) {
            throw new \Exception('"' . $table . '" does not exist');
        }

        return array_keys($this->data['columns'][$schema][$table]);
    }

    /**
     * Get columns
     *
     * @param  string $table
     * @param  string $schema
     * @return array
     */
    public function getColumns($table, $schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $this->loadColumnData($table, $schema);

        $columns = array();
        foreach ($this->getColumnNames($table, $schema) as $columnName) {
            $columns[] = $this->getColumn($columnName, $table, $schema);
        }
        return $columns;
    }

    /**
     * Get column
     *
     * @param  string $columnName
     * @param  string $table
     * @param  string $schema
     * @return Object\ColumnObject
     */
    public function getColumn($columnName, $table, $schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $this->loadColumnData($table, $schema);

        if (!isset($this->data['columns'][$schema][$table][$columnName])) {
            throw new \Exception('A column by that name was not found.');
        }

        $info = $this->data['columns'][$schema][$table][$columnName];

        $column = new Object\ColumnObject($columnName, $table, $schema);
        $column->setOrdinalPosition($info['ordinal_position']);
        $column->setColumnDefault($info['column_default']);
        $column->setIsNullable($info['is_nullable']);
        $column->setDataType($info['data_type']);
        $column->setCharacterMaximumLength($info['character_maximum_length']);
        $column->setCharacterOctetLength($info['character_octet_length']);
        $column->setNumericPrecision($info['numeric_precision']);
        $column->setNumericScale($info['numeric_scale']);
        $column->setNumericUnsigned($info['numeric_unsigned']);
        $column->setErratas($info['erratas']);

        return $column;
    }

    /**
     * Get constraints
     *
     * @param  string $table
     * @param  string $schema
     * @return array
     */
    public function getConstraints($table, $schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $this->loadConstraintData($table, $schema);

        $constraints = array();
        foreach ($this->data['constraint_names'][$schema] as $constraintName => $constraintInfo) {
            if ($constraintInfo['table_name'] == $table) {
                $constraints[] = $this->getConstraint($constraintInfo['constraint_name'], $table, $schema);
            }
        }

        return $constraints;
    }

    /**
     * Get constraint
     *
     * @param  string $constraintName
     * @param  string $table
     * @param  string $schema
     * @param  string $database
     * @return Object\ConstraintObject
     */
    public function getConstraint($constraintName, $table, $schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $this->loadConstraintData($table, $schema);

        $found = false;
        foreach ($this->data['constraint_names'][$schema] as $constraintInfo) {
            if ($constraintInfo['constraint_name'] == $constraintName && $constraintInfo['table_name'] == $table) {
                $found = $constraintInfo;
                break;
            }
        }

        if (!$found) {
            throw new \Exception('Cannot find a constraint by that name in this table');
        }

        $constraint = new Object\ConstraintObject($constraintName, $table, $schema);
        $constraint->setType($found['constraint_type']);
        $constraint->setKeys($this->getConstraintKeys($constraintName, $table, $schema));
        return $constraint;
    }

    /**
     * Get constraint keys
     *
     * @param  string $constraint
     * @param  string $table
     * @param  string $schema
     * @param  string $database
     * @return Object\ConstraintKeyObject
     */
    public function getConstraintKeys($constraint, $table, $schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $this->loadConstraintData($table, $schema);

        // organize references first
        $references = array();
        foreach ($this->data['constraint_references'][$schema] as $refKeyInfo) {
            if ($refKeyInfo['constraint_name'] == $constraint) {
                $references[$refKeyInfo['constraint_name']] = $refKeyInfo;
            }
        }

        $keys = array();
        foreach ($this->data['constraint_keys'][$schema] as $constraintKeyInfo) {
            if ($constraintKeyInfo['table_name'] == $table && $constraintKeyInfo['constraint_name'] === $constraint) {
                $keys[] = $key = new Object\ConstraintKeyObject($constraintKeyInfo['column_name']);
                $key->setOrdinalPosition($constraintKeyInfo['ordinal_position']);
                if (isset($references[$constraint])) {
                    //$key->setReferencedTableSchema($constraintKeyInfo['referenced_table_schema']);
                    $key->setForeignKeyUpdateRule($references[$constraint]['update_rule']);
                    $key->setForeignKeyDeleteRule($references[$constraint]['delete_rule']);
                    //$key->setReferencedTableSchema($references[$constraint]['referenced_table_schema']);
                    $key->setReferencedTableName($references[$constraint]['referenced_table_name']);
                    $key->setReferencedColumnName($references[$constraint]['referenced_column_name']);
                }
            }
        }

        return $keys;
    }

    /**
     * Get trigger names
     *
     * @param string $schema
     */
    public function getTriggerNames($schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $this->loadTriggerData($schema);

        return array_keys($this->data['triggers'][$schema]);
    }

    /**
     * Get triggers
     *
     * @param string $schema
     */
    public function getTriggers($schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $triggers = array();
        foreach ($this->getTriggerNames($schema) as $triggerName) {
            $triggers[] = $this->getTrigger($triggerName, $schema);
        }
        return $triggers;
    }

    /**
     * Get trigger
     *
     * @param string $triggerName
     * @param string $schema
     */
    public function getTrigger($triggerName, $schema = null)
    {
        if ($schema === null) {
            $schema = $this->defaultSchema;
        }

        $this->loadTriggerData($schema);

        if (!isset($this->data['triggers'][$schema][$triggerName])) {
            throw new \Exception('Trigger "' . $triggerName . '" does not exist');
        }

        $info = $this->data['triggers'][$schema][$triggerName];

        $trigger = new Object\TriggerObject();

        $trigger->setName($triggerName);
        $trigger->setEventManipulation($info['event_manipulation']);
        $trigger->setEventObjectCatalog($info['event_object_catalog']);
        $trigger->setEventObjectSchema($info['event_object_schema']);
        $trigger->setEventObjectTable($info['event_object_table']);
        $trigger->setActionOrder($info['action_order']);
        $trigger->setActionCondition($info['action_condition']);
        $trigger->setActionStatement($info['action_statement']);
        $trigger->setActionOrientation($info['action_orientation']);
        $trigger->setActionTiming($info['action_timing']);
        $trigger->setActionReferenceOldTable($info['action_reference_old_table']);
        $trigger->setActionReferenceNewTable($info['action_reference_new_table']);
        $trigger->setActionReferenceOldRow($info['action_reference_old_row']);
        $trigger->setActionReferenceNewRow($info['action_reference_new_row']);
        $trigger->setCreated($info['created']);

        return $trigger;
    }

    /**
     * Prepare data hierarchy
     *
     * @param string $type
     * @param string $key ...
     */
    protected function prepareDataHierarchy($type)
    {
        $data = &$this->data;
        foreach (func_get_args() as $key) {
            if (!isset($data[$key])) {
                $data[$key] = array();
            }
            $data = &$data[$key];
        }
    }

    protected function loadSchemaData()
    {
    }

    protected function loadTableNameData($schema)
    {
    }

    protected function loadColumnData($table, $schema)
    {
    }

    protected function loadConstraintData($table, $schema)
    {
    }

    protected function loadTriggerData($schema)
    {
    }

}
