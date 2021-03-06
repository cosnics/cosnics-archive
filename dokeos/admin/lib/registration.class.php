<?php
require_once Path :: get_common_path() . 'data_class.class.php';

/**
 * @package admin.lib
 * @author Hans De Bisschop
 */

require_once dirname(__FILE__) . '/admin_data_manager.class.php';

class Registration extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_TYPE = 'type';
    const PROPERTY_NAME = 'name';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_VERSION = 'version';
    
    const TYPE_CONTENT_OBJECT = 'content_object';
    const TYPE_APPLICATION = 'application';
    
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    
    /**
     * Get the default properties of registrations.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ID, self :: PROPERTY_TYPE, self :: PROPERTY_NAME, self :: PROPERTY_STATUS, self :: PROPERTY_VERSION));
    }
    
	/**
	 * inherited
	 */
	function get_data_manager()
	{
		return AdminDataManager :: get_instance();	
	}
    
    /**
     * Returns the type of this registration.
     * @return int The type
     */
    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    /**
     * Returns the name of this registration.
     * @return int the name
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the status of this registration.
     * @return int the status
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Returns the version of the registered item.
     * @return String the version
     */
    function get_version()
    {
        return $this->get_default_property(self :: PROPERTY_VERSION);
    }

    /**
     * Sets the type of this registration.
     * @param Int $id the registration type.
     */
    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    /**
     * Sets the name of this registration.
     * @param int $name the name.
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Sets the status of this registration.
     * @param int $status the status.
     */
    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    /**
     * Sets the version of this registered item.
     * @param String $version the version.
     */
    function set_version($version)
    {
        $this->set_default_property(self :: PROPERTY_VERSION, $version);
    }

    function is_active()
    {
        return $this->get_status();
    }

    function toggle_status()
    {
        $this->set_status(! $this->get_status());
    }

    static function get_table_name()
    {
        return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>
