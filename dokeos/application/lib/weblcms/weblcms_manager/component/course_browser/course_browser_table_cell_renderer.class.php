<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/course_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../course/course_table/default_course_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../course/course.class.php';
require_once dirname(__FILE__).'/../../weblcms_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class CourseBrowserTableCellRenderer extends DefaultCourseTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
* @param WeblcmsBrowserComponent $browser
	 */
	function CourseBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $course)
	{
		if ($column === CourseBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($course);
		}
		
		// Add special features here
		switch ($column->get_name())
		{
			// Exceptions that need post-processing go here ...
		}
		return parent :: render_cell($column, $course);
	}
	/**
	 * Gets the action links to display
	 * @param Course $course The course for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($course)
	{
		$toolbar_data = array();
		
		$course_subscription_url = $this->browser->get_course_subscription_url($course);
		
		if ($course_subscription_url)
		{	
			$toolbar_data[] = array(
				'href' => $course_subscription_url,
				'label' => Translation :: get('Subscribe'),
				'confirm' => true,
				'img' => Theme :: get_common_image_path().'action_subscribe.png'
			);
			
			return DokeosUtilities :: build_toolbar($toolbar_data);
		}
		elseif ($this->browser->is_subscribed($course, $this->browser->get_user_id()))
		{
			return Translation :: get('AlreadySubscribed');
		}
		else
		{
			return Translation :: get('SubscriptionNotAllowed');
		}
		
	}
}
?>