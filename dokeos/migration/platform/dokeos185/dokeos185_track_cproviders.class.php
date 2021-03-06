<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_track_cproviders.class.php';
require_once dirname(__FILE__) . '/../../../user/trackers/providers_tracker.class.php';

/**
 * This class presents a Dokeos185 track_c_providers
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackCProviders extends ImportTrackCProviders
{
	private static $mgdm;
	
	/**
	 * Dokeos185TrackCProviders properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_PROVIDER = 'provider';
	const PROPERTY_COUNTER = 'counter';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackCProviders object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackCProviders($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_PROVIDER, self :: PROPERTY_COUNTER);
	}

	/**
	 * Sets a default property by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Returns the id of this Dokeos185TrackCProviders.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the provider of this Dokeos185TrackCProviders.
	 * @return the provider.
	 */
	function get_provider()
	{
		return $this->get_default_property(self :: PROPERTY_PROVIDER);
	}

	/**
	 * Returns the counter of this Dokeos185TrackCProviders.
	 * @return the counter.
	 */
	function get_counter()
	{
		return $this->get_default_property(self :: PROPERTY_COUNTER);
	}
	
	/**
	 * Validation checks
	 * @param Array $array
	 */
	function is_valid($array)
	{
		$mgdm = MigrationDataManager :: get_instance();
		if(!$this->get_provider() || $this->get_counter()==null)
		{		 
			$mgdm->add_failed_element($this->get_id(),'track_c_providers');
			return false;
		}
		return true;
	}
	
	/**
	 * Convertion
	 * @param Array $array
	 */
	function convert_to_lcms($array)
	{	
		$conditions = array();
    	$conditions[] = new EqualityCondition('type', 'provider');
    	$conditions[] = new EqualityCondition('name', $this->get_provider());
    	$condtion = new AndCondition($conditions);
		$providertracker = new ProvidersTracker();
		$trackeritems = $providertracker->retrieve_tracker_items($condtion);
    	
    	if(count($trackeritems) != 0)
    	{
    		$providertracker = $trackeritems[0];
    		$providertracker->set_value($providertracker->get_value() + $this->get_counter());
    		$providertracker->update();
    	}
    	else
    	{
    		
    		$providertracker->set_name($this->get_provider());
    		$providertracker->set_value($this->get_counter());
    		$providertracker->create();
    	}
    	return $providertracker;
	}
	
	/**
	 * Gets all the trackers
	 * @param Array $array
	 * @return Array
	 */
	static function get_all($parameters)
	{
		$old_mgdm = $parameters['old_mgdm'];
		
		$db = 'statistics_database';
		$tablename = 'track_c_providers';
		$classname = 'Dokeos185TrackCProviders';
			
		return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);	
	}
	
	static function get_database_table($parameters)
	{
		$array = array();
		$array['database'] = 'statistics_database';
		$array['table'] = 'track_c_providers';
		return $array;
	}
}

?>