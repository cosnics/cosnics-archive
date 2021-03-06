<?php
require_once Path :: get_common_path() . 'data_class.class.php';

/**
 * @package admin.lib
 * @author Hans De Bisschop
 */

require_once dirname(__FILE__) . '/admin_data_manager.class.php';

class Setting extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_VARIABLE = 'variable';
    const PROPERTY_VALUE = 'value';
   
    /**
     * Get the default properties of all settings.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
       return parent :: get_default_property_names(array(self :: PROPERTY_APPLICATION, self :: PROPERTY_VARIABLE, self :: PROPERTY_VALUE));
    }

	/**
	 * inherited
	 */
	function get_data_manager()
	{
		return AdminDataManager :: get_instance();	
	}

    /**
     * Returns the application of this setting object
     * @return string The setting application
     */
    function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    /**
     * Returns the variable of this setting object
     * @return string the variable
     */
    function get_variable()
    {
        return $this->get_default_property(self :: PROPERTY_VARIABLE);
    }

    /**
     * Returns the value of this setting object
     * @return string the value
     */
    function get_value()
    {
        return $this->get_default_property(self :: PROPERTY_VALUE);
    }

    /**
     * Sets the application of this setting.
     * @param string $application the setting application.
     */
    function set_application($application)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $application);
    }

    /**
     * Sets the variable of this setting.
     * @param string $variable the variable.
     */
    function set_variable($variable)
    {
        $this->set_default_property(self :: PROPERTY_VARIABLE, $variable);
    }

    /**
     * Sets the value of this setting.
     * @param string $value the value.
     */
    function set_value($value)
    {
        $this->set_default_property(self :: PROPERTY_VALUE, $value);
    }

    static function get_table_name()
    {
        return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>
