<?php
/**
 * @package tracking.lib
 */
require_once dirname(__FILE__) . '/event.class.php';
require_once dirname(__FILE__) . '/event_rel_tracker.class.php';
require_once dirname(__FILE__) . '/tracking_data_manager.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';

/**
 * Class to create and trigger tracker events
 * @author Sven Vanpoucke
 */
class Events
{

    /** 
     * Create an event
     * @param String $event_name the event name (must be a unique name)
     */
    public static function create_event($event_name, $block)
    {
        $event = new Event();
        $event->set_name($event_name);
        $event->set_active(true);
        $event->set_block($block);
        if (! $event->create())
        {
            return false;
        }
        
        return $event;
    }

    public static function trigger_event($event_name, $block, $parameters = array())
    {
        $adm = AdminDataManager :: get_instance();
        $setting = $adm->retrieve_setting_from_variable_name('enable_tracking', 'tracking');
        
        if ($setting->get_value() != 1)
            return;
        
        $trkdmg = TrackingDataManager :: get_instance();
        $event = $trkdmg->retrieve_event_by_name($event_name, $block);
        
        if (!$event || $event->get_active() == 0)
            return;
        
        $trackerregistrations = $trkdmg->retrieve_trackers_from_event($event->get_id());
        
        foreach ($trackerregistrations as $trackerregistration)
        {
            $classname = $trackerregistration->get_class();
            $filename = DokeosUtilities :: camelcase_to_underscores($classname);
            
            $fullpath = Path :: get(SYS_PATH) . $trackerregistration->get_path() . strtolower($filename) . '.class.php';
            require_once ($fullpath);
            
            $parameters['event'] = $event_name;
            
            $object = new $classname();
            $return[] = $object->track($parameters);
        }
        
        return $return;
    }
}
?>