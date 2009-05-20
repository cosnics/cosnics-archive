<?php
/**
 * $Id: editor.class.php 11337 2007-03-02 13:29:08Z Scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../home_manager.class.php';
require_once dirname(__FILE__).'/../home_manager_component.class.php';
/**
 * Repository manager component to edit an existing learning object.
 */
class HomeManagerMoverComponent extends HomeManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		global $this_section;
		$this_section='platform_admin';

		$id = $_GET[HomeManager :: PARAM_HOME_ID];
		$type = $_GET[HomeManager :: PARAM_HOME_TYPE];
		$direction = $_GET[HomeManager :: PARAM_DIRECTION];
		$trail = new BreadcrumbTrail();
		$admin = new AdminManager();
        $trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('Home')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('HomeManager')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('HomeMover')));

		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}

		if ($id && $type)
		{
			$url = $this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_EDIT_HOME, HomeManager :: PARAM_HOME_TYPE => $type, HomeManager :: PARAM_HOME_ID => $id));
			switch($type)
			{
				case HomeManager :: TYPE_BLOCK :
					$move_home = $this->retrieve_home_block($id);
					$sort = $move_home->get_sort();
					$next_home = $this->retrieve_home_block_at_sort($move_home->get_column(), $sort, $direction);
					break;
				case HomeManager :: TYPE_COLUMN :
					$move_home = $this->retrieve_home_column($id);
					$sort = $move_home->get_sort();
					$next_home = $this->retrieve_home_column_at_sort($move_home->get_row(), $sort, $direction);
					break;
				case HomeManager :: TYPE_ROW :
					$move_home = $this->retrieve_home_row($id);
					$sort = $move_home->get_sort();
					$next_home = $this->retrieve_home_row_at_sort($sort, $direction);
					break;
			}

			if ($direction == 'up')
			{
				$move_home->set_sort($sort-1);
				$next_home->set_sort($sort);
			}
			elseif($direction == 'down')
			{
				$move_home->set_sort($sort+1);
				$next_home->set_sort($sort);
			}

			if ($move_home->update() && $next_home->update())
			{
				$success = true;
			}
			else
			{
				$success = false;
			}

			$this->redirect(Translation :: get($success ? 'HomeMoved' : 'HomeNotMoved'), ($success ? false : true), array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>