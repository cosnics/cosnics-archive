<?php
/**
 * @package admin
 * @subpackage package_manager
 * @author Hans De Bisschop
 */
require_once Path :: get_common_path() . 'sub_manager.class.php';
require_once Path :: get_rights_path() . 'lib/location_manager/location_manager_component.class.php';

class LocationManager extends SubManager
{
    const PARAM_LOCATION_ACTION = 'action';
    const PARAM_SOURCE = 'source';
    const PARAM_LOCATION = 'location';

    const ACTION_BROWSE_LOCATIONS = 'browse';
    const ACTION_LOCK_LOCATIONS = 'lock';
    const ACTION_UNLOCK_LOCATIONS = 'unlock';
    const ACTION_INHERIT_LOCATIONS = 'inherit';
    const ACTION_DISINHERIT_LOCATIONS = 'disinherit';

    function LocationManager($rights_manager)
    {
        parent :: __construct($rights_manager);

        $location_action = Request :: get(self :: PARAM_LOCATION_ACTION);
        if ($location_action)
        {
            $this->set_parameter(self :: PARAM_LOCATION_ACTION, $location_action);
        }
    }

    function run()
    {
        $location_action = $this->get_parameter(self :: PARAM_LOCATION_ACTION);

        switch ($location_action)
        {
            case self :: ACTION_BROWSE_LOCATIONS :
                $component = LocationManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_LOCK_LOCATIONS :
                $component = LocationManagerComponent :: factory('Locker', $this);
                break;
            case self :: ACTION_UNLOCK_LOCATIONS :
                $component = LocationManagerComponent :: factory('Unlocker', $this);
                break;
            case self :: ACTION_INHERIT_LOCATIONS :
                $component = LocationManagerComponent :: factory('Inheriter', $this);
                break;
            case self :: ACTION_DISINHERIT_LOCATIONS :
                $component = LocationManagerComponent :: factory('Disinheriter', $this);
                break;
            default :
                $component = LocationManagerComponent :: factory('Browser', $this);
                break;
        }

        $component->run();
    }

    function get_application_component_path()
    {
        return Path :: get_rights_path() . 'lib/location_manager/component/';
    }

    function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_locations($condition, $offset, $count, $order_property);
    }

    function count_locations($conditions = null)
    {
        return $this->get_parent()->count_locations($conditions);
    }

    function retrieve_location($location_id)
    {
        return $this->get_parent()->retrieve_location($location_id);
    }

    function get_location_inheriting_url($location)
    {
        return $this->get_url(array(LocationManager :: PARAM_LOCATION_ACTION => LocationManager :: ACTION_INHERIT_LOCATIONS, LocationManager :: PARAM_LOCATION => $location->get_id()));
    }

    function get_location_disinheriting_url($location)
    {
        return $this->get_url(array(LocationManager :: PARAM_LOCATION_ACTION => LocationManager :: ACTION_DISINHERIT_LOCATIONS, LocationManager :: PARAM_LOCATION => $location->get_id()));
    }

    function get_location_locking_url($location)
    {
        return $this->get_url(array(LocationManager :: PARAM_LOCATION_ACTION => LocationManager :: ACTION_LOCK_LOCATIONS, LocationManager :: PARAM_LOCATION => $location->get_id()));
    }

    function get_location_unlocking_url($location)
    {
        return $this->get_url(array(LocationManager :: PARAM_LOCATION_ACTION => LocationManager :: ACTION_UNLOCK_LOCATIONS, LocationManager :: PARAM_LOCATION => $location->get_id()));
    }
}
?>