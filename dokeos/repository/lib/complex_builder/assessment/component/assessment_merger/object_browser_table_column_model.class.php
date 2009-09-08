<?php
/**
 * $Id: repository_browser_table_column_model.class.php 21940 2009-07-09 09:45:03Z scara84 $
 * @package repository.repositorymanager
 */
require_once Path :: get_repository_path() . 'lib/learning_object_table/default_learning_object_table_column_model.class.php';
/**
 * Table column model for the repository browser table
 */
class ObjectBrowserTableColumnModel extends DefaultLearningObjectTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function ObjectBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(0);
		$this->add_column(self :: get_modification_column());
	}
	/**
	 * Gets the modification column
	 * @return LearningObjectTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new StaticTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>