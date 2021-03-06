<?php
/**
 * @package application.weblcms.tool.exercise.component.exercise_page_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once Path :: get_repository_path(). 'lib/content_object.class.php';
/**
 * This class represents a column model for a publication candidate table
 */
class WikiPageTableColumnModel extends ObjectTableColumnModel {
	/**
	 * The column with the action buttons.
	 */
	private static $action_column;
	/**
	 * Constructor.
	 */
	function WikiPageTableColumnModel()
	{
		parent :: __construct(self :: get_columns(), 1, SORT_ASC);
	}
	/**
	 * Gets the columns of this table.
	 * @return array An array of all columns in this table.
	 * @see ContentObjectTableColumn
	 */
	function get_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE);
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_MODIFICATION_DATE);
        $columns[] = new StaticTableColumn(Translation :: get('Versions'));
		$columns[] = self :: get_action_column();
		return $columns;
	}
	/**
	 * Gets the column wich contains the action buttons.
	 * @return ContentObjectTableColumn The action column.
	 */
	static function get_action_column()
	{
		if (!isset(self :: $action_column))
		{
			self :: $action_column = new StaticTableColumn(Translation :: get('Actions'));
		}
		return self :: $action_column;
	}
}
?>