<?php

require_once dirname(__FILE__) . '/../tool.class.php';
require_once dirname(__FILE__) . '/../tool_component.class.php';
require_once dirname(__FILE__).'/../../content_object_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/feedback/feedback.class.php';

class ToolFeedbackPublisherComponent extends ToolComponent
{
	function run()
	{
		if(!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();

        if(Request :: get('pcattree') > 0)
        {
            foreach(Tool ::get_pcattree_parents(Request :: get('pcattree')) as $breadcrumb)
            {
                $trail->add(new BreadCrumb($this->get_url(), $breadcrumb->get_name()));
            }
        }
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), Tool :: PARAM_ACTION => 'view')), WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get('pid'))->get_content_object()->get_title()));
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_FEEDBACK)), Translation :: get('AddFeedback')));
        $trail->add_help('courses general');

        $object = Request :: get('object');
		$pub = new ContentObjectRepoViewer($this, 'feedback', true);
		$pub->set_parameter(Tool :: PARAM_ACTION, Tool :: ACTION_PUBLISH_FEEDBACK);
		if(Request :: get('pid')!=null)
            $pub->set_parameter('pid', Request :: get('pid'));

		if(Request :: get('cid')!=null)
			$pub->set_parameter('cid', Request :: get('cid'));


		if(!isset($object))
		{
			$html[] = '<p><a href="' . $this->get_url() . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
			$this->display_header($trail, true);
			echo implode("\n",$html);
			$this->display_footer();
		}
		else
		{
			$feedback = new Feedback();
			$feedback->set_id($object);
			$id = Request :: get('cid')!=null?Request :: get('cid'):Request :: get('pid');
			$pid = Request :: get('pid');

			$publication_feedback= new ContentObjectPublicationFeedback(null, $feedback, $this->get_course_id(), $this->get_tool_id().'_feedback', $id,$this->get_user_id(), time(), 0, 0);
			$publication_feedback->set_show_on_homepage(0);
			$publication_feedback->create();
			$this->redirect(Translation :: get('FeedbackAdded'), '', array(Tool :: PARAM_ACTION => Request :: get('cid')!=null?'view_item':'view', Request :: get('cid')!=null?'cid':'pid' => $id, 'pid' => $pid));
		}

	}
}
?>