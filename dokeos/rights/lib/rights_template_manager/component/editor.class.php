<?php
require_once Path :: get_rights_path() . 'lib/rights_template_manager/rights_template_manager.class.php';
require_once Path :: get_rights_path() . 'lib/rights_template_manager/rights_template_manager_component.class.php';
require_once Path :: get_rights_path() . 'lib/rights_template_manager/component/rights_template_browser_table/rights_template_browser_table.class.php';
require_once Path :: get_rights_path() . 'lib/forms/rights_template_form.class.php';
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class RightsTemplateManagerEditorComponent extends RightsTemplateManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES)), Translation :: get('RightsTemplates')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES)), Translation :: get('EditRightsTemplate')));
		$trail->add_help('rights general');

		$id = Request :: get(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID);

		if ($id)
		{
			$rights_template = $this->retrieve_rights_template($id);

			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header($trail);
				Display :: error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}

			$form = new RightsTemplateForm(RightsTemplateForm :: TYPE_EDIT, $rights_template, $this->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID => $id)));

			if($form->validate())
			{
				$success = $form->update_rights_template();
				$this->redirect(Translation :: get($success ? 'RightsTemplateUpdated' : 'RightsTemplateNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_BROWSE_RIGHTS_TEMPLATES));
			}
			else
			{
				$this->display_header($trail);
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoRightsTemplateSelected')));
		}
	}
}
?>