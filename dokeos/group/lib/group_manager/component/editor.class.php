<?php
/**
 * @package group.group_manager.component
 */
require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/group_form.class.php';
require_once dirname(__FILE__).'/../../group_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class GroupManagerEditorComponent extends GroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add_help('group general');
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupList')));

		$id = Request :: get(GroupManager :: PARAM_GROUP_ID);
		if ($id)
		{
			$group = $this->retrieve_group($id);
			$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => Request :: get(GroupManager :: PARAM_GROUP_ID))), $group->get_name()));
			$trail->add(new Breadcrumb($this->get_url(array(GroupManager :: PARAM_GROUP_ID => $id)), Translation :: get('GroupUpdate')));

			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header($trail, false);
				Display :: error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}

			$form = new GroupForm(GroupForm :: TYPE_EDIT, $group, $this->get_url(array(GroupManager :: PARAM_GROUP_ID => $id)), $this->get_user());

			if($form->validate())
			{
				$success = $form->update_group();
				$group = $form->get_group();
				$this->redirect(Translation :: get($success ? 'GroupUpdated' : 'GroupNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $group->get_id()));
			}
			else
			{
				$this->display_header($trail, false);
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoGroupSelected')));
		}
	}
}
?>