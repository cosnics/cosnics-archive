<?php
/**
 */
require_once dirname(__FILE__).'/../course_sections_tool.class.php';
require_once dirname(__FILE__).'/../course_sections_tool_component.class.php';

class CourseSectionsToolVisibilityChangerComponent extends CourseSectionsToolComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$user = $this->get_user();

		if (!$user->is_platform_admin())
		{
			$trail = new BreadcrumbTrail();
			$trail->add_help('courses sections');
			$this->display_header($trail, true);
			Display :: error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}

		$ids = Request :: get(CourseSectionsTool :: PARAM_COURSE_SECTION_ID);
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$course_section = WeblcmsDataManager :: get_instance()->retrieve_course_sections(new EqualityCondition('id', $id))->next_result();
				$course_section->set_visible(!$course_section->get_visible());

				if (!$course_section->update())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedCourseSectionVisibilityChanged';
				}
				else
				{
					$message = 'SelectedCourseSectionVisibilityChanged';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedCourseSectionsVisibilityChanged';
				}
				else
				{
					$message = 'SelectedCourseSectionsVisibilityChanged';
				}
			}

			$this->redirect(Translation :: get($message), ($failures != 0 ? true : false), array(CourseSectionsTool :: PARAM_ACTION => CourseSectionsTool :: ACTION_VIEW_COURSE_SECTIONS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoCourseSectionsSelected')));
		}
	}
}
?>