<?php
require_once Path :: get_library_path() . 'application.class.php';

abstract class CoreApplication extends Application
{

    /**
     *
     * @see Application::is_active()
     */
    function is_active($application)
    {
        if (self :: exists($application))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Determines if a given application exists
     * @param string $name
     * @return boolean
     */
    public static function exists($name)
    {
        $application_path = self :: get_application_path($name);
        $application_manager_path = $application_path . '/lib/' . $name . '_manager' . '/' . $name . '_manager.class.php';
        if (file_exists($application_path) && is_dir($application_path) && file_exists($application_manager_path))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public static function get_list()
    {
    	$applications = array();
    	$applications[] = 'admin';
    	$applications[] = 'tracking';
    	$applications[] = 'repository';
    	$applications[] = 'user';
    	$applications[] = 'group';
    	$applications[] = 'rights';
    	$applications[] = 'home';
    	$applications[] = 'menu';
    	$applications[] = 'webservice';
    	$applications[] = 'reporting';
    	
    	return $applications;
    }

    /**
     * Gets a link to the personal calendar application
     * @param array $parameters
     * @param boolean $encode
     */
    public function get_link($parameters = array (), $filter = array(), $encode_entities = false, $application_type = Redirect :: TYPE_CORE)
    {
        return parent :: get_link($parameters, $filter, $encode_entities, $application_type);
    }

    /**
     * @see Application :: simple_redirect
     */
    function simple_redirect($parameters = array (), $filter = array(), $encode_entities = false, $redirect_type = Redirect :: TYPE_URL, $application_type = Redirect :: TYPE_CORE)
    {
        return parent :: simple_redirect($parameters, $filter, $encode_entities, $redirect_type, $application_type);
    }

    /**
     * @see Application :: redirect
     */
    function redirect($message = '', $error_message = false, $parameters = array (), $filter = array(), $encode_entities = false, $redirect_type = Redirect :: TYPE_URL, $application_type = Redirect :: TYPE_CORE)
    {
        return parent :: redirect($message, $error_message, $parameters, $filter, $encode_entities, $redirect_type, $application_type);
    }

    public function get_application_path($application_name)
    {
        return Path :: get(SYS_PATH) . $application_name . '/';
    }

    public function get_application_component_path()
    {
        $application_name = $this->get_application_name();
        return $this->get_application_path($application_name) . 'lib/' . $application_name . '_manager/component/';
    }

    function factory($application, $user = null)
    {
        $manager_path = self :: get_application_path($application) . 'lib/' . $application . '_manager/' . $application . '_manager.class.php';
        require_once $manager_path;
        return parent :: factory($application, $user);
    }
}

?>