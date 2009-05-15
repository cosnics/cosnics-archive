<?php
/**
 * $Id: editor.class.php 11337 2007-03-02 13:29:08Z Scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../home_manager.class.php';
require_once dirname(__FILE__).'/../home_manager_component.class.php';
require_once dirname(__FILE__).'/../../home_block_form.class.php';
require_once dirname(__FILE__).'/../../home_row_form.class.php';
require_once dirname(__FILE__).'/../../home_column_form.class.php';
/**
 * Repository manager component to edit an existing learning object.
 */
class HomeManagerCreatorComponent extends HomeManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		global $this_section;
		$this_section='platform_admin';
		$type = $_GET[HomeManager :: PARAM_HOME_TYPE];
		$trail = new BreadcrumbTrail();
		$admin = new AdminManager();
        $trail->add(new Breadcrumb($admin->get_link(array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('Home')));
        $trail->add(new Breadcrumb($this->get_url(array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('HomeManager')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('HomeCreator')));
		
		$user = $this->get_user();		
		$user_home_allowed = $this->get_platform_setting('allow_user_home');
		
		if ($user_home_allowed && Authentication :: is_valid())
		{
			$user_id = $user->get_id();
		}
		else
		{
			if (!$user->is_platform_admin())
			{
				$this->display_header($trail);
				Display :: error_message(Translation :: get('NotAllowed'));
				$this->display_footer();
				exit;
			}
			
			$user_id = '0';
		}
		
		if ($type)
		{
			$url = $this->get_url(array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_CREATE_HOME, HomeManager :: PARAM_HOME_TYPE => $type));
			switch($type)
			{
				case HomeManager :: TYPE_BLOCK :
					$object = new HomeBlock();
					$object->set_user($user_id);
					$form = new HomeBlockForm(HomeBlockForm :: TYPE_CREATE, $object, $url);
					break;
				case HomeManager :: TYPE_COLUMN :
					$object = new HomeColumn();
					$object->set_user($user_id);
					$form = new HomeColumnForm(HomeColumnForm :: TYPE_CREATE, $object, $url);
					break;
				case HomeManager :: TYPE_ROW :
					$object = new HomeRow();
					$object->set_user($user_id);
					$form = new HomeRowForm(HomeRowForm :: TYPE_CREATE, $object, $url);
					break;
			}
			
			if ($object->get_user() == $user_id || ($object->get_user() == '0' && $user->is_platform_admin()))
			{
				if ($form->validate())
				{
					$success = $form->create_object();
					$this->redirect('url', Translation :: get($success ? 'HomeCreated' : 'HomeNotCreated'), ($success ? false : true), array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME));
				}
				else
				{
					//$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => $object->get_title());
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
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>