<?php
/**
 * @package application.lib.calendar.repo_viewer
 */
require_once dirname(__FILE__).'/../weblcms_block.class.php';
require_once dirname(__FILE__).'/../course/course_user_category.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/announcement/announcement.class.php';
/**
 * This class represents a calendar repo_viewer component which can be used
 * to browse through the possible learning objects to publish.
 */
class WeblcmsNewAnnouncements extends WeblcmsBlock
{
	function run()
	{
		return $this->as_html();
	}

	/*
	 * Inherited
	 */
	function as_html()
	{
		$html = array();

		$html[] = $this->display_header();

		$dm = WeblcmsDataManager :: get_instance();
		$weblcms = $this->get_parent();

		$condition = new EqualityCondition(CourseUserRelation :: PROPERTY_USER, $this->get_user_id(), CourseUserRelation :: get_table_name());
		$courses = $weblcms->retrieve_user_courses($condition);

		$items = array();

		while($course = $courses->next_result())
		{
			$last_visit_date = $dm->get_last_visit_date($course->get_id(),$this->get_user_id(),'announcement',0);

			$conditions = array();
			$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $course->get_id());
			$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'announcement');
			$subselect_condition = new EqualityCondition('type', 'announcement');
			$conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
			$condition = new AndCondition($conditions);

			$publications = $dm->retrieve_content_object_publications_new($condition, new ObjectTableOrder(Announcement :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC));

			while($publication = $publications->next_result())
			{
				if( $publication->get_publication_date() >= $last_visit_date)
				{
					$items[] = array(
						'course' => $course->get_id(),
						'title' => $publication->get_content_object()->get_title(),
						'id' => $publication->get_id()
					);
				}
			}
		}
		$html[] = $this->display_new_items($items);
		$html[] = $this->display_footer();

		return implode("\n", $html);
	}

	function display_new_items($items)
	{
		$weblcms = $this->get_parent();

		$html = array();

		if(count($items) > 0)
		{
			$html[] = '<ul style="padding: 0px; margin: 0px 0px 0px 15px;">';
			foreach($items as $item)
			{

				$html[] = '<li><a href="'. $weblcms->get_link(array('go' => 'courseviewer', 'application' => 'weblcms', 'tool' => 'announcement', 'tool_action' => 'view', 'pid' => $item['id'], 'course' => $item['course'])) .'">'.$item['title'] .'</a>';
				$html[] = '</li>';
			}
			$html[] = '</ul>';
		}
		else
		{
			$html[] = Translation :: get('NoNewAnnouncements');
		}
		return implode($html, "\n");
	}
}
?>