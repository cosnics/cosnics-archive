<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/../userform.class.php';
require_once dirname(__FILE__).'/../../usersdatamanager.class.php';

class UserManagerDeleterComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$id = $_GET[UserManager :: PARAM_USER_USER_ID];
		if ($id)
		{
			$user = $this->retrieve_user($id);

			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header();
				Display :: display_error_message(get_lang("NotAllowed"));
				$this->display_footer();
				exit;
			}

			$success = $user->delete();
			$this->redirect('url', get_lang($success ? 'UserDeleted' : 'UserNotDeleted'), ($success ? false : true), array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));

		}
		else
		{
			$this->display_error_page(htmlentities(get_lang('NoObjectSelected')));
		}
	}
}
?>