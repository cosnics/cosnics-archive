<?php
require_once dirname(__FILE__) . '/home_data_manager.class.php';
require_once Path :: get_common_path() . 'data_class.class.php';

class HomeRow extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'row';
    
    const PROPERTY_TITLE = 'title';
    const PROPERTY_SORT = 'sort';
    const PROPERTY_TAB = 'tab';
    const PROPERTY_USER = 'user';
    
    /**
     * Get the default properties of all user course categories.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TITLE, self :: PROPERTY_SORT, self :: PROPERTY_TAB, self :: PROPERTY_USER));
    }
    
	/**
	 * inherited
	 */
	function get_data_manager()
	{
		return HomeDataManager :: get_instance();	
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

    function get_tab()
    {
        return $this->get_default_property(self :: PROPERTY_TAB);
    }

    function set_tab($tab)
    {
        $this->set_default_property(self :: PROPERTY_TAB, $tab);
    }

    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    function create()
    {
        $wdm = $this->get_data_manager();
        $id = $wdm->get_next_home_row_id();
        $this->set_id($id);
        
        $condition = new EqualityCondition(self :: PROPERTY_TAB, $this->get_tab());
        $sort = $wdm->retrieve_max_sort_value(self :: get_table_name(), self :: PROPERTY_SORT, $condition);
        $this->set_sort($sort + 1);
        
        $success = $wdm->create_home_row($this);
        if (! $success)
        {
            return false;
        }
        
        return true;
    }

    static function get_table_name()
    {
    	return self :: TABLE_NAME;
    }
}
?>