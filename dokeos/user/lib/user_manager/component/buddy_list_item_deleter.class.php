<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';

class UserManagerBuddyListItemDeleterComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = Request :: get(UserManager :: PARAM_BUDDYLIST_ITEM);
		$failures = 0;
		
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			foreach ($ids as $id)
			{
				$buddy = new BuddyListItem();
				$buddy->set_user_id($this->get_user_id());
				$buddy->set_buddy_id($id);
				
				if(!$buddy->delete())
				{
					$failures++;
				}
			}
			
			if (!$failures)
			{
				if (count($ids) == 1)
				{
					$message = 'BuddyListItemsDeleted';
				}
				else
				{
					$message = 'BuddyListItemDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'BuddyListItemsNotDeleted';
				}
				else
				{
					$message = 'BuddyListItemNotDeleted';
				}
				echo $message;
			}
			
			$ajax = Request :: get('ajax');
			if(!$ajax)
				$this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_BUDDYLIST));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoBuddyListItemSelected')));
		}
	}
}
?>