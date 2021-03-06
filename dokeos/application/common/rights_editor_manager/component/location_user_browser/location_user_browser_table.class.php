<?php
/**
 * @package users.lib.usermanager.component.admin_user_browser
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/location_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/location_user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/location_user_browser_table_cell_renderer.class.php';
/**
 * Table to display a set of users.
 */
class LocationUserBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'admin_user_browser_table';
	
	/**
	 * Constructor
	 * @see ContentObjectTable::ContentObjectTable()
	 */
	function LocationUserBrowserTable($browser, $parameters, $condition)
	{
		$model = new LocationUserBrowserTableColumnModel($browser);
		$renderer = new LocationUserBrowserTableCellRenderer($browser);
		$data_provider = new LocationUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, LocationUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		//Deactivated: What should happen when a user is removed ? Full remove or deactivation of account ?
		//$actions[UserManager :: PARAM_REMOVE_SELECTED] = Translation :: get('RemoveSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>