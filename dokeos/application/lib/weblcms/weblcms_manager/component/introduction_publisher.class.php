<?php

require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
require_once dirname(__FILE__).'/../../content_object_repo_viewer.class.php';

class WeblcmsManagerIntroductionPublisherComponent extends WeblcmsManagerComponent
{
	function run()
	{
		/*if(!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}*/

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
		$trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_PUBLISH_INTRODUCTION)), Translation :: get('PublishIntroduction')));
		
		$trail->add_help('courses general');
		/*$pub = new ContentObjectPublisher($this, 'introduction', true);

		$html[] = '<p><a href="' . $this->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE)) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
		$html[] =  $pub->as_html();*/

		$object = Request :: get('object');
		$pub = new ContentObjectRepoViewer($this, 'introduction', true);

		if(!isset($object))
		{
			$html[] = '<div class="clear">&nbsp;</div><p><a href="' . $this->get_url(array('go' => 'course_viewer'), array(), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			$dm = WeblcmsDataManager :: get_instance();
			$do = $dm->get_next_content_object_publication_display_order_index($this->get_course_id(),$this->get_tool_id(),0);

			$pub = new ContentObjectPublication();
			$pub->set_content_object_id($object);
			$pub->set_course_id($this->get_course_id());
			$pub->set_tool('introduction'); 
			$pub->set_publisher_id(Session :: get_user_id());
			$pub->set_publication_date(time());
			$pub->set_modified_date(time());
			$pub->set_hidden(false);
			$pub->set_display_order_index($do);
			$pub->create();

			$parameters = $this->get_parameters();
			$parameters['go'] = WeblcmsManager :: ACTION_VIEW_COURSE;

			$this->redirect(Translation :: get('IntroductionPublished'), (false), $parameters);
		}

		$this->display_header($trail, false, true);
		echo '<div class="clear"></div><br />';
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>