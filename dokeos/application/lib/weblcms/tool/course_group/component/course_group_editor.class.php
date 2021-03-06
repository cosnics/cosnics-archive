<?php

require_once dirname(__FILE__) . '/../course_group_tool.class.php';
require_once dirname(__FILE__) . '/../course_group_tool_component.class.php';
require_once dirname(__FILE__).'/../../../course_group/course_group_form.class.php';

class CourseGroupToolEditorComponent extends CourseGroupToolComponent
{
	private $action_bar;

	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_UNSUBSCRIBE)), WebLcmsDataManager :: get_instance()->retrieve_course_group(Request :: get(CourseGroupTool :: PARAM_COURSE_GROUP))->get_name()));
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_EDIT_COURSE_GROUP)), Translation :: get('Edit')));
        $trail->add_help('courses group');

		$course_group_id = Request :: get(CourseGroupTool :: PARAM_COURSE_GROUP);
		$wdm = WeblcmsDataManager :: get_instance();
		$course_group = $wdm->retrieve_course_group($course_group_id);

		$form = new CourseGroupForm(CourseGroupForm :: TYPE_EDIT, $course_group, $this->get_url(array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_EDIT_COURSE_GROUP, CourseGroupTool :: PARAM_COURSE_GROUP => $course_group_id)));
		if ($form->validate())
		{
			$succes = $form->update_course_group();
			$message = $succes ? 'CourseGroupUpdated' : 'CourseGroupNotUpdated'; 
			
			$this->redirect(Translation :: get($message), !$succes, array(Tool :: PARAM_ACTION => CourseGroupTool :: ACTION_VIEW_GROUPS));
		}
		else
		{
			$this->display_header($trail, true);
			$form->display();
			$this->display_footer();
		}

	}

}
?>