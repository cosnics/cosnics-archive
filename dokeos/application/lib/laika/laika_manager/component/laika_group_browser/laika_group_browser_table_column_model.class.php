<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_group_path() . '/lib/group_table/default_group_table_column_model.class.php';
require_once Path :: get_group_path() . '/lib/group.class.php';
/**
 * Table column model for the user browser table
 */
class LaikaGroupBrowserTableColumnModel extends DefaultGroupTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function LaikaGroupBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(1);
		$this->add_column(new StaticTableColumn(Translation :: get('Subgroups')));
		$this->add_column(new StaticTableColumn(Translation :: get('Users')));
		$this->add_column(new StaticTableColumn(Translation :: get('Participants')));
		$this->add_column(new StaticTableColumn(Translation :: get('Percentage')));
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
