<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/admin_course_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/admin_course_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/admin_course_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../weblcms_manager.class.php';
/**
 * Table to display a set of courses.
 */
class AdminCourseBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'course_browser_table';
	
	/**
	 * Constructor
	 */
	function AdminCourseBrowserTable($browser, $parameters, $condition)
	{
		$model = new AdminCourseBrowserTableColumnModel();
		$renderer = new AdminCourseBrowserTableCellRenderer($browser);
		$data_provider = new AdminCourseBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, AdminCourseBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$actions = array();
		
		$actions[] = new ObjectTableFormAction(WeblcmsManager :: PARAM_REMOVE_SELECTED, Translation :: get('RemoveSelected'));
		
		//$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>