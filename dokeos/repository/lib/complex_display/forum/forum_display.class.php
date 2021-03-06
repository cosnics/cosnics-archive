<?php
/**
 * @author Michael Kyndt
 */

require_once dirname(__FILE__) . '/../complex_display.class.php';
require_once dirname(__FILE__) . '/forum_display_component.class.php';

class ForumDisplay extends ComplexDisplay
{
	const ACTION_VIEW_FORUM = 'view_forum';
	const ACTION_VIEW_TOPIC = 'view_topic';
	const ACTION_PUBLISH_FORUM = 'publish';

	const ACTION_CREATE_FORUM_POST = 'add_post';
	const ACTION_EDIT_FORUM_POST = 'edit_post';
	const ACTION_DELETE_FORUM_POST = 'delete_post';
	const ACTION_QUOTE_FORUM_POST = 'quote_post';

	const ACTION_CREATE_TOPIC = 'create_topic';
	const ACTION_DELETE_TOPIC = 'delete_topic';

	const ACTION_CREATE_SUBFORUM = 'create_subforum';
	const ACTION_EDIT_SUBFORUM = 'edit_subforum';
	const ACTION_DELETE_SUBFORUM = 'delete_subforum';
	const ACTION_MOVE_SUBFORUM = 'move_subforum';

	function run()
	{
		$action = $this->get_action();

		switch ($action)
		{
			case self :: ACTION_PUBLISH_FORUM :
				$component = ForumDisplayComponent :: factory('Publisher', $this);
				break;
			case self :: ACTION_VIEW_FORUM :
				$component = ForumDisplayComponent :: factory('ForumViewer', $this);
				break;
			case self :: ACTION_VIEW_TOPIC :
				$component = ForumDisplayComponent :: factory('TopicViewer', $this);
				break;
			case self :: ACTION_CREATE_FORUM_POST :
				$component = ForumDisplayComponent :: factory('ForumPostCreator', $this);
				break;
			case self :: ACTION_EDIT_FORUM_POST :
				$component = ForumDisplayComponent :: factory('ForumPostEditor', $this);
				break;
			case self :: ACTION_DELETE_FORUM_POST :
				$component = ForumDisplayComponent :: factory('ForumPostDeleter', $this);
				break;
			case self :: ACTION_QUOTE_FORUM_POST :
				$component = ForumDisplayComponent :: factory('ForumPostQuoter', $this);
				break;
			case self :: ACTION_CREATE_TOPIC :
				$component = ForumDisplayComponent :: factory('ForumTopicCreator', $this);
				break;
			case self :: ACTION_DELETE_TOPIC :
				$component = ForumDisplayComponent :: factory('ForumTopicDeleter', $this);
				break;
			case self :: ACTION_MOVE_SUBFORUM :
				$component = ForumDisplayComponent :: factory('ForumSubforumMover', $this);
				break;
			case self :: ACTION_CREATE_SUBFORUM :
				$component = ForumDisplayComponent :: factory('ForumSubforumCreator', $this);
				break;
			case self :: ACTION_EDIT_SUBFORUM :
				$component = ForumDisplayComponent :: factory('ForumSubforumEditor', $this);
				break;
			case self :: ACTION_DELETE_SUBFORUM :
				$component = ForumDisplayComponent :: factory('ForumSubforumDeleter', $this);
				break;
			default :
				$this->set_action(self :: ACTION_VIEW_CLO);
				$component = ForumDisplayComponent :: factory('ForumViewer', $this);
		}
        
		if(!$component)
			parent :: run();
		else
			$component->run();
	}
}

?>