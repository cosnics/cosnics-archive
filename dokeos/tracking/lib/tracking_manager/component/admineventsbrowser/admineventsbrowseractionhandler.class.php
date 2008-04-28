<?php
/**
 * @package tracking.lib.tracking_manager.component
 */

/**
 * Class used to handle the actions from a table
 */
class AdminEventsBrowserActionHandler
{
	/**
	 * Eventviewer where this Action Handler belongs to
	 */
	private $eventsbrowser;
	
	/**
	 * Constructor
	 * @param EventsBrowser $eventsbrowser the events browser where this action handler belongs to
	 */
	function AdminEventsBrowserActionHandler($eventsbrowser)
	{ 
		$this->eventsbrowser = $eventsbrowser;
	}
	
	/**
	 * Method to retrieve the available actions
	 * @return Array of actions name => action
	 */
	 function get_actions()
	 {
	 	return array('enable' => Translation :: get('Enable_selected_events'),
	 				 'disable' => Translation :: get('Disable_selected_events'),
	 				 TrackingManager :: ACTION_EMPTY_TRACKER => Translation :: get('Empty_selected_events'));
	 }
	 
	 /**
	  * Handle's an action that has been triggered
	  * @param array $parameters the parameters for the action (exportvalues of form)
	  */
	 function handle_action($parameters)
	 {
	 	$action = $parameters['action'];

	 	$ids = array();
	 	
	 	foreach($parameters as $key => $parameter)
	 	{ 
	 		if(substr($key, 0, 2) == 'id')
	 		{
	 			$ids[] = substr($key, 2);
	 		}
	 		
	 		if($action == 'enable' || $action == 'disable')
	 		{
	 			$this->eventsbrowser->redirect('url', null, null, array(
		 				TrackingManager :: PARAM_ACTION => TrackingManager :: ACTION_CHANGE_ACTIVE, 
		 				TrackingManager :: PARAM_EVENT_ID => $ids, 
		 				TrackingManager :: PARAM_TYPE => 'event',
		 				TrackingManager :: PARAM_EXTRA => $action));
	 		}
	 		else
	 		{
		 		$this->eventsbrowser->redirect('url', null, null, array(
		 				TrackingManager :: PARAM_ACTION => $action, 
		 				TrackingManager :: PARAM_EVENT_ID => $ids, 
		 				TrackingManager :: PARAM_TYPE => 'event'));
	 		}
	 	}
	 }

}
?>