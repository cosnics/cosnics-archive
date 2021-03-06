<?php
/**
 * @package common.database;
 */

require_once dirname(__FILE__) . '/object_result_set.class.php';
require_once dirname(__FILE__) . '/connection.class.php';
require_once Path :: get_library_path() . '/dokeos_utilities.class.php';

/**
 * This class provides basic functionality for database connections
 * Create Table, Get next id, Insert, Update, Delete,
 * Select(with use of conditions), Count(with use of conditions)
 * @author Sven Vanpoucke
 */
class Database
{
    const ALIAS_MAX_SORT = 'max_sort';

    private $connection;
    private $prefix;
    private $aliases;

    /**
     * Constructor
     */
    function Database($aliases = array())
    {
        $this->aliases = $aliases;
        $this->initialize();
    }

    /**
     * Initialiser, creates the connection and sets the database to UTF8
     */
    function initialize()
    {
        $this->connection = Connection :: get_instance()->get_connection();
        $this->connection->setOption('debug_handler', array(get_class($this), 'debug'));
        $this->connection->query('SET NAMES utf8');
    }

    /**
     * Returns the prefix
     * @return String the prefix
     */
    function get_prefix()
    {
        return $this->prefix;
    }

    /**
     * Sets the prefix
     * @param String $prefix
     */
    function set_prefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Returns the connection
     * @return Connection the connection
     */
    function get_connection()
    {
        return $this->connection;
    }

    /**
     * Sets the connection
     * @param Connection $connection
     */
    function set_connection($connection)
    {
        $this->connection = $connection;
    }

    /**
     * Debug function
     * Uncomment the lines if you want to debug
     */
    function debug()
    {
        $args = func_get_args();
        // Do something with the arguments
        if ($args[1] == 'query')
        {
            /*echo '<pre>';
		 	echo($args[2]);
		 	echo '</pre>';*/
        }
    }

    /**
     * Escapes a column name in accordance with the database type.
     * @param string $name The column name.
     * @param boolean $prefix_properties Whether or not to
     *                                                   prefix properties
     *                                                   to avoid collisions.
     * @return string The escaped column name.
     */
    function escape_column_name($name, $storage_unit = null)
    {
        $column_name = '';
        if (!is_null($storage_unit))
        {
            $column_name .= $storage_unit . '.';
        }

        return $column_name . $this->connection->quoteIdentifier($name);

//        // Check whether the name contains a seperator, avoids notices.
//        $contains_table_name = strpos($name, '.');
//        if ($contains_table_name === false)
//        {
//            $table = $name;
//            $column = null;
//        }
//        else
//        {
//            list($table, $column) = explode('.', $name, 2);
//        }
//
//        $prefix = '';
//        if (isset($column))
//        {
//            $prefix = $table . '.';
//            $name = $column;
//        }
//        elseif ($storage_unit)
//        {
//            $prefix = $storage_unit . '.';
//        }
//        return $prefix . $this->connection->quoteIdentifier($name);
    }

    /**
     * Expands a table identifier to the real table name. Currently, this
     * method prefixes the given table name with the user-defined prefix, if
     * any.
     * @param string $name The table identifier.
     * @return string The actual table name.
     */
    function get_table_name($name)
    {
        $dsn = $this->connection->getDSN('array');
        return $dsn['database'] . '.' . $this->prefix . $name;
    }

    /**
     * Escapes a table name in accordance with the database type.
     * @param string $name The table identifier.
     * @return string The escaped table name.
     */
    function escape_table_name($name)
    {
        $dsn = $this->connection->getDSN('array');
        $database_name = $this->connection->quoteIdentifier($dsn['database']);
        return $database_name . '.' . $this->connection->quoteIdentifier($this->prefix . $name);
    }

    /**
     * Maps a record to an object
     * @param Record $record a record from the database
     * @param String $class Class to create new object
     * @return new object from type Class
     */
    function record_to_object($record, $class_name)
    {
        if (! is_array($record) || ! count($record))
        {
            throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
        }
        $default_properties = array();

        $object = new $class_name($default_properties);

        foreach ($object->get_default_property_names() as $property)
        {
            $default_properties[$property] = $record[$property];
        }

        $object->set_default_properties($default_properties);
        return $object;
    }

    /**
     * Creates a storage unit in the system
     * @param String $name the table name
     * @param Array $properties the table properties
     * @param Array $indexes the table indexes
     * @return true if the storage unit is succesfully created
     */
    function create_storage_unit($name, $properties, $indexes)
    {
        $name = $this->get_table_name($name);
        $this->connection->loadModule('Manager');
        $manager = $this->connection->manager;
        // If table allready exists -> drop it
        // @todo This should change: no automatic table drop but warning to user
        $tables = $manager->listTables();
        if (in_array($name, $tables))
        {
            $manager->dropTable($name);
        }
        $options['charset'] = 'utf8';
        $options['collate'] = 'utf8_unicode_ci';
        if (! MDB2 :: isError($manager->createTable($name, $properties, $options)))
        {
            foreach ($indexes as $index_name => $index_info)
            {
                if ($index_info['type'] == 'primary')
                {
                    $index_info['primary'] = 1;
                    if (MDB2 :: isError($manager->createConstraint($name, $index_name, $index_info)))
                    {
                        return false;
                    }
                }
                elseif ($index_info['type'] == 'unique')
                {
                    $index_info['unique'] = 1;
                    if (MDB2 :: isError($manager->createConstraint($name, $index_name, $index_info)))
                    {
                        return false;
                    }
                }
                else
                {
                    if (MDB2 :: isError($manager->createIndex($name, $index_name, $index_info)))
                    {
                        return false;
                    }
                }
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Retrieves the next id for a given table
     * @param String $table_name
     * @return Int the id
     */
    function get_next_id($table_name)
    {
        $id = $this->connection->nextID($this->get_table_name($table_name));
        return $id;
    }

    /**
     *
     */
    function create($object)
    {
        $object_table = $object->get_table_name();

        $props = array();
        foreach ($object->get_default_properties() as $key => $value)
        {
            $props[$this->escape_column_name($key)] = $value;
        }
        $this->connection->loadModule('Extended');

        if ($this->connection->extended->autoExecute($this->get_table_name($object_table), $props, MDB2_AUTOQUERY_INSERT))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Update functionality (can only be used when table has an ID)
     * @param Object $object the object that has to be updated
     * @param String $table_name the table name
     * @param Condition $condition The condition for the item that has to be updated
     * @return True if update is successfull
     */
    function update($object, $condition)
    {
        $object_table = $object->get_table_name();

        $props = array();
        foreach ($object->get_default_properties() as $key => $value)
        {
            $props[$this->escape_column_name($key)] = $value;
        }
        $this->connection->loadModule('Extended');
        $this->connection->extended->autoExecute($this->get_table_name($object_table), $props, MDB2_AUTOQUERY_UPDATE, $condition);

        return true;
    }

    function update_objects($table_name, $properties = array(), $condition, $offset = null, $max_objects = null, $order_by = array())
    {
    	if (count($properties) > 0)
    	{
    		$query = 'UPDATE ' . $this->escape_table_name($table_name) . ' AS ' . $this->get_alias($table_name) . ' SET ';

    		$updates = array();
    		foreach($properties as $column => $property)
    		{
    			$updates[] = $this->escape_column_name($column) . '=' . $property;
    		}

    		$query .= implode(", ", $updates);

    	    $params = array();
	        if (isset($condition))
	        {
	            $translator = new ConditionTranslator($this, $params, $this->get_alias($table_name));
	            $query .= $translator->render_query($condition);
	            $params = $translator->get_parameters();
	        }

			$orders = array();

	        if (is_null($order_by))
	        {
	            $order_by = array();
	        }
	        elseif (! is_array($order_by))
	        {
	            $order_by = array($order_by);
	        }

	        foreach($order_by as $order)
	        {
	            $orders[] = $this->escape_column_name($order->get_property(), ($order->alias_is_set() ? $order->get_alias() : $this->get_alias($table_name))) . ' ' . ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
	        }
	        if (count($orders))
	        {
	            $query .= ' ORDER BY ' . implode(', ', $orders);
	        }

	        if ($max_objects > 0)
	        {
            	$query .= ' LIMIT ' . $max_objects;
        	}

        	//$this->connection->setLimit(intval($max_objects), intval($offset));
        	//$this->connection->setLimit(intval($max_objects));
    		$statement = $this->connection->prepare($query);
    		$statement->execute($params);
    		return true;
    	}
    	else
    	{
    		return true;
    	}
    }

    /**
     * Deletes an object from a table with a given condition
     * @param String $table_name
     * @param Condition $condition
     * @return true if deletion is successfull
     */
    function delete($table_name, $condition)
    {
        $query = 'DELETE FROM ' . $this->escape_table_name($table_name) . ' WHERE ' . $condition;
        $sth = $this->connection->prepare($query);

        if ($res = $sth->execute())
        {
            return true;
        }
        else
        {
            return false;
        }
        $sth->free();
    }

    /**
     * Deletes the objects of a given table
     * @param String $table_name
     * @param Condition $condition the condition
     * @return boolean
     */
    function delete_objects($table_name, $condition = null)
    {
        $query = 'DELETE ' . $this->get_alias($table_name) . '.* FROM ' . $this->escape_table_name($table_name) . ' AS ' . $this->get_alias($table_name);
        $params = array();
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $params, $this->get_alias($table_name));
            $query .= $translator->render_query($condition);
            $params = $translator->get_parameters();
        }

        $statement = $this->connection->prepare($query);

        if ($res = $statement->execute($params))
        {
            return true;
        }
        else
        {
            return false;
        }
        $statement->free();
    }

    /**
     * Drop a given storage unit
     * @param String $table_name
     * @return boolean
     */
    function drop_storage_unit($table_name)
    {
        $this->connection->loadModule('Manager');
        $manager = $this->connection->manager;

        $result = $manager->dropTable($this->escape_table_name($table_name));

        if (MDB2 :: isError($result))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Counts the objects of a table with a given condition
     * @param String $table_name
     * @param Condition $condition
     * return Int the number of objects
     */
    function count_objects($table_name, $condition = null)
    {
        $query = 'SELECT COUNT(*) FROM ' . $this->escape_table_name($table_name) . ' AS ' . $this->get_alias($table_name);

        return $this->count_result_set($query, $table_name, $condition);
    }

    function count_result_set($query, $table_name, $condition = null)
    {
    	$params = array();
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $params, $this->get_alias($table_name));
            $query .= $translator->render_query($condition);
            $params = $translator->get_parameters();
        }

        $sth = $this->connection->prepare($query);

        $res = $sth->execute($params);
        $sth->free();
        $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
        return $record[0];
    }

    /**
     * Retrieves the objects of a given table
     * @param String $table_name
     * @param String $classname The name of the class where the object has to be mapped to
     * @param Condition $condition the condition
     * @param Int $offset the starting offset
     * @param Int $max_objects the max amount of objects to be retrieved
     * @param Array(String) $order_by the list of column names that the objects have to be ordered by
     * @param String $resultset - Optional, the resultset to map the items to
     * @return ResultSet
     */
    function retrieve_objects($table_name, $condition = null, $offset = null, $max_objects = null, $order_by = array(), $class_name = null)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name($table_name) . ' AS ' . $this->get_alias($table_name);
        return $this->retrieve_result_set($query, $table_name, $condition, $offset, $max_objects, $order_by, $class_name);
    }

    function retrieve_result_set($query, $table_name, $condition = null, $offset = null, $max_objects = null, $order_by = array(), $class_name = null)
    {
        $params = array();
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $params, $this->get_alias($table_name));
            $query .= $translator->render_query($condition);
            $params = $translator->get_parameters();
        }

        $orders = array();

//        dump('<strong>Statement</strong><br />' . $query . '<br /><br /><br />');
//        dump($params);
//        dump($order_by);

        if (is_null($order_by))
        {
            $order_by = array();
        }
        elseif (! is_array($order_by))
        {
            $order_by = array($order_by);
        }

        foreach($order_by as $order)
        {
            $orders[] = $this->escape_column_name($order->get_property(), ($order->alias_is_set() ? $order->get_alias() : $this->get_alias($table_name))) . ' ' . ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
        }
        if (count($orders))
        {
            $query .= ' ORDER BY ' . implode(', ', $orders);
        }
        if ($max_objects < 0)
        {
            $max_objects = null;
        }

        $this->connection->setLimit(intval($max_objects), intval($offset));
        $statement = $this->connection->prepare($query);

        $res = $statement->execute($params);

        if (is_null($class_name))
        {
            $class_name = DokeosUtilities :: underscores_to_camelcase($table_name);
        }

        return new ObjectResultSet($this, $res, $class_name);
    }

    function retrieve_max_sort_value($table_name, $column, $condition = null)
    {
        $query = 'SELECT MAX(' . $this->escape_column_name($column) . ') as ' . self :: ALIAS_MAX_SORT . ' FROM' . $this->escape_table_name($table_name) . ' AS ' . $this->get_alias($table_name);

        $params = array();
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $params, $this->get_alias($table_name));
            $query .= $translator->render_query($condition);
            $params = $translator->get_parameters();
        }

        $sth = $this->connection->prepare($query);
        $res = $sth->execute($params);
		$sth->free();
        if ($res->numRows() >= 1)
        {
            $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
            return $record[0];
        }
        else
        {
            return 0;
        }
    }

    function retrieve_next_sort_value($table_name, $column, $condition = null)
    {
    	return $this->retrieve_max_sort_value($table_name, $column, $condition) + 1;
    }

    function truncate_storage_unit($table_name, $optimize = true)
    {
        $this->connection->loadModule('Manager');
        $manager = $this->connection->manager;
        if ($manager->truncateTable($this->escape_table_name($table_name)))
        {
            if ($optimize)
            {
                return $this->optimize_storage_unit($table_name);
            }
            else
            {
                return true;
            }
        }
        else
        {
            return false;
        }
    }

    function optimize_storage_unit($table_name)
    {
        $this->connection->loadModule('Manager');
        $manager = $this->connection->manager;
        if ($manager->vacuum($this->escape_table_name($table_name)))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function retrieve_object($table_name, $condition = null, $order_by = array(), $class_name = null)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name($table_name) . ' AS ' . $this->get_alias($table_name);

        $params = array();
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $params, $this->get_alias($table_name));
            $query .= $translator->render_query($condition);
            $params = $translator->get_parameters();
        }

        $orders = array();

        foreach($order_by as $order)
        {
            $orders[] = $this->escape_column_name($order->get_property(), ($order->alias_is_set() ? $order->get_alias() : $this->get_alias($table_name))) . ' ' . ($order->get_direction() == SORT_DESC ? 'DESC' : 'ASC');
        }
        if (count($orders))
        {
            $query .= ' ORDER BY ' . implode(', ', $orders);
        }

        $this->connection->setLimit(1);
        $statement = $this->connection->prepare($query);

        $res = $statement->execute($params);
        $statement->free();
        $record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
        $res->free();

        if (is_null($class_name))
        {
            $class_name = DokeosUtilities :: underscores_to_camelcase($table_name);
        }

        if ($record)
        {
            return self :: record_to_object($record, $class_name);
        }
        else
        {
            return false;
        }
    }

    function retrieve_distinct($table_name, $column_name, $condition = null)
    {
        $query = 'SELECT DISTINCT(' . $this->escape_column_name($column_name) . ') FROM ' . $this->escape_table_name($table_name) . ' AS ' . $this->get_alias($table_name);;

        $params = array();
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $params, $this->get_alias($table_name));
            $query .= $translator->render_query($condition);
            $params = $translator->get_parameters();
        }

        $statement = $this->connection->prepare($query);

        $res = $statement->execute($params);
		$statement->free();
        $distinct_elements = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $distinct_elements[] = $record[$column_name];
        }

        return $distinct_elements;
    }

    function count_distinct($table_name, $column_name, $condition = null)
    {
        $query = 'SELECT COUNT(DISTINCT(' . $this->escape_column_name($column_name) . ')) FROM ' . $this->escape_table_name($table_name) . ' AS ' . $this->get_alias($table_name);;

        $params = array();
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this, $params, $this->get_alias($table_name));
            $query .= $translator->render_query($condition);
            $params = $translator->get_parameters();
        }

        $statement = $this->connection->prepare($query);

        $res = $statement->execute($params);
        $statement->free();
        $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
        return $record[0];
    }

    function get_alias($table_name)
    {
        if (! $this->aliases[$table_name])
        {
            $possible_name = substr($table_name, 0, 2) . substr($table_name, - 2);
            $index = 0;
            while (array_key_exists($possible_name, $this->aliases))
            {
                $possible_name = $possible_name . $index;
                $index = $index ++;
            }
            $this->aliases[$table_name] = $possible_name;
        }

        return $this->aliases[$table_name];
    }

    /**
     * Function to check whether a column is a date column or not
     * @param String $name the column name
     * @return false (default value)
     */
    static function is_date_column($name)
    {
        return false;
    }
}
?>