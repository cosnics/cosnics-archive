<?php

require_once dirname(__FILE__).'/../course_sections_tool.class.php';
require_once dirname(__FILE__).'/../course_sections_tool_component.class.php';
require_once dirname(__FILE__).'/../course_section_form.class.php';

class CourseSectionsToolCreatorComponent extends CourseSectionsToolComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add_help('courses sections');
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => CourseSectionsTool :: ACTION_CREATE_COURSE_SECTION)), Translation :: get('Create')));

		if (!$this->get_course()->is_course_admin($this->get_parent()->get_user()))
		{
			$this->display_header($trail, true);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}

		$course_section = new CourseSection();
		$course_section->set_course_code($this->get_course_id());
		$course_section->set_type(CourseSection :: TYPE_TOOL);

		$form = new CourseSectionForm(CourseSectionForm :: TYPE_CREATE, $course_section, $this->get_url(array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_CREATE_COURSE_SECTION)));

		if($form->validate())
		{
			$success = $form->create_course_section();
			if($success)
			{
				$course_section = $form->get_course_section();
				$this->redirect(Translation :: get('CourseSectionCreated'), (false), array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_VIEW_COURSE_SECTIONS));
			}
			else
			{
				$this->redirect(Translation :: get('CourseSectionNotCreated'), (true), array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_VIEW_COURSE_SECTIONS));
			}
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