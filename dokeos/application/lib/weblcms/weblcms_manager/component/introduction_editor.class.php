<?php

require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/../../content_object_publication_form.class.php';
require_once Path :: get_repository_path() . 'lib/content_object_form.class.php';

class WeblcmsManagerIntroductionEditorComponent extends WeblcmsManagerComponent
{
	function run()
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'introduction');
		$condition = new AndCondition($conditions);

		$publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
		$introduction_text = $publications->next_result();

		$lo = $introduction_text->get_content_object();
		$form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $lo, 'edit', 'post', $this->get_url(array('edit_introduction' => Request :: get('edit_introduction'))));

		if( $form->validate())
		{
			$form->update_content_object();
			if($form->is_version())
			{
				$introduction_text->set_content_object($lo->get_latest_version());
				$introduction_text->update();
			}
			$this->redirect(Translation :: get('IntroductionEdited'), '', array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE));
		}
		else
		{
			$trail = new BreadcrumbTrail();
			
			switch($this->get_course()->get_breadcrumb())
		{
			case Course :: BREADCRUMB_TITLE : $title = $this->get_course()->get_name(); break;
			case Course :: BREADCRUMB_CODE : $title = $this->get_course()->get_visual(); break;
			case Course :: BREADCRUMB_COURSE_HOME : $title = Translation :: get('CourseHome'); break;
			default: $title = $this->get_course()->get_visual(); break;
		}
		
		$trail->add(new Breadcrumb($this->get_url(array('go' => null, 'course' => null)), Translation :: get('MyCourses')));
		$trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE)), $title));
		$trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_PUBLISH_INTRODUCTION)), Translation :: get('EditIntroduction')));
			
			$trail->add_help('courses general');

			$this->display_header($trail, false, true);
			echo '<div class="clear"></div><br />';
			$form->display();
			$this->display_footer();
			exit();
		}
	}
}
?>