<?php

require_once dirname(__FILE__).'/group_data_manager.class.php';
require_once Path :: get_common_path() . 'data_class.class.php';

/**
 * @package users
 */
/**
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */

class GroupRelUser extends DataClass
{
	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_GROUP_ID = 'group_id';
	const PROPERTY_USER_ID = 'user_id';
	
	function get_group_id()
	{
		return $this->get_default_property(self :: PROPERTY_GROUP_ID);
	}
	
	function set_group_id($group_id)
	{
		$this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
	}
	
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}	
	
	/**
	 * Get the default properties of all groups.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_GROUP_ID, self :: PROPERTY_USER_ID);
	}
	
	/**
	 * inherited
	 */
	function get_data_manager()
	{
		return GroupDataManager :: get_instance();	
	}
	
	function create()
	{
		$gdm = $this->get_data_manager();
		return $gdm->create_group_rel_user($this);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>