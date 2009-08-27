<?php
require_once Path :: get_rights_path() . 'lib/location_manager/location_manager.class.php';
require_once Path :: get_rights_path() . 'lib/location_manager/location_manager_component.class.php';

class LocationManagerLockerComponent extends LocationManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = Request :: get(LocationManager :: PARAM_LOCATION);
		$failures = 0;

		if (!empty($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$location = $this->retrieve_location($id);
				$location->lock();

				if (!$location->update())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLocationNotLocked';
				}
				else
				{
					$message = 'SelectedLocationsNotLocked';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedLocationLocked';
				}
				else
				{
					$message = 'SelectedLocationsLocked';
				}
			}

			if ($location->get_parent() == 0)
		    {
		        $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_LOCATIONS, LocationManager :: PARAM_LOCATION_ACTION => LocationManager :: ACTION_BROWSE_LOCATIONS, LocationManager :: PARAM_SOURCE => $location->get_application()));
		    }
		    else
		    {
		        $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_LOCATIONS, LocationManager :: PARAM_LOCATION_ACTION => LocationManager :: ACTION_BROWSE_LOCATIONS, LocationManager :: PARAM_SOURCE => $location->get_application(), LocationManager :: PARAM_LOCATION => $location->get_parent()));
		    }
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
		}
	}
}
?>