<?php
/**
 * @package tracking.lib.datamanager
 */
require_once dirname(__FILE__) . '/../tracking_data_manager.class.php';
require_once Path :: get_library_path() . 'configuration/configuration.class.php';
require_once Path :: get_library_path() . 'condition/condition_translator.class.php';

require_once 'MDB2.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *  @author Sven Vanpoucke
==============================================================================
 */

class DatabaseTrackingDataManager extends TrackingDataManager
{
    const ALIAS_EVENTS_TABLE = 'ev';
    const ALIAS_TRACKER_REGISTRATION_TABLE = 'tr';
    const ALIAS_TRACKER_TABLE = 'trk';
    const ALIAS_TRACKER_SETTINGS_TABLE = 'trs';
    const ALIAS_TRACKER_EVENT_TABLE = 'tre';

    /**
     * The database connection.
     * @var Database
     */
    private $database;

    // Inherited.
    function initialize()
    {
        $this->database = new Database();
        $this->database->set_prefix('tracking_');
    }

    function get_database()
    {
        return $this->database;
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    /**
     * Retrieves the tables of this database
     */
    function get_tables()
    {
        $this->database->get_connection()->loadModule('Manager');
        $manager = $this->database->get_connection()->manager;
        return $manager->listTables();
    }

    function get_next_id($table_name)
    {
        return $this->database->get_next_id($table_name);
    }

    /**
     * Creates an event in the database
     * @param Event $event
     */
    function create_event($event)
    {
        return $this->database->create($event);
    }

    /**
     * Creates a tracker registration in the database
     * @param TrackerRegistration $trackerregistration
     */
    function create_tracker_registration($tracker_registration)
    {
        return $this->database->create($tracker_registration);
    }

    /**
     * Registers a tracker to an event
     * @param EventTrackerRelation $eventtrackerrelation
     */
    function create_event_tracker_relation($event_tracker_relation)
    {
        return $this->database->create($event_tracker_relation);
    }

    /**
     * Creates a Tracker Setting in the database
     * @param TrackerSetting $trackersetting
     */
    function create_tracker_setting($tracker_setting)
    {
        return $this->database->create($tracker_setting);
    }

    /**
     * Updates an event (needed for change of activity)
     * @param Event $event
     */
    function update_event($event)
    {
        $condition = new EqualityCondition(Event :: PROPERTY_ID, $event->get_id());
        return $this->database->update($event, $condition);
    }

    /**
     * Updates an event tracker relation (needed for change of activity)
     * @param EventTrackerRelation $eventtrackerrelation
     */
    function update_event_tracker_relation($event_tracker_relation)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(EventRelTracker :: PROPERTY_EVENT_ID, $eventtrackerrelation->get_event_id());
        $conditions[] = new EqualityCondition(EventRelTracker :: PROPERTY_TRACKER_ID, $eventtrackerrelation->get_tracker_id());
        $condition = new AndCondition($conditions);

        return $this->database->update($event_tracker_relation, $condition);
    }

    /**
     * Updates an tracker setting
     * @param TrackerSetting $trackersetting
     */
    function update_tracker_setting($tracker_setting)
    {
        $condition = new EqualityCondition(TrackerSetting :: PROPERTY_ID, $tracker_setting->get_id());
        return $this->database->update($tracker_setting, $condition);
    }

    /**
     * Retrieves the event with the given name
     * @param String $name
     * @return Event event
     */
    function retrieve_event_by_name($event_name, $block = null)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(Event :: PROPERTY_NAME, $event_name);

        if ($block)
        {
            $conditions[] = new EqualityCondition(Event :: PROPERTY_BLOCK, $block);
        }

        $condition = new AndCondition($conditions);

        return $this->database->retrieve_object(Event :: get_table_name(), $condition);
    }

    /**
     * Retrieve all trackers from an event
     * @param int $event_id
     * @param Bool $active true if only the active ones should be shown (default true)
     * @return array of Tracker Registrations
     */
    function retrieve_trackers_from_event($event_id, $active = true)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition('event_id', $event_id);
        if ($active)
        {
            $conditions[] = new EqualityCondition('active', 1);
        }

        $condition = new AndCondition($conditions);

        $relations_result_set = $this->retrieve_event_tracker_relations($condition);
        $relations = $relations_result_set->as_array();

        foreach ($relations as $relation)
        {
            $trackers[] = $this->retrieve_tracker_registration($relation->get_tracker_id(), $relation->get_active());
        }

        return $trackers;
    }

    function retrieve_event_tracker_relations($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->database->retrieve_objects(EventRelTracker :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    /**
     * Retrieves an event tracker relation by given id's
     * @param int $event_id the event id
     * @param int $tracker_id the tracker id
     * @return EventTrackerRelation that belongs to the given id's
     */
    function retrieve_event_tracker_relation($event_id, $tracker_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(EventRelTracker :: PROPERTY_TRACKER_ID, $tracker_id);
        $conditions[] = new EqualityCondition(EventRelTracker :: PROPERTY_EVENT_ID, $event_id);
        $condition = new AndCondition($conditions);

        return $this->database->retrieve_object(EventRelTracker :: get_table_name(), $condition);
    }

    /**
     * Retrieves a tracker registration by the given id
     * @param int $tracker_id the tracker id
     * @return Tracker Registration
     */
    function retrieve_tracker_registration($tracker_id, $active)
    {
        $condition = new EqualityCondition(TrackerRegistration :: PROPERTY_ID, $tracker_id);
        $tracker = $this->database->retrieve_object(TrackerRegistration :: get_table_name(), $condition);
        $tracker->set_active($active);
        return $tracker;
    }

    /**
     * Retrieves all events
     * @return array of events
     */
    function retrieve_events($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
    	return $this->database->retrieve_objects(Event :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    /**
     * Count events for a given condition
     * @param Condition $condition
     * @return Int event count
     */
    function count_events($condition = null)
    {
        return $this->database->count_objects(Event :: get_table_name(), $condition);
    }

    /**
     * Retrieves an event by given id
     * @param int $event_id
     * @return Event $event
     */
    function retrieve_event($event_id)
    {
        $condition = new EqualityCondition(Event :: PROPERTY_ID, $event_id);
        return $this->database->retrieve_object(Event :: get_table_name(), $condition);
    }

    /** Creates a tracker item in the database
     * @param string $tablename the table name where the database has to be written to
     * @param MainTracker $tracker_item a subclass of MainTracker
     * @return true if creation is valid
     */
    function create_tracker_item($tablename, $tracker_item)
    {
        return $this->database->create($tracker_item);
    }

    /**
     * Retrieves all tracker items from the database
     * @param string $tablename the table name where the database has to be written to
     * @param string $classname the tracker's class name (needed to create the class when data is retrieved)
     * @param array $conditons a list of conditions
     * @return MainTracker $tracker a subclass of MainTracker
     */
    function retrieve_tracker_items($table_name, $classname, $condition)
    {
        //$items = $this->database->retrieve_objects($table_name, $condition);
        $items = $this->database->retrieve_objects($table_name, $condition, null, null, array(), $classname);
        return $items->as_array();
    }

    function retrieve_tracker_items_result_set($table_name, $classname, $condition, $order_by)
    {
        return $this->database->retrieve_objects($table_name, $condition, null, null, $order_by, $classname);
    }

    function count_tracker_items($tablename, $condition)
    {
        return $this->database->count_objects($tablename, $condition);
    }

    /**
     * Retrieves a tracker item from the database
     * @param string $tablename the table name where the database has to be written to
     * @param int $id the id of the tracker item
     * @param string $classname the tracker's class name (needed to create the class when data is retrieved)
     * @return MainTracker $tracker a subclass of MainTracker
     */
    function retrieve_tracker_item($tablename, $classname, $id)
    {
        $condition = new EqualityCondition('id', $id);
        return $this->retrieve_tracker_items($tablename, $classname, $condition);
    }

    /**
     * Updates a tracker item in the database
     * @param string $tablename the table name where the database has to be written to
     * @param MainTracker $tracker_item a subclass of MainTracker
     * @return true if update is valid
     */
    function update_tracker_item($tablename, $tracker_item)
    {
        $condition = new EqualityCondition('id', $tracker_item->get_id());
        return $this->database->update($tracker_item, $condition);
    }

    /**
     * Deletes tracker items in the database
     * @param Condition conditon which items should be removed
     * @return true if tracker items are removed
     */
    function remove_tracker_items($tablename, $condition)
    {
        return $this->database->delete_objects($tablename, $condition);
    }

    /**
     * Creates a archive controller item in the database
     * @param ArchiveControllerItem
     * @return true if creation is valid
     */
    function create_archive_controller_item($archive_controller_item)
    {
        return $this->database->create($archive_controller_item);
    }

    /**
     * Convert unix time to DB date
     * @param int $date unix time
     */
    function to_db_date($date)
    {
        if (isset($date))
        {
            return date('Y-m-d H:i:s', $date);
        }
        return null;
    }

    function delete_events($condition = null)
    {
        return $this->database->delete_objects(Event :: get_table_name(), $condition);
    }

    function delete_tracker_registrations($condition = null)
    {
        return $this->database->delete_objects(TrackerRegistration :: get_table_name(), $condition);
    }

    function delete_orphaned_event_rel_tracker()
    {
		$query = 'DELETE FROM '.$this->database->escape_table_name(EventRelTracker :: get_table_name()).' WHERE ';
		$query .= $this->database->escape_column_name(EventRelTracker :: PROPERTY_EVENT_ID).' NOT IN (SELECT '.$this->database->escape_column_name(Event :: PROPERTY_ID).' FROM '.$this->database->escape_table_name(Event :: get_table_name()).') OR ';
		$query .= $this->database->escape_column_name(EventRelTracker :: PROPERTY_TRACKER_ID).' NOT IN (SELECT '.$this->database->escape_column_name(TrackerRegistration :: PROPERTY_ID).' FROM '.$this->database->escape_table_name(TrackerRegistration :: get_table_name()).')';
		$sth = $this->database->get_connection()->prepare($query);
		$res = $sth->execute();
		$sth->free();
		return $res;
    }
}
?>