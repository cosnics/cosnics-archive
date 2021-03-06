<?php
/**
 * @package application.lib.forum.forum_manager
 */
require_once dirname(__FILE__).'/forum_manager_component.class.php';
require_once dirname(__FILE__).'/../forum_data_manager.class.php';
require_once dirname(__FILE__).'/../../web_application.class.php';

/**
 * A forum manager
 * @author Sven Vanpoucke & Michael Kyndt
 */
 class ForumManager extends WebApplication
 {
 	const APPLICATION_NAME = 'forum';

 	const PARAM_ACTION = 'go';
	const PARAM_DELETE_SELECTED = 'delete_selected';
	const PARAM_FORUM_PUBLICATION = 'forum_publication';
    const PARAM_PUBLICATION_ID = 'pid';
    const PARAM_MOVE = 'move';

	const ACTION_DELETE = 'delete_forum_publication';
	const ACTION_EDIT = 'edit_forum_publication';
	const ACTION_CREATE = 'create_forum_publication';
    const ACTION_VIEW = 'view_forum_publications';
    const ACTION_PUBLISH = 'publish';
	const ACTION_BROWSE = 'browse';
    const ACTION_TOGGLE_VISIBILITY = 'toggle_visibility';
    const ACTION_MOVE = 'move';

	private $parameters;
	private $user;
    private $rights;

	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function ForumManager($user = null)
    {
    	$this->user = $user;
		$this->parameters = array ();
        $this->load_rights();
		$this->set_action(Request :: get(self :: PARAM_ACTION));
    }

    /**
	 * Run this forum manager
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_DELETE :
				$component = ForumManagerComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_CREATE :
				$component = ForumManagerComponent :: factory('Creator', $this);
				break;
			case self :: ACTION_BROWSE:
				$component = ForumManagerComponent :: factory('Browser', $this);
				break;
            case self :: ACTION_VIEW:
                $component = ForumManagerComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_EDIT:
                $component = ForumManagerComponent :: factory('Editor', $this);
                break;
            case self :: ACTION_MOVE:
                $component = ForumManagerComponent :: factory('Mover',$this);
                break;
            case self :: ACTION_TOGGLE_VISIBILITY :
                $component = ForumManagerComponent :: factory('ToggleVisibility',$this);
                break;
			default :
				$this->set_action(self :: ACTION_BROWSE);
				$component = ForumManagerComponent :: factory('Browser', $this);

		}
		$component->run();
	}

	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}

	// Data Retrieving

	function count_forum_publications($condition)
	{
		return ForumDataManager :: get_instance()->count_forum_publications($condition);
	}

	function retrieve_forum_publications($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return ForumDataManager :: get_instance()->retrieve_forum_publications($condition, $offset, $count, $order_property);
	}

 	function retrieve_forum_publication($id)
	{
		return ForumDataManager :: get_instance()->retrieve_forum_publication($id);
	}

	// Url Creation

	function get_create_forum_publication_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_FORUM_PUBLICATION));
	}

	function get_update_forum_publication_url($forum_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_FORUM_PUBLICATION,
								    self :: PARAM_FORUM_PUBLICATION => $forum_publication->get_id()));
	}

 	function get_delete_forum_publication_url($forum_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_FORUM_PUBLICATION,
								    self :: PARAM_FORUM_PUBLICATION => $forum_publication->get_id()));
	}

	function get_browse_forum_publications_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_FORUM_PUBLICATIONS));
	}

	function get_browse_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
	}

	// Dummy Methods which are needed because we don't work with learning objects
	function content_object_is_published($object_id)
	{
	}

	function any_content_object_is_published($object_ids)
	{
	}

	function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
	{
	}

	function get_content_object_publication_attribute($object_id)
	{

	}

	function count_publication_attributes($type = null, $condition = null)
	{

	}

	function delete_content_object_publications($object_id)
	{

	}

	function update_content_object_publication_id($publication_attr)
	{

	}

	function get_content_object_publication_locations($content_object)
	{

	}

	function publish_content_object($content_object, $location)
	{

	}

    function get_user_id()
    {
        return $this->user->get_id();
    }

    function is_allowed($right)
	{
		return $this->rights[$right];
	}

    function get_user()
    {
        return $this->user;
    }

    /**
	 * Load the rights for the current user in this tool
	 */
	private function load_rights()
	{
		$this->rights[VIEW_RIGHT] = true;
		$this->rights[EDIT_RIGHT] = true;
		$this->rights[ADD_RIGHT] = true;
		$this->rights[DELETE_RIGHT] = true;
//		$user = $this->user;
//		if ($user != null)
//		{
//			if($user->is_platform_admin())
//			{
//				$this->rights[EDIT_RIGHT] = true;
//				$this->rights[ADD_RIGHT] = true;
//				$this->rights[DELETE_RIGHT] = true;
//			}
//		}
		return;
	}
}
?>