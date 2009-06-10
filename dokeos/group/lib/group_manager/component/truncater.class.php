<?php
/**
 * @package group.group_manager.component
 */
require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class GroupManagerTruncaterComponent extends GroupManagerComponent
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
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('EmptyGroup')));
			$trail->add_help('group general');

			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}


		$ids = Request :: get(GroupManager :: PARAM_GROUP_ID);
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$group = $this->retrieve_group($id);
				if (!$group->truncate())
				{
					$failures++;
				}
				else
				{
					Events :: trigger_event('empty', 'group', array('target_group_id' => $group->get_id(), 'action_user_id' => $user->get_id()));
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedGroupNotEmptied';
				}
				else
				{
					$message = 'SelectedGroupsNotEmptied';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedGroupEmptied';
				}
				else
				{
					$message = 'SelectedGroupsEmptied';
				}

			}

			if(count($ids) == 1)
				$this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $ids[0]));
			else
				$this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoGroupSelected')));
		}
	}
}
?>