<?php
/**
 * @package group.group_manager.component
 */
require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class GroupManagerSubscriberComponent extends GroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$user = $this->get_user();
		$group_id = Request :: get(GroupManager :: PARAM_GROUP_ID);
		if (!$user->is_platform_admin())
		{
			$trail = new BreadcrumbTrail();
			$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
			$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupList')));
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('SubscribeToGroup')));
			$trail->add_help('group general');

			$this->display_header($trail);
			Display :: error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}

		$users = Request :: get(GroupManager :: PARAM_USER_ID);

		$failures = 0;

		if (!empty ($users))
		{
			if (!is_array($users))
			{
				$users = array ($users);
			}

			foreach($users as $user)
			{
				$existing_groupreluser = $this->retrieve_group_rel_user($user, $group_id);

				if (!is_null($existing_groupreluser))
				{
					$groupreluser = new GroupRelUser();
					$groupreluser->set_group_id($group_id);
					$groupreluser->set_user_id($user);

					if (!$groupreluser->create())
					{
						$failures++;
					}
					else
					{
						Events :: trigger_event('subscribe_user', 'group', array('target_group_id' => $groupreluser->get_group_id(), 'target_user_id' => $groupreluser->get_user_id(), 'action_user_id' => $this->get_user()->get_id()));
					}
				}
				else
				{
					$contains_dupes = true;
				}
			}

			if ($failures)
			{
				if (count($users) == 1)
				{
					$message = 'SelectedUserNotAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
				}
				else
				{
					$message = 'SelectedUsersNotAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
				}
			}
			else
			{
				if (count($users) == 1)
				{
					$message = 'SelectedUserAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
				}
				else
				{
						$message = 'SelectedUsersAddedToGroup' . ($contains_dupes ? 'Dupes' : '');
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $group_id));
			exit;
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoGroupRelUserSelected')));
		}
	}
}
?>