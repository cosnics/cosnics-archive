<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/user_form.class.php';
require_once dirname(__FILE__).'/../../user_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class UserManagerUpdaterComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{

		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserList')));
		$trail->add_help('user general');

		$id = Request :: get(UserManager :: PARAM_USER_USER_ID);
		if ($id)
		{
			$user = $this->retrieve_user($id);

            $trail->add(new Breadcrumb($this->get_url(), $user->get_fullname()));

			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header($trail);
				Display :: error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}

			$form = new Userform(UserForm :: TYPE_EDIT, $user, $this->get_user(), $this->get_url(array(UserManager :: PARAM_USER_USER_ID => $id)));

			if($form->validate())
			{
				$success = $form->update_user();
				$this->redirect(Translation :: get($success ? 'UserUpdated' : 'UserNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
			}
			else
			{
                $trail->add(new Breadcrumb($this->get_url(array(UserManager::PARAM_USER_USER_ID => $id)), Translation :: get('Update')));

				$this->display_header($trail);
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>