<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../user_table/usertable.class.php';
require_once dirname(__FILE__).'/adminuserbrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/adminuserbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/adminuserbrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../usermanager.class.php';
/**
 * Table to display a set of learning objects.
 */
class AdminUserBrowserTable extends UserTable
{
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function AdminUserBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new AdminUserBrowserTableColumnModel();
		$renderer = new AdminUserBrowserTableCellRenderer($browser);
		$data_provider = new AdminUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$actions = array();
		$actions[UserManager :: PARAM_REMOVE_SELECTED] = get_lang('RemoveSelected');
		//$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>