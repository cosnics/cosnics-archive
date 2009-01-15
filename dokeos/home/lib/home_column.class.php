<?php
require_once dirname(__FILE__).'/home_data_manager.class.php';

class HomeColumn {

	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_SORT = 'sort';
	const PROPERTY_WIDTH = 'width';
	const PROPERTY_ROW = 'row';
	const PROPERTY_USER = 'user';
	
	private $id;
	private $defaultProperties;

    function HomeColumn($id = null, $defaultProperties = array ())
    {
    	$this->id = $id;
		$this->defaultProperties = $defaultProperties;
    }
    
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Get the default properties of all user course categories.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_SORT, self :: PROPERTY_WIDTH, self :: PROPERTY_ROW, self :: PROPERTY_USER);
	}
	
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
    
    function get_id()
    {
    	return $this->id;
    }
    
    function set_id($id)
	{
		$this->id = $id;
	}
	
    function get_sort()
    {
    	return $this->get_default_property(self :: PROPERTY_SORT);
    }
    
	function set_sort($sort)
	{
		$this->set_default_property(self :: PROPERTY_SORT, $sort);
	}
    
    function get_title()
    {
    	return $this->get_default_property(self :: PROPERTY_TITLE);
    }
	
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	
    function get_width()
    {
    	return $this->get_default_property(self :: PROPERTY_WIDTH);
    }
	
	function set_width($width)
	{
		$this->set_default_property(self :: PROPERTY_WIDTH, $width);
	}
	
    function get_row()
    {
    	return $this->get_default_property(self :: PROPERTY_ROW);
    }
	
	function set_row($row)
	{
		$this->set_default_property(self :: PROPERTY_ROW, $row);
	}
	
    function get_user()
    {
    	return $this->get_default_property(self :: PROPERTY_USER);
    }
	
	function set_user($user)
	{
		$this->set_default_property(self :: PROPERTY_USER, $user);
	}
	
	function update()
	{
		$hdm = HomeDataManager :: get_instance();
		$success = $hdm->update_home_column($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}
	
	function create()
	{
		$hdm = HomeDataManager :: get_instance();
		$id = $hdm->get_next_home_column_id();
		$this->set_id($id);
		$success = $hdm->create_home_column($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}
	
	function delete()
	{
		$hdm = HomeDataManager :: get_instance();
		$success = $hdm->delete_home_column($this);
		if (!$success)
		{
			return false;
		}

		return true;
	}
	
	function is_empty()
	{
		$hdm = HomeDataManager :: get_instance();
		
		$condition = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $this->get_id());
		
		$blocks_count = $hdm->count_home_blocks($condition);
		
		return ($blocks_count == 0);
	}
}
?>