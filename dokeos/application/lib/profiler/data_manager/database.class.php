<?php
/**
 * @package application.lib.profiler.data_manager.database
 */
require_once dirname(__FILE__).'/../profilerdatamanager.class.php';
require_once dirname(__FILE__).'/../profilepublication.class.php';
require_once dirname(__FILE__).'/database/databaseprofilepublicationresultset.class.php';
require_once 'MDB2.php';

class DatabaseProfilerDataManager extends ProfilerDataManager {

	private $prefix;
	private $repoDM;
	private $userDM;

	const ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE = 'pmb';
	const ALIAS_LEARNING_OBJECT_TABLE = 'lo';

	function initialize()
	{
		PEAR :: setErrorHandling(PEAR_ERROR_CALLBACK, array (get_class(), 'handle_error'));
		$this->repoDM = & RepositoryDataManager :: get_instance();
		$this->userDM = & UsersDataManager :: get_instance();
		$conf = Configuration :: get_instance();
		$this->connection = MDB2 :: connect($conf->get_parameter('database', 'connection_string_profiler'),array('debug'=>3,'debug_handler'=>array('ProfilerDatamanager','debug')));
		if (PEAR::isError($this)) {
   		 die($this->connection->getMessage());
		}
		$this->prefix = $conf->get_parameter('database', 'table_name_prefix');
		$this->connection->query('SET NAMES utf8');
	}

	/**
	 * Escapes a column name
	 * @param string $name
	 */
	private function escape_column_name($name)
	{
		list($table, $column) = explode('.', $name, 2);
		$prefix = '';

		if (isset($column))
		{
			$prefix = $table.'.';
			$name = $column;
		}
		return $prefix.$this->connection->quoteIdentifier($name);
	}

	static function handle_error($error)
	{
		die(__FILE__.':'.__LINE__.': '.$error->getMessage()
		// For debugging only. May create a security hazard.
		.' ('.$error->getDebugInfo().')');
	}

	function debug()
	{
		$args = func_get_args();
		// Do something with the arguments
		if($args[1] == 'query')
		{
			//echo '<pre>';
		 	//echo($args[2]);
		 	//echo '</pre>';
		}
	}

	function escape_table_name($name)
	{
		$dsn = $this->connection->getDSN('array');
		$database_name = $this->connection->quoteIdentifier($dsn['database']);
		return $database_name.'.'.$this->connection->quoteIdentifier($this->prefix.$name);
	}

	/**
	 * Gets the full name of a given table (by adding the database name and a
	 * prefix if required)
	 * @param string $name
	 */
	private function get_table_name($name)
	{
		$dsn = $this->connection->getDSN('array');
		return $dsn['database'].'.'.$this->prefix.$name;
	}

	/**
	 * Translates any type of condition to a SQL WHERE clause.
	 * @param Condition $condition The Condition object.
	 * @param array $params A reference to the query's parameter list.
	 * @param boolean $prefix_learning_object_properties Whether or not to
	 *                                                   prefix learning
	 *                                                   object properties
	 *                                                   to avoid collisions.
	 * @return string The WHERE clause.
	 */
	function translate_condition($condition, & $params, $prefix_learning_object_properties = false)
	{
		if ($condition instanceof AggregateCondition)
		{
			return $this->translate_aggregate_condition($condition, & $params, $prefix_learning_object_properties);
		}
		elseif ($condition instanceof InCondition)
		{
			return $this->translate_in_condition($condition, & $params, $prefix_learning_object_properties);
		}
		elseif ($condition instanceof Condition)
		{
			return $this->translate_simple_condition($condition, & $params, $prefix_learning_object_properties);
		}
		else
		{
			die('Need a Condition instance');
		}
	}

	/**
	 * Translates an aggregate condition to a SQL WHERE clause.
	 * @param AggregateCondition $condition The AggregateCondition object.
	 * @param array $params A reference to the query's parameter list.
	 * @param boolean $prefix_learning_object_properties Whether or not to
	 *                                                   prefix learning
	 *                                                   object properties
	 *                                                   to avoid collisions.
	 * @return string The WHERE clause.
	 */
	function translate_aggregate_condition($condition, & $params, $prefix_learning_object_properties = false)
	{
		if ($condition instanceof AndCondition)
		{
			$cond = array ();
			foreach ($condition->get_conditions() as $c)
			{
				$cond[] = $this->translate_condition($c, & $params, $prefix_learning_object_properties);
			}
			return '('.implode(' AND ', $cond).')';
		}
		elseif ($condition instanceof OrCondition)
		{
			$cond = array ();
			foreach ($condition->get_conditions() as $c)
			{
				$cond[] = $this->translate_condition($c, & $params, $prefix_learning_object_properties);
			}
			return '('.implode(' OR ', $cond).')';
		}
		elseif ($condition instanceof NotCondition)
		{
			return 'NOT ('.$this->translate_condition($condition->get_condition(), & $params, $prefix_learning_object_properties) . ')';
		}
		else
		{
			die('Cannot translate aggregate condition');
		}
	}

	/**
	 * Translates an in condition to a SQL WHERE clause.
	 * @param InCondition $condition The InCondition object.
	 * @param array $params A reference to the query's parameter list.
	 * @param boolean $prefix_learning_object_properties Whether or not to
	 *                                                   prefix learning
	 *                                                   object properties
	 *                                                   to avoid collisions.
	 * @return string The WHERE clause.
	 */
	function translate_in_condition($condition, & $params, $prefix_learning_object_properties = false)
	{
		if ($condition instanceof InCondition)
		{
			$name = $condition->get_name();
			$where_clause = $this->escape_column_name($name).' IN (';
			$values = $condition->get_values();
			$placeholders = array();
			foreach($values as $index => $value)
			{
				$placeholders[] = '?';
				$params[] = $value;
			}
			$where_clause .= implode(',',$placeholders).')';
			return $where_clause;
		}
		else
		{
			die('Cannot translate in condition');
		}
	}

	/**
	 * Translates a simple condition to a SQL WHERE clause.
	 * @param Condition $condition The Condition object.
	 * @param array $params A reference to the query's parameter list.
	 * @param boolean $prefix_learning_object_properties Whether or not to
	 *                                                   prefix learning
	 *                                                   object properties
	 *                                                   to avoid collisions.
	 * @return string The WHERE clause.
	 */
	function translate_simple_condition($condition, & $params, $prefix_learning_object_properties = false)
	{
		if ($condition instanceof EqualityCondition)
		{
			$name = $condition->get_name();
			$value = $condition->get_value();
			if (self :: is_date_column($name))
			{
				$value = self :: to_db_date($value);
			}
			if (is_null($value))
			{
				return $this->escape_column_name($name).' IS NULL';
			}
			$params[] = $value;
			return $this->escape_column_name($name, $prefix_learning_object_properties).' = ?';
		}
		elseif ($condition instanceof LikeCondition)
		{
			$name = $condition->get_name();
			$value = $condition->get_value();
			if (is_null($value))
			{
				return $this->escape_column_name($name).' IS NULL';
			}
			$params[] = $value;
			return $this->escape_column_name($name, $prefix_learning_object_properties).' LIKE ?';
		}
		elseif ($condition instanceof InequalityCondition)
		{
			$name = $condition->get_name();
			$value = $condition->get_value();
			if (self :: is_date_column($name))
			{
				$value = self :: to_db_date($value);
			}
			$params[] = $value;
			switch ($condition->get_operator())
			{
				case InequalityCondition :: GREATER_THAN :
					$operator = '>';
					break;
				case InequalityCondition :: GREATER_THAN_OR_EQUAL :
					$operator = '>=';
					break;
				case InequalityCondition :: LESS_THAN :
					$operator = '<';
					break;
				case InequalityCondition :: LESS_THAN_OR_EQUAL :
					$operator = '<=';
					break;
				default :
					die('Unknown operator for inequality condition');
			}
			return $this->escape_column_name($name, $prefix_learning_object_properties).' '.$operator.' ?';
		}
		elseif ($condition instanceof PatternMatchCondition)
		{
			$params[] = $this->translate_search_string($condition->get_pattern());
			return $this->escape_column_name($condition->get_name(), $prefix_learning_object_properties).' LIKE ?';
		}
		else
		{
			die('Cannot translate condition');
		}
	}

	//Inherited.
	function get_next_profile_publication_id()
	{
		return $this->connection->nextID($this->get_table_name('profiler_publication'));
	}

	//Inherited.
    function count_profile_publications($condition = null)
    {
		$query = 'SELECT COUNT('.$this->escape_column_name(ProfilePublication :: PROPERTY_ID).') FROM '.$this->escape_table_name('profiler_publication');
		$query .= 'JOIN '.$this->userDM->escape_table_name('user') . 'ON' . $this->escape_table_name('profiler_publication'). '.' . $this->escape_column_name(ProfilePublication :: PROPERTY_PUBLISHER) .'='. $this->userDM->escape_table_name('user') .'.'. $this->userDM->escape_column_name('user_id');

		$params = array ();
		if (isset ($condition))
		{
			// TODO: SCARA - Exclude category from learning object count
			$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
		}

		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		$res->free();
		return $record[0];
    }

    //Inherited
    function retrieve_profile_publication($id)
	{

		$query = 'SELECT * FROM '.$this->escape_table_name('profiler_publication');
		$query .= ' JOIN '.$this->userDM->escape_table_name('user') . 'ON' . $this->escape_table_name('profiler_publication'). '.' . $this->escape_column_name(ProfilePublication :: PROPERTY_PUBLISHER) .'='. $this->userDM->escape_table_name('user') .'.'. $this->userDM->escape_column_name('user_id');
		$query .= ' WHERE '.$this->escape_column_name(ProfilePublication :: PROPERTY_ID).'=?';

		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_profile_publication($record);
	}

    //Inherited.
    function retrieve_profile_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{

		$query = 'SELECT * FROM '.$this->escape_table_name('profiler_publication');
		$query .= 'JOIN '.$this->userDM->escape_table_name('user') . 'ON' . $this->escape_table_name('profiler_publication'). '.' . $this->escape_column_name(ProfilePublication :: PROPERTY_PUBLISHER) .'='. $this->userDM->escape_table_name('user') .'.'. $this->userDM->escape_column_name('user_id');

		$params = array ();
		if (isset ($condition))
		{
			$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
		}
		$order = array ();

		for ($i = 0; $i < count($orderBy); $i ++)
		{
			$order[] = $this->escape_column_name($orderBy[$i], true).' '. ($orderDir[$i] == SORT_DESC ? 'DESC' : 'ASC');
		}
		if (count($order))
		{
			$query .= ' ORDER BY '.implode(', ', $order);
		}
		if ($maxObjects < 0)
		{
			$maxObjects = null;
		}

		$this->connection->setLimit(intval($maxObjects),intval($offset));
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		return new DatabaseProfilePublicationResultSet($this, $res);
	}

	//Inherited.
	function record_to_profile_publication($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(get_lang('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (ProfilePublication :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new ProfilePublication($record[ProfilePublication :: PROPERTY_ID], $defaultProp);
	}

	//Inherited.
	function update_profile_publication($profile_publication)
	{
		$where = $this->escape_column_name(ProfilePublication :: PROPERTY_ID).'='.$profile_publication->get_id();
		$props = array();
		foreach ($profile_publication->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('profiler_publication'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		return true;
	}

	//Inherited
	function delete_profile_publication($profile_publication)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('profiler_publication').' WHERE '.$this->escape_column_name(ProfilePublication :: PROPERTY_ID).'=?';
		$statement = $this->connection->prepare($query);
		if ($statement->execute($profile_publication->get_id()))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	//Inherited.
	function delete_profile_publications($object_id)
	{
		$condition = new EqualityCondition(ProfilePublication :: PROPERTY_PROFILE, $object_id);
		$publications = $this->retrieve_profile_publications($condition, null, null, null, null, true, array (), array (), 0, -1, $object_id);
		while ($publication = $publications->next_result())
		{
//			$subject = '['.api_get_setting('siteName').'] '.$publication->get_learning_object()->get_title();
//			// TODO: SCARA - Add meaningfull publication removal message
//			$body = 'message';
//			$user = $this->userDM->retrieve_user($publication->get_publisher_id());
//			api_send_mail($user->get_email(), $subject, $body);
			$this->delete_profile_publication($publication);
		}
		return true;
	}


	//Inherited.
	function update_profile_publication_id($publication_attr)
	{
		$where = $this->escape_column_name(ProfilePublication :: PROPERTY_ID).'='.$publication_attr->get_id();
		$props = array();
		$props[$this->escape_column_name(ProfilePublication :: PROPERTY_PROFILE)] = $publication_attr->get_publication_object_id();
		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('profiler_publication'), $props, MDB2_AUTOQUERY_UPDATE, $where))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	//Inherited.
	static function is_date_column($name)
	{
		return ($name == ProfilePublication :: PROPERTY_PUBLISHED);
	}

	//Inherited.
	function any_learning_object_is_published($object_ids)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('profiler_publication').' WHERE '.$this->escape_column_name(ProfilePublication :: PROPERTY_PROFILE).' IN (?'.str_repeat(',?', count($object_ids) - 1).')';
		$res = $this->limitQuery($query, 1, null,$object_ids);
		return $res->numRows() == 1;
	}

	//Inherited.
	function learning_object_is_published($object_id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('profiler_publication').' WHERE '.$this->escape_column_name(ProfilePublication :: PROPERTY_PROFILE).'=?';
		$res = $this->limitQuery($query, 1,null, array ($object_id));
		return $res->numRows() == 1;
	}

	//Inherited
	private function limitQuery($query,$limit,$offset,$params,$is_manip = false)
	{
		$this->connection->setLimit($limit,$offset);
		$statement = $this->connection->prepare($query,null,($is_manip ? MDB2_PREPARE_MANIP : null));
		$res = $statement->execute($params);
		return $res;
	}

	//Inherited
	function create_storage_unit($name,$properties,$indexes)
	{
		$name = $this->get_table_name($name);
		$this->connection->loadModule('Manager');
		$manager = $this->connection->manager;
		// If table allready exists -> drop it
		// @todo This should change: no automatic table drop but warning to user
		$tables = $manager->listTables();
		if( in_array($name,$tables))
		{
			$manager->dropTable($name);
		}
		$options['charset'] = 'utf8';
		$options['collate'] = 'utf8_unicode_ci';
		$manager->createTable($name,$properties,$options);
		foreach($indexes as $index_name => $index_info)
		{
			if($index_info['type'] == 'primary')
			{
				$index_info['primary'] = 1;
				$manager->createConstraint($name,$index_name,$index_info);
			}
			else
			{
				$manager->createIndex($name,$index_name,$index_info);
			}
		}
	}

	//Inherited
	function get_learning_object_publication_attributes($user, $object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		if (isset($type))
		{
			if ($type == 'user')
			{
				$query  = 'SELECT '.self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE.'.*, '. self :: ALIAS_LEARNING_OBJECT_TABLE .'.'. $this->escape_column_name('title') .' FROM '.$this->escape_table_name('profiler_publication').' AS '. self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE .' JOIN '.$this->repoDM->escape_table_name('learning_object').' AS '. self :: ALIAS_LEARNING_OBJECT_TABLE .' ON '. self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE .'.`profile` = '. self :: ALIAS_LEARNING_OBJECT_TABLE .'.`id`';
				$query .= ' WHERE '.self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE. '.'.$this->escape_column_name(ProfilePublication :: PROPERTY_PUBLISHER).'=?';

				$order = array ();
				for ($i = 0; $i < count($order_property); $i ++)
				{
					if ($order_property[$i] == 'application' || $order_property[$i] == 'location')
					{
					}
					elseif($order_property[$i] == 'title')
					{
						$order[] = self :: ALIAS_LEARNING_OBJECT_TABLE. '.' .$this->escape_column_name('title').' '. ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
					}
					else
					{
						$order[] = self :: ALIAS_LEARNING_OBJECT_PUBLICATION_TABLE. '.' .$this->escape_column_name($order_property[$i], true).' '. ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
						$order[] = self :: ALIAS_LEARNING_OBJECT_TABLE. '.' .$this->escape_column_name('title').' '. ($order_direction[$i] == SORT_DESC ? 'DESC' : 'ASC');
					}
				}
				if (count($order))
				{
					$query .= ' ORDER BY '.implode(', ', $order);
				}

				$statement = $this->connection->prepare($query);
				$param = $user->get_user_id();
			}
		}
		else
		{
			$query = 'SELECT * FROM '.$this->escape_table_name('profiler_publication').' WHERE '.$this->escape_column_name(ProfilePublication :: PROPERTY_PROFILE).'=?';
			$statement = $this->connection->prepare($query);
			$param = $object_id;
		}

		$res = $statement->execute($param);

		$publication_attr = array();
		while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$publication = $this->record_to_profile_publication($record);

			$info = new LearningObjectPublicationAttributes();
			$info->set_id($publication->get_id());
			$info->set_publisher_user_id($publication->get_publisher());
			$info->set_publication_date($publication->get_published());
			$info->set_application('Profiler');
			//TODO: i8n location string
			$info->set_location(get_lang('List'));
			$info->set_url('index_profiler.php?go=view&profile='.$publication->get_id());
			$info->set_publication_object_id($publication->get_profile());

			$publication_attr[] = $info;
		}
		return $publication_attr;
	}

	//Indered.
	function get_learning_object_publication_attribute($publication_id)
	{

		$query = 'SELECT * FROM '.$this->escape_table_name('profiler_publication').' WHERE '.$this->escape_column_name(ProfilePublication :: PROPERTY_ID).'=?';
		$statement = $this->connection->prepare($query);
		$this->connection->setLimit(0,1);
		$res = $statement->execute($publication_id);

		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

		$publication = $this->record_to_profile_publication($record);

		$info = new LearningObjectPublicationAttributes();
		$info->set_id($publication->get_id());
		$info->set_publisher_user_id($publication->get_publisher());
		$info->set_publication_date($publication->get_published());
		$info->set_application('Profiler');
		//TODO: i8n location string
		$info->set_location(get_lang('List'));
		$info->set_url('index_profiler.php?go=view&profile='.$publication->get_id());
		$info->set_publication_object_id($publication->get_profile());

		return $info;
	}

	//Inherited.
	function count_publication_attributes($user, $type = null, $condition = null)
	{
		$params = array ();
		$query = 'SELECT COUNT('.$this->escape_column_name(ProfilePublication :: PROPERTY_ID).') FROM '.$this->escape_table_name('profiler_publication').' WHERE '.$this->escape_column_name(ProfilePublication :: PROPERTY_PUBLISHER).'=?';;

		$sth = $this->connection->prepare($query);
		$res = $sth->execute($user->get_user_id());
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	//Inherited.
	function create_profile_publication($publication)
	{
		$props = array();
		foreach ($publication->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(ProfilePublication :: PROPERTY_ID)] = $publication->get_id();

		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('profiler_publication'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>