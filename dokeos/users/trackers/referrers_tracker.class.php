<?php

/**
 * @package users.lib.trackers
 */
 
require_once dirname(__FILE__) . '/usertracker.class.php';
 
/**
 * This class tracks the referer that a user uses
 */
class ReferrersTracker extends UserTracker
{
	/**
	 * Constructor sets the default values
	 */
    function ReferrersTracker() 
    {
    	parent :: UserTracker();
    	$this->set_property(self :: PROPERTY_TYPE, 'referer');
    }
    
    function track($parameters = array())
    {
    	$server = $parameters['server'];
		$referer = $server['HTTP_REFERER'];
		
		$conditions = array();
    	$conditions[] = new EqualityCondition('type', 'referer');
    	$conditions[] = new EqualityCondition('name', $referer);
    	$condtion = new AndCondition($conditions);
    	
    	$trackeritems = $this->retrieve_tracker_items($condtion);
    	if(count($trackeritems) != 0)
    	{
    		$referertracker = $trackeritems[0];
    		$referertracker->set_value($referertracker->get_value() + 1);
    		$referertracker->update();
    	}
    	else
    	{
    		$this->set_name($referer);
    		$this->set_value(1);
    		$this->create();
    	}
    }
}
?>