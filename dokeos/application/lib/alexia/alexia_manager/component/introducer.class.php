<?php
/**
 * @package alexia
 * @subpackage alexia_manager
 * @subpackage component
 *
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__) . '/../alexia_manager.class.php';
require_once dirname(__FILE__) . '/../alexia_manager_component.class.php';
require_once dirname(__FILE__) . '/../../forms/alexia_publication_form.class.php';
require_once Path :: get_application_library_path() . 'repo_viewer/repo_viewer.class.php';

class AlexiaManagerIntroducerComponent extends AlexiaManagerComponent
{
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_BROWSE_PUBLICATIONS)), Translation :: get('Alexia')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishIntroductionText')));
		$trail->add_help('alexia general');

		$object = Request :: get('object');

		$repo_viewer = new RepoViewer($this, 'introduction', true);
		$repo_viewer->set_parameter(AlexiaManager :: PARAM_ACTION, AlexiaManager :: ACTION_PUBLISH_INTRODUCTION);

		if(!isset($object))
		{
			$html = array();
			$html[] = '<p><a href="' . $this->get_url() . '"><img src="' . Theme :: get_common_image_path() . 'action_browser.png" alt="' . Translation :: get('BrowserTitle') . '" style="vertical-align:middle;"/> ' . Translation :: get('BrowserTitle') . '</a></p>';
			$html[] =  $repo_viewer->as_html();
			
			$this->display_header($trail, true);
			echo implode("\n", $html);
			$this->display_footer();
		}
		else
		{			
			$publication = new AlexiaPublication();
			$publication->set_content_object($object);
			$publication->set_target_users(array());
			$publication->set_target_groups(array());
			$publication->set_from_date(0);
			$publication->set_to_date(0);
			$publication->set_publisher(Session :: get_user_id());
			$publication->set_published(time());
			$publication->set_hidden(0);
			
			if ($publication->create())
			{
				$this->redirect(Translation :: get('IntroductionPublished'), false, array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_BROWSE_PUBLICATIONS));
			}
			else
			{
				$this->display_header($trail, true);
				$this->display_error_message(Translation :: get('IntroductionNotPublished'));
				$this->display_footer();
			}
		}
	}
}
?>