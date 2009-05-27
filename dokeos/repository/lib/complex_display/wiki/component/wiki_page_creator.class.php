<?php

/*
 * This is the compenent that allows the user to create a wiki_page.
 *
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once Path :: get_application_path() . 'lib/weblcms/learning_object_repo_viewer.class.php';
require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/publisher/learning_object_publisher.class.php';
require_once Path::get_repository_path().'/lib/complex_learning_object_item.class.php';
require_once Path::get_repository_path().'lib/complex_builder/complex_repo_viewer.class.php';

class WikiDisplayWikiPageCreatorComponent extends WikiDisplayComponent
{
    private $pub;
    private $publication_id;

	function run()
	{
		$trail = new BreadcrumbTrail();
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))), DokeosUtilities::truncate_string($_SESSION['wiki_title'],20)));
        $trail->add_help('courses wiki tool');

        //if(!WikiTool ::is_wiki_locked(Request :: get('wiki_id')))
        //{
            $object = Request :: get('object'); //the object that was made, needed to set the reference for the complex object

            $this->pub = new LearningObjectRepoViewer($this->get_parent()->get_parent(), 'wiki_page', true, RepoViewer :: SELECT_MULTIPLE, WikiTool :: ACTION_CREATE_PAGE);

            $this->publication_id = Request :: get('pid');
            if(!empty($this->publication_id))
            {
                $_SESSION['wiki_id'] = WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication($this->publication_id)->get_learning_object()->get_id();
                $wm = WeblcmsDataManager :: get_instance();
                $publication = $wm->retrieve_learning_object_publication($this->publication_id);
            }

            if(empty($object))
            {
                $html[] = '<p><a href="' . $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS), array(), true) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
                $html[] =  $this->pub->as_html();
                $this->display_header($trail, true);
                echo implode("\n",$html);

            }
            else
            {
                $o = RepositoryDataManager :: get_instance()->retrieve_learning_object($object);
                $count = RepositoryDataManager ::get_instance()->count_learning_objects('wiki_page', new EqualityCondition(LearningObject :: PROPERTY_TITLE,$o->get_title()));
                if($count==1)
                {
                    $cloi = ComplexLearningObjectItem ::factory('wiki_page');
                    $cloi->set_ref($object);
                    $cloi->set_parent($_SESSION['wiki_id']);
                    $cloi->set_user_id($this->pub->get_user_id());
                    $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($_SESSION['wiki_id']));
                    $cloi->set_additional_properties(array('is_homepage' => 0));
                    $cloi->create();
                    $pid = WebLcmsDataManager :: get_instance()->retrieve_learning_object_publications(null,null,null,null, new EqualityCondition(LearningObjectPublication ::PROPERTY_LEARNING_OBJECT_ID,$cloi->get_parent()))->next_result()->get_id();
                    $this->redirect($message, '', array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI_PAGE, Tool :: PARAM_COMPLEX_ID => $cloi->get_id(), 'pid' => $pid));
                }
                else
                {
                    $this->display_header($trail, true);
                    $this->display_error_message(Translation :: get('WikiPageTitleError'));
                    $this->display_footer();
                }
            }
        /*}
        else
        {
            $this->redirect(htmlentities(Translation :: get('WikiIsLocked')), '', $params);
        }*/
        $this->display_footer();
    }
}
?>