<?php

/**
 * @package users.lib.trackers
 */

require_once Path :: get_tracking_path() . 'lib/default_tracker.class.php';

/**
 * This class tracks the login that a user uses
 */
class LoginLogoutTracker extends DefaultTracker
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'login_tracker';

	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_DATE = 'date';
	const PROPERTY_IP = 'ip';
	const PROPERTY_TYPE = 'type';

	/**
	 * Constructor sets the default values
	 */
    function LoginLogoutTracker()
    {
    	parent :: MainTracker('login_tracker');
    }

    /**
     * Inherited
     * @see MainTracker :: track()
     */
    function track($parameters = array())
    {
    	$user = $parameters['user'];
    	$server = $parameters['server'];
    	$type = $parameters['event'];

    	$this->set_user_id($user->get_id());
    	$this->set_date(time());
    	$this->set_ip($server['REMOTE_ADDR']);
    	$this->set_type($type);

    	$this->create();
    }

    /**
     * Inherited
     * @see MainTracker :: empty_tracker
     */
    function empty_tracker($event)
    {
    	$condition = new EqualityCondition('type', $event->get_name());
    	return $this->remove($condition);
    }

    /**
     * Inherited
     */
    function export($start_date, $end_date, $event)
    {
    	$conditions = array();
    	$conditions[] = new EqualityCondition('type', $event->get_name());
    	return parent :: export($start_date, $end_date, $conditions);
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

    /**
     * Inherited
     */
    function get_default_property_names()
    {
    	return array_merge(MainTracker :: get_default_property_names(), array(self :: PROPERTY_TYPE,
    				 self :: PROPERTY_USER_ID, self :: PROPERTY_DATE, self :: PROPERTY_IP));
    }

    /**
     * Inherited
     * @see MainTracker :: is_summary_tracker
     */
    function is_summary_tracker()
    {
    	return false;
    }

	static function get_table_name()
	{
		return self :: TABLE_NAME;
	}
}
?>