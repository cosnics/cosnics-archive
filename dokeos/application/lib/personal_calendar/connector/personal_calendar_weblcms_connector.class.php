<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once (dirname(__FILE__).'/../personal_calendar_connector.class.php');
require_once (dirname(__FILE__).'/../../weblcms/weblcms_data_manager.class.php');
/**
 * This personal calendar connector allows the personal calendar to retrieve the
 * published calendar events in the WebLcms application.
 */
class PersonalCalendarWeblcmsConnector implements PersonalCalendarConnector
{
	/**
	 * @see PersonalCalendarConnector
	 */
	public function get_events($user, $from_date, $to_date)
	{
		$dm = WeblcmsDataManager :: get_instance();
		$condition = $this->get_conditions($user);

		$publications = $dm->retrieve_content_object_publications_new($condition, array(), 0, -1);
//		$publications = $dm->retrieve_content_object_publications(null, null, $user->get_id(), $course_groups, $condition, false, array (), array (), 0, -1, null, new EqualityCondition('type', 'calendar_event'));
		$result = array ();
		while ($publication = $publications->next_result())
		{
			$object = $publication->get_content_object();

			if ($object->repeats())
			{
				$repeats = $object->get_repeats($from_date, $to_date);

				foreach($repeats as $repeat)
				{
					$event = new PersonalCalendarEvent();
					$event->set_start_date($repeat->get_start_date());
					$event->set_end_date($repeat->get_end_date());
					$event->set_url('run.php?application=weblcms&amp;go=courseviewer&amp;course='.$publication->get_course_id().'&amp;tool='.$publication->get_tool().'&amp;pid='.$publication->get_id());
					$event->set_title($repeat->get_title());
					$event->set_content($repeat->get_description());
					$event->set_source('weblcms');

					$result[] = $event;
				}
			}
			elseif($object->get_start_date() >= $from_date && $object->get_start_date() <= $to_date)
			{
				$event = new PersonalCalendarEvent();
				$event->set_start_date($object->get_start_date());
				$event->set_end_date($object->get_end_date());
				$event->set_url('run.php?application=weblcms&amp;go=courseviewer&amp;course='.$publication->get_course_id().'&amp;tool='.$publication->get_tool().'&amp;pid='.$publication->get_id());
				$event->set_title($object->get_title());
				$event->set_content($object->get_description());
				$event->set_source('weblcms');
				$event->set_id($publication->get_id());
				$result[] = $event;
			}
		}
		return $result;
	}

	function get_conditions($user)
	{
		$dm = WeblcmsDataManager :: get_instance();
		$course_groups = $dm->retrieve_course_groups_from_user($user)->as_array();

		$conditions = array();
		$conditions[] = new EqualityCondition('tool', 'calendar');
		$conditions[] = new EqualityCondition('hidden', 0);
		
		$user_id = $user->get_id();

		$access = array();
		$access[] = new InCondition('user_id', $user_id, $dm->get_database()->get_alias('content_object_publication_user'));
		$access[] = new InCondition('course_group_id', $course_groups, $dm->get_database()->get_alias('content_object_publication_course_group'));
		if (!empty($user_id) || !empty($course_groups))
		{
			$access[] = new AndCondition(array(new EqualityCondition('user_id', null, $dm->get_database()->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $dm->get_database()->get_alias('content_object_publication_course_group'))));
		}

		$conditions[] = new OrCondition($access);
		$subselect_condition = new EqualityCondition('type', 'calendar_event');
		$conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);

		return new AndCondition($conditions);
	}
}
?>