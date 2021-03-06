<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once dirname(__FILE__) . '/../personal_calendar_data_manager.class.php';
require_once dirname(__FILE__) . '/../calendar_event_publication.class.php';
require_once dirname(__FILE__) . '/../calendar_event_publication_user.class.php';
require_once dirname(__FILE__) . '/../calendar_event_publication_group.class.php';
require_once Path :: get_library_path() . 'condition/condition_translator.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';
/**
 * This is an implementation of a personal calendar datamanager using the PEAR::
 * MDB2 package as a database abstraction layer.
 */
class DatabasePersonalCalendarDatamanager extends PersonalCalendarDatamanager
{
    /**
     * @var Database
     */
	private $database;

    function initialize()
    {
        $this->database = new Database(array());
        $this->database->set_prefix('personal_calendar_');
    }

    function get_database()
    {
        return $this->database;
    }

    function create_storage_unit($name, $properties, $indexes)
    {
        return $this->database->create_storage_unit($name, $properties, $indexes);
    }

    public function content_object_is_published($object_id)
    {
        $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT, $object_id);
        return $this->database->count_objects(CalendarEventPublication :: get_table_name(), $condition) >= 1;
    }

    public function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT, $object_ids);
        return $this->database->count_objects(CalendarEventPublication :: get_table_name(), $condition) >= 1;
    }

    /**
     * @see Application::get_content_object_publication_attributes()
     */
    public function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $query = 'SELECT * FROM ' . $this->database->get_table_name(CalendarEventPublication :: get_table_name()) . ' WHERE ' . $this->database->escape_column_name(CalendarEventPublication :: PROPERTY_PUBLISHER) . '=?';

                $order = array();
                for($i = 0; $i < count($order_property); $i ++)
                {
                    if ($order_property[$i] == 'application')
                    {
                    }
                    elseif ($order_property[$i] == 'location')
                    {
                    }
                    elseif ($order_property[$i] == 'title')
                    {
                    }
                    else
                    {
                    }
                }
                if (count($order))
                {
                    $query .= ' ORDER BY ' . implode(', ', $order);
                }

                $statement = $this->database->get_connection()->prepare($query);
                $res = $statement->execute(Session :: get_user_id());
                $statement->free();
            }
        }
        else
        {
            $query = 'SELECT * FROM ' . $this->database->get_table_name(CalendarEventPublication :: get_table_name()) . ' WHERE ' . $this->database->escape_column_name(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT) . '=?';
            $statement = $this->database->get_connection()->prepare($query);
            $res = $statement->execute($object_id);
            $statement->free();
        }
        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record['id']);
            $info->set_publisher_user_id($record['publisher']);
            $info->set_publication_date($record['publication_date']);
            $info->set_application('personal_calendar');
            //TODO: i8n location string
            $info->set_location('');
            //TODO: set correct URL
            $info->set_url('index_personal_calendar.php?pid=' . $record['id']);
            $info->set_publication_object_id($record['calendar_event']);
            $publication_attr[] = $info;
        }
        return $publication_attr;
    }

    /**
     * @see Application::get_content_object_publication_attribute()
     */
    public function get_content_object_publication_attribute($publication_id)
    {
        $record = $this->retrieve_calendar_event_publication($publication_id);

        $info = new ContentObjectPublicationAttributes();
        $info->set_id($record->get_id());
        $info->set_publisher_user_id($record->get_publisher());
        $info->set_publication_date($record->get_publication_date());
        $info->set_application('personal_calendar');
        //TODO: i8n location string
        $info->set_location('');
        //TODO: set correct URL
        $info->set_url('index_personal_calendar.php?pid=' . $record->get_id());
        $info->set_publication_object_id($record->get_content_object());
        return $info;
    }

    /**
     * @see Application::count_publication_attributes()
     */
    public function count_publication_attributes($type = null, $condition = null)
    {
        $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_PUBLISHER, Session :: get_user_id());
        return $this->database->count_objects(CalendarEventPublication :: get_table_name(), $condition);
    }

    /**
     * @see Application::delete_content_object_publications()
     */
    public function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT, $object_id);
        $this->database->delete(CalendarEventPublication :: get_table_name(), $condition);
    }

    /**
     * @see Application::update_content_object_publication_id()
     */
    function update_content_object_publication_id($publication_attr)
    {
        $where = $this->database->escape_column_name('id') . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->database->escape_column_name('content_object')] = $publication_attr->get_publication_object_id();
        $this->database->get_connection()->loadModule('Extended');
        return $this->database->get_connection()->extended->autoExecute($this->database->get_table_name('publication'), $props, MDB2_AUTOQUERY_UPDATE, $where);
    }

    function get_next_calendar_event_publication_id()
    {
        return $this->database->get_next_id(CalendarEventPublication :: get_table_name());
    }

    //Inherited
    function retrieve_calendar_event_publication($id)
    {
        $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_ID, $id);
        return $this->database->retrieve_object(CalendarEventPublication :: get_table_name(), $condition, array(), CalendarEventPublication :: CLASS_NAME);
    }

    //Inherited.
    function retrieve_calendar_event_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->database->retrieve_objects(CalendarEventPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, CalendarEventPublication :: CLASS_NAME);
    }

    function retrieve_shared_calendar_event_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $query = 'SELECT DISTINCT ' . $this->database->get_alias(CalendarEventPublication :: get_table_name()) . '.* FROM ' . $this->database->escape_table_name(CalendarEventPublication :: get_table_name()) . ' AS ' . $this->database->get_alias(CalendarEventPublication :: get_table_name());
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name('publication_user') . ' AS ' . $this->database->get_alias('publication_user') . ' ON ' . $this->database->get_alias(CalendarEventPublication :: get_table_name()) . '.id = ' . $this->database->get_alias('publication_user') . '.publication_id';
        $query .= ' LEFT JOIN ' . $this->database->escape_table_name('publication_group') . ' AS ' . $this->database->get_alias('publication_group') . ' ON ' . $this->database->get_alias(CalendarEventPublication :: get_table_name()) . '.id = ' . $this->database->get_alias('publication_group') . '.publication_id';

        return $this->database->retrieve_result_set($query, CalendarEventPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, CalendarEventPublication :: CLASS_NAME);
    }

    //Inherited.
    function update_calendar_event_publication($calendar_event_publication)
    {
        // Delete target users and groups
        $condition = new EqualityCondition('publication_id', $calendar_event_publication->get_id());
        $this->database->delete_objects(CalendarEventPublicationUser :: get_table_name(), $condition);
        $this->database->delete_objects(CalendarEventPublicationGroup :: get_table_name(), $condition);

        // Add updated target users and groups
        if(!$this->create_calendar_event_publication_users($calendar_event_publication))
        {
        	return false;
        }

        if(!$this->create_calendar_event_publication_groups($calendar_event_publication))
        {
        	return false;
        }

        $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_ID, $calendar_event_publication->get_id());
        return $this->database->update($calendar_event_publication, $condition);
    }

    //Inherited
    function delete_calendar_event_publication($calendar_event_publication)
    {
        $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_ID, $calendar_event_publication->get_id());
        return $this->database->delete(CalendarEventPublication :: get_table_name(), $condition);
    }

    //Inherited.
    function delete_calendar_event_publications($object_id)
    {
        $condition = new EqualityCondition(CalendarEventPublication :: PROPERTY_CALENDAR_EVENT, $object_id);
        return $this->database->delete_objects(CalendarEventPublication :: get_table_name(), $condition);
    }

    //Inherited.
    function update_calendar_event_publication_id($publication_attr)
    {
        $where = $this->database->escape_column_name(CalendarEventPublication :: PROPERTY_ID) . '=' . $publication_attr->get_id();
        $props = array();
        $props[$this->database->escape_column_name(CalendarEventPublication :: PROPERTY_PROFILE)] = $publication_attr->get_publication_object_id();
        $this->database->get_connection()->loadModule('Extended');
        if ($this->database->get_connection()->extended->autoExecute($this->database->get_table_name(CalendarEventPublication :: get_table_name()), $props, MDB2_AUTOQUERY_UPDATE, $where))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function create_calendar_event_publication($publication)
    {
        if (! $this->database->create($publication))
        {
            return false;
        }

        if(!$this->create_calendar_event_publication_users($publication))
        {
        	return false;
        }

        if(!$this->create_calendar_event_publication_groups($publication))
        {
        	return false;
        }

        return true;
    }

    function create_calendar_event_publication_user($publication_user)
    {
        return $this->database->create($publication_user);
    }

    function create_calendar_event_publication_users($publication)
    {
        $users = $publication->get_target_users();

        foreach ($users as $index => $user_id)
        {
        	$publication_user = new CalendarEventPublicationUser();
        	$publication_user->set_publication($publication->get_id());
        	$publication_user->set_user($user_id);

        	if (!$publication_user->create())
        	{
        		return false;
        	}
        }

        return true;
    }

    function create_calendar_event_publication_group($publication_group)
    {
        return $this->database->create($publication_group);
    }

    function create_calendar_event_publication_groups($publication)
    {
    	$groups = $publication->get_target_groups();

    	foreach ($groups as $index => $group_id)
        {
            $publication_group = new CalendarEventPublicationGroup();
        	$publication_group->set_publication($publication->get_id());
        	$publication_group->set_group_id($group_id);

        	if (!$publication_group->create())
        	{
        		return false;
        	}
        }

        return true;
    }

    function retrieve_calendar_event_publication_target_groups($calendar_event_publication)
    {
    	$condition = new EqualityCondition(CalendarEventPublicationGroup :: PROPERTY_PUBLICATION, $calendar_event_publication->get_id());
    	$groups = $this->database->retrieve_objects(CalendarEventPublicationGroup :: get_table_name(), $condition, null, null, array(), CalendarEventPublicationGroup :: CLASS_NAME);

        $target_groups = array();
        while ($group = $groups->next_result())
        {
            $target_groups[] = $group->get_group_id();
        }

        return $target_groups;
    }

    function retrieve_calendar_event_publication_target_users($calendar_event_publication)
    {
    	$condition = new EqualityCondition(CalendarEventPublicationUser :: PROPERTY_PUBLICATION, $calendar_event_publication->get_id());
    	$users = $this->database->retrieve_objects(CalendarEventPublicationUser :: get_table_name(), $condition, null, null, array(), CalendarEventPublicationUser :: CLASS_NAME);

        $target_users = array();
        while ($user = $users->next_result())
        {
            $target_users[] = $user->get_user();
        }

        return $target_users;
    }
}
?>