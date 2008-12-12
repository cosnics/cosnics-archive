<?php
/**
 * $Id: course_settings_tool.class.php 15449 2008-05-27 11:10:16Z Scara84 $
 * Course settings tool
 * @package application.weblcms.tool
 * @subpackage course_settings
 */
require_once dirname(__FILE__).'/../course_settings_tool_component.class.php';
require_once dirname(__FILE__).'/../../../course/course_form.class.php';

class CourseSettingsToolUpdaterComponent extends CourseSettingsToolComponent
{
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		if (!$this->get_course()->is_course_admin($this->get_parent()->get_user()))
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$form = new CourseForm(CourseForm :: TYPE_EDIT, $this->get_course(), $this->get_user(), $this->get_url(array(Tool :: PARAM_ACTION => CourseSettingsTool :: ACTION_UPDATE_COURSE_SETTINGS)));
		
		if($form->validate())
		{
			$success = $form->update_course();
			$this->redirect(Weblcms :: ACTION_VIEW_WEBLCMS_HOME, Translation :: get($success ? 'CourseSettingsUpdated' : 'CourseSettingsUpdateFailed'), ($success ? false : true));
		}
		else
		{			
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>