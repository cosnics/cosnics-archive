<?php
/**
 * @package repository.repositorymanager
 */

require_once dirname(__FILE__).'/../../platform_category.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
/**
 * Table column model for the user browser table
 */
class CategoryBrowserTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function CategoryBrowserTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
		$this->set_default_order_column(1);
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

	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new StaticTableColumn('');
		$columns[] = new StaticTableColumn(PlatformCategory :: PROPERTY_NAME);
		$columns[] = new StaticTableColumn(Translation :: get('Subcategories'));
		return $columns;
	}
}
?>
