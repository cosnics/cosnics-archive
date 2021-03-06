<?php
/**
 * $Id: configuration.class.php 14277 2008-02-08 21:13:49Z Scara84 $
 * @package repository
 */
require_once (Path :: get_admin_path() . 'lib/admin_data_manager.class.php');
/**
 *	This class represents the current configurable settings.
 *	They are retrieved from the DB via the AdminDataManager
 *
 *	@author Hans De Bisschop
 */

class PlatformSetting
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;
    
    /**
     * Parameters defined in the configuration. Stored as an associative array.
     */
    private $params;

    /**
     * Constructor.
     */
    private function PlatformSetting()
    {
        $this->params = array();
        $this->load_platform_settings();
    }

    /**
     * Returns the instance of this class.
     * @return Configuration The instance.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    /**
     * Gets a parameter from the configuration.
     * @param string $section The name of the section in which the parameter
     *                        is located.
     * @param string $name The parameter name.
     * @return mixed The parameter value.
     */
    function get($variable, $application = 'admin')
    {
        $instance = self :: get_instance();
        
        $params = $instance->params;
        
        if (isset($params[$application]))
        {
            $value = $instance->params[$application][$variable];
            return (isset($value) ? $value : null);
        }
        else
        {
            return null;
        }
    }

    function load_platform_settings()
    {
        $settings = AdminDataManager :: get_instance()->retrieve_settings();
        while ($setting = $settings->next_result())
        {
            $this->params[$setting->get_application()][$setting->get_variable()] = $setting->get_value();
        }
    }
}
?>