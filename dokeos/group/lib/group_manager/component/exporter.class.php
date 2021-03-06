<?php
/**
 * @package group.group_manager.component
 */
require_once dirname(__FILE__).'/../group_manager.class.php';
require_once dirname(__FILE__).'/../group_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/group_export_form.class.php';
require_once Path :: get_library_path().'export/export.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class GroupManagerExporterComponent extends GroupManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('GroupCreateExport')));
		$trail->add_help('group export');

		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail, false);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}

		$form = new GroupExportForm(GroupExportForm :: TYPE_EXPORT, $this->get_url());

		if($form->validate())
		{
			$export = $form->exportValues();
			$file_type = $export['file_type'];
			$data['groups'] = $this->build_group_tree(0);
			$this->export_groups($file_type,$data);
		}
		else
		{
			$this->display_header($trail, false);
			$form->display();
			$this->display_footer();
		}
	}

	function build_group_tree($parent_group)
	{
		$condition = new EqualityCondition(Group :: PROPERTY_PARENT, $parent_group);
		$result = $this->retrieve_groups($condition);
		while($group = $result->next_result())
     	{
     		$group_array[Group::PROPERTY_NAME] = htmlspecialchars($group->get_name());
     		$group_array[Group::PROPERTY_DESCRIPTION] = htmlspecialchars($group->get_description());
     		$group_array['children'] = $this->build_group_tree($group->get_id());
     		$data[] = $group_array;
 	    }

 	    return $data;
	}

	function export_groups($file_type, $data)
    {
    	$filename = 'export_groups_'.date('Y-m-d_H-i-s');
    	$export = Export::factory($file_type,$filename);
    	if($file_type == 'pdf')
    		$data = array(array('key' => 'users', 'data' => $data));
    	$export->write_to_file($data);
    	return;
    }
}
?>