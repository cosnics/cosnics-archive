<?php
/**
 * @package application.forum.forum.component
 */
require_once dirname(__FILE__).'/../forum_manager.class.php';
require_once dirname(__FILE__).'/../forum_manager_component.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/complex_display.class.php';

/**
 * Component to view a new forum_publication object
 * @author Michael Kyndt
 */
class ForumManagerViewerComponent extends ForumManagerComponent
{
    /**
     * Runs this component and displays its output.
     */
    public $trail;
    function run()
    {
        $this->trail = new BreadcrumbTrail();
        $this->trail->add(new Breadcrumb($this->get_url(), Translation :: get('forum')));
        $this->trail->add(new Breadcrumb($this->get_url(), Translation :: get('browse')));

        $pid = Request :: get('pid');
        $cid = Request :: get('cid');
        
        $cd = ComplexDisplay :: factory($this,'forum');
        $cd->run();

        switch($cd->get_action())
        {
            case ForumDisplay :: ACTION_VIEW_TOPIC:
                Events :: trigger_event('view_forum_topic', 'weblcms', array('user_id' => $this->get_user_id(), 'publication_id' => $pid,
								'forum_topic_id' => $cid));
                break;
        }
    }

	function get_url($parameters = array (), $filter = array(), $encode_entities = false)
	{
        $parameters[ForumManager :: PARAM_ACTION] = ForumManager :: ACTION_VIEW;
		return $this->get_parent()->get_url($parameters, $filter, $encode_entities);
	}

    function redirect($message = null, $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false)
	{
        $parameters[ForumManager :: PARAM_ACTION] = ForumManager :: ACTION_VIEW;
		$this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities);
	}
}
?>