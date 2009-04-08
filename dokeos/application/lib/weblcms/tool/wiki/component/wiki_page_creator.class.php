<?php
require_once dirname(__FILE__).'/../../../learning_object_repo_viewer.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/learning_object_publisher.class.php';
require_once Path::get_repository_path().'/lib/complex_learning_object_item.class.php';
require_once Path::get_repository_path().'lib/complex_builder/complex_repo_viewer.class.php';

class WikiToolPageCreatorComponent extends WikiToolComponent
{
    private $pub;
	function run()
	{
        
		if (!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
		$object = Request :: get('object');

		$this->pub = new LearningObjectRepoViewer($this, 'wiki_page', true, RepoViewer :: SELECT_MULTIPLE, WikiTool ::ACTION_CREATE_PAGE);
        $this->pub->set_parameter('object_id', $_GET['object_id']);

		if(!isset($object))
		{
           
			$html[] = '<p><a href="' . $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $this->pub->as_html();
            $this->display_header($trail);
            echo implode("\n",$html);
        }
		else
		{
            
            $cloi = ComplexLearningObjectItem ::factory('wiki_page');
            $cloi->set_ref($object);
            $cloi->set_parent(Request :: get('object_id'));
            $cloi->set_user_id($this->pub->get_user_id());
            $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order(Request :: get('object_id')));
            $cloi->set_additional_properties(array('is_homepage' => 0));
            $cloi->create();
            $this->redirect(null, $message, '', array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI_PAGE, WikiTool ::PARAM_OBJECT_ID => $cloi->get_ref()));
        }
        $this->display_footer();
    }
}
?>