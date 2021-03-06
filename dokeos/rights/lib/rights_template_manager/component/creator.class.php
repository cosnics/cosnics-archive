<?php
/**
 * @package users.lib.usermanager.component
 */
require_once Path :: get_rights_path() . 'lib/rights_template_manager/rights_template_manager.class.php';
require_once Path :: get_rights_path() . 'lib/rights_template_manager/rights_template_manager_component.class.php';
require_once Path :: get_rights_path() . 'lib/forms/rights_template_form.class.php';
require_once Path :: get_rights_path() . 'lib/rights_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class RightsTemplateManagerCreatorComponent extends RightsTemplateManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES)), Translation :: get('RightsTemplates')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES)), Translation :: get('CreateRightsTemplate')));
		$trail->add_help('rights general');

		if (!$this->get_user()->is_platform_admin())
		{
			$this->not_allowed();
			exit;
		}
		$rights_template = new RightsTemplate();
		$rights_template->set_user_id($this->get_user_id());

		$form = new RightsTemplateForm(RightsTemplateForm :: TYPE_CREATE, $rights_template, $this->get_url());

		if($form->validate())
		{
			$success = $form->create_rights_template();
			$this->redirect(Translation :: get($success ? 'RightsTemplateCreated' : 'RightsTemplateNotCreated'), ($success ? false : true), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_BROWSE_RIGHTS_TEMPLATES));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>