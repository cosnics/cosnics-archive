<?php
/**
 * @package rights
 * @author Hans de Bisschop
 */

require_once Path :: get_common_path() . 'data_class.class.php';

class UserRightLocation extends DataClass
{
	const CLASS_NAME = __CLASS__;
	const PROPERTY_RIGHT_ID = 'right_id';
	const PROPERTY_LOCATION_ID = 'location_id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_VALUE = 'value';

	/**
	 * Get the default properties of all users.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return parent :: get_default_property_names(array(self :: PROPERTY_RIGHT_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_LOCATION_ID, self :: PROPERTY_VALUE));
	}

	/**
	 * inherited
	 */
	function get_data_manager()
	{
		return RightsDataManager :: get_instance();
	}

	function get_right_id()
	{
		return $this->get_default_property(self :: PROPERTY_RIGHT_ID);
	}

	function set_right_id($right_id)
	{
		$this->set_default_property(self :: PROPERTY_RIGHT_ID, $right_id);
	}

	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}

	function get_location_id()
	{
		return $this->get_default_property(self :: PROPERTY_LOCATION_ID);
	}

	function set_location_id($location_id)
	{
		$this->set_default_property(self :: PROPERTY_LOCATION_ID, $location_id);
	}

	function get_value()
	{
		return $this->get_default_property(self :: PROPERTY_VALUE);
	}

	function set_value($value)
	{
		$this->set_default_property(self :: PROPERTY_VALUE, $value);
	}

	function invert()
	{
		$value = $this->get_value();
		$this->set_value(!$value);
	}

	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>