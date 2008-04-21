<?php

/**
 * @package users.lib.trackers
 */
 
require_once Path :: get_tracking_path() . 'lib/maintracker.class.php';
 
/**
 * This class tracks the login that a user uses
 */
class LoginTracker extends MainTracker
{
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_DATE = 'date';
	const PROPERTY_IP = 'ip';
	const PROPERTY_TYPE = 'type';
	
	/**
	 * Constructor sets the default values
	 */
    function LoginTracker() 
    {
    	parent :: MainTracker('login');
    }
    
    function track($parameters = array())
    {
    	$user = $parameters['user'];
    	$server = $parameters['server'];
    	$type = $parameters['event'];
    	
    	$this->set_user_id($user->get_user_id());
    	$this->set_date(time());
    	$this->set_ip($server['REMOTE_ADDR']);
    	$this->set_type($type);
    	
    	$this->create();
    }
    
    /**
     * Get's the userid of the login tracker
     * @return int $userid the userid
     */
    function get_user_id()
    {
    	return $this->get_property(self :: PROPERTY_USER_ID);
    }
    
    /**
     * Sets the userid of the login tracker
     * @param int $userid the userid
     */
    function set_user_id($userid)
    {
    	$this->set_property(self :: PROPERTY_USER_ID, $userid);
    }
    
    /**
     * Get's the date of the login tracker
     * @return int $date the date
     */
    function get_date()
    {
    	return $this->get_property(self :: PROPERTY_DATE);
    }
    
    /**
     * Sets the date of the login tracker
     * @param int $date the date
     */
    function set_date($date)
    {
    	$date = $this->to_db_date($date);
    	$this->set_property(self :: PROPERTY_DATE, $date);
    }
    
    /**
     * Get's the ip of the login tracker
     * @return int $ip the ip
     */
    function get_ip()
    {
    	return $this->get_property(self :: PROPERTY_IP);
    }
    
    /**
     * Sets the ip of the login tracker
     * @param int $ip the ip
     */
    function set_ip($ip)
    {
    	$this->set_property(self :: PROPERTY_IP, $ip);
    }
    
    /**
     * Get's the type of the login tracker
     * @return int $type the type
     */
    function get_type()
    {
    	return $this->get_property(self :: PROPERTY_TYPE);
    }
    
    /**
     * Sets the type of the login tracker
     * @param int $type the type
     */
    function set_type($type)
    {
    	$this->set_property(self :: PROPERTY_TYPE, $type);
    }

}
?>