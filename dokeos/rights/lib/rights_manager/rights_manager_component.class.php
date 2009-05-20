<?php
/**
 * @package user.usermanager
 */

require_once Path :: get_library_path() . 'core_application_component.class.php';

/**
 * Base class for a user manager component.
 * A user manager provides different tools to the end user. Each tool is
 * represented by a user manager component and should extend this class.
 */

abstract class RightsManagerComponent extends CoreApplicationComponent
{
    function RightsManagerComponent($rights_manager)
    {
    	parent :: __construct($rights_manager);
    }

	function retrieve_users($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_users($condition, $offset, $count, $order_property, $order_direction);
	}

	function retrieve_groups($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_groups($condition, $offset, $count, $order_property, $order_direction);
	}

	function retrieve_roles($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_roles($condition, $offset, $count, $order_property, $order_direction);
	}

	function retrieve_role($id)
	{
		return $this->get_parent()->retrieve_role($id);
	}

	function retrieve_rights($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_rights($condition, $offset, $count, $order_property, $order_direction);
	}

	function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->get_parent()->retrieve_locations($condition, $offset, $count, $order_property, $order_direction);
	}

	function retrieve_role_right_location($right_id, $role_id, $location_id)
	{
		return $this->get_parent()->retrieve_role_right_location($right_id, $role_id, $location_id);
	}

	function retrieve_user_role($user_id, $location_id)
	{
		return $this->get_parent()->retrieve_user_role($user_id, $location_id);
	}

	function retrieve_group_role($group_id, $location_id)
	{
		return $this->get_parent()->retrieve_group_role($group_id, $location_id);
	}

	function retrieve_location($location_id)
	{
		return $this->get_parent()->retrieve_location($location_id);
	}

	function count_users($conditions = null)
	{
		return $this->get_parent()->count_users($conditions);
	}

	function delete_role($role)
	{
		return $this->get_parent()->delete_role($role);
	}

	function count_groups($conditions = null)
	{
		return $this->get_parent()->count_groups($conditions);
	}

	function count_roles($conditions = null)
	{
		return $this->get_parent()->count_roles($conditions);
	}

	/**
	 * @see RightsManager::retrieve_user()
	 */
	function retrieve_user($id)
	{
		return $this->get_parent()->retrieve_user($id);
	}

	function retrieve_group($id)
	{
		return $this->get_parent()->retrieve_group($id);
	}

	/**
	 * @see RightsManager::User_deletion_allowed()
	 */
	function user_deletion_allowed($user)
	{
		return $this->get_parent()->user_deletion_allowed($user);
	}
	/**
	 * @see RightsManager::get_user_editing_url()
	 */
	function get_user_editing_url($user)
	{
		return $this->get_parent()->get_user_editing_url($user);
	}

	function get_group_editing_url($group)
	{
		return $this->get_parent()->get_group_editing_url($group);
	}

	/**
	 * @see RightsManager::get_user_quota_url()
	 */
	function get_user_quota_url($user)
	{
		return $this->get_parent()->get_user_quota_url($user);
	}

	function get_user_roles_url($user)
	{
		return $this->get_parent()->get_user_roles_url($user);
	}

	function get_group_roles_url($group)
	{
		return $this->get_parent()->get_group_roles_url($group);
	}

	function get_role_editing_url($role)
	{
		return $this->get_parent()->get_role_editing_url($role);
	}

	function get_role_deleting_url($role)
	{
		return $this->get_parent()->get_role_deleting_url($role);
	}

	function is_allowed($right, $role_id, $location_id)
	{
		return $this->get_parent()->is_allowed($right, $role_id, $location_id);
	}
}
?>