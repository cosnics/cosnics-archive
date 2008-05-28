<?php
/**
 * @package application.lib.menu.menu_manager.component.menupublicationbrowser
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/menu_item_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/menu_item_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/menu_item_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../menu_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class MenuItemBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'menu_item_browser_table';
	
	/**
	 * Constructor
	 */
	function MenuItemBrowserTable($browser, $parameters, $condition)
	{
		$model = new MenuItemBrowserTableColumnModel($browser);
		$renderer = new MenuItemBrowserTableCellRenderer($browser);
		$data_provider = new MenuItemBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, MenuItemBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$actions = array();
		$actions[MenuManager :: PARAM_DELETE_SELECTED] = Translation :: get('RemoveSelected');
		$user = $browser->get_user();
		$this->set_form_actions($actions);
		$this->set_default_row_count(10);
	}
}
?>