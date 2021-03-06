<?php

require_once dirname(__FILE__).'/reservations_data_manager.class.php';
require_once Path :: get_common_path() . 'data_class.class.php';

/**
 * @package quota
 */
/**
 *	@author Sven Vanpoucke
 */

class QuotaBox extends DataClass
{
	const PROPERTY_NAME = 'name';
	const PROPERTY_DESCRIPTION = 'description';
	
	const CLASS_NAME = __CLASS__;
	
	/**
	 * Get the default properties of all contributions.
	 * @return array The property titles.
	 */
	static function get_default_property_names()
	{
		return parent :: get_default_property_names(array (self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION));
	}
	
	function get_data_manager()
	{
		return ReservationsDataManager :: get_instance();
	}
	
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}	
	
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}