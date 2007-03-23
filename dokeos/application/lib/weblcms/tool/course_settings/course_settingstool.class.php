<?php
/**
 * $Id$
 * Course settings tool
 * @package application.weblcms.tool
 * @subpackage course_settings
 */
require_once dirname(__FILE__).'/../tool.class.php';
require_once dirname(__FILE__).'/course_settingsform.class.php';
require_once dirname(__FILE__).'/../../weblcms.class.php';

class CourseSettingsTool extends Tool
{
	function run()
	{
		$form = new CourseSettingsForm($this);
		
		if($form->validate())
		{
			$success = $form->update_course();
			$this->redirect(Weblcms :: ACTION_VIEW_COURSE_HOME, get_lang('CourseSettingsUpdated'));
		}
		else
		{
			$this->display_header();
			$form->display();
			$this->display_footer();
		}
	}
}
?>