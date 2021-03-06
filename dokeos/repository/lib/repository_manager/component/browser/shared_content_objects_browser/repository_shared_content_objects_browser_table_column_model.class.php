<?php
/**
 * $Id: repository_browser_table_column_model.class.php 21940 2009-07-09 09:45:03Z scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../../content_object_table/default_shared_content_object_table_column_model.class.php';
/**
 * Table column model for the repository browser table
 */
class RepositorySharedContentObjectsBrowserTableColumnModel extends DefaultSharedContentObjectTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function RepositorySharedContentObjectsBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(0);
		$this->add_column(self :: get_modification_column());
	}
	/**
	 * Gets the modification column
	 * @return ContentObjectTableColumn
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
