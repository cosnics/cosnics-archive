<?php
/**
 * @package group.group_manager.component
 */
require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class GroupManagerUnsubscriberComponent extends GroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$user = $this->get_user();

		if (!$user->is_platform_admin())
		{
			$trail = new BreadcrumbTrail();
			$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
			$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupList')));
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UnsubscribeFromGroup')));
			$trail->add_help('group unsubscribe users');

			$this->display_header($trail);
			Display :: error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}

		$ids = Request :: get(GroupManager :: PARAM_GROUP_REL_USER_ID);
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$groupreluser_ids = explode('|', $id);
				$groupreluser = $this->retrieve_group_rel_user($groupreluser_ids[1], $groupreluser_ids[0]);

				if(!isset($groupreluser)) continue;

				if ($groupreluser_ids[0] == $groupreluser->get_group_id())
				{
					if (!$groupreluser->delete())
					{
						$failures++;
					}
					else
					{
						Events :: trigger_event('unsubscribe_user', 'group', array('target_group_id' => $groupreluser->get_group_id(), 'target_user_id' => $groupreluser->get_user_id(), 'action_user_id' => $user->get_id()));
					}
				}
				else
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedGroupRelUserNotDeleted';
				}
				else
				{
					$message = 'SelectedGroupRelUsersNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedGroupRelUserDeleted';
				}
				else
				{
					$message = 'SelectedGroupRelUsersDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $groupreluser_ids[0]));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoGroupRelUserSelected')));
		}
	}
}
?>