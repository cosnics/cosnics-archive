<?php
/**
 * @package application.weblcms.tool.assessment.component.assessment_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once Path :: get_repository_path(). 'lib/content_object.class.php';
require_once Path :: get_repository_path(). 'lib/content_object/glossary_item/glossary_item.class.php';
/**
 * This class represents a column model for a publication candidate table
 */
class GlossaryViewerTableColumnModel extends ObjectTableColumnModel {
	/**
	 * The column with the action buttons.
	 */
	private static $action_column;
	/**
	 * Constructor.
	 */
	function GlossaryViewerTableColumnModel()
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
		//$columns[] = new StaticTableColumn(Translation :: get(DokeosUtilities :: underscores_to_camelcase(GlossaryItem :: PROPERTY_TITLE)));
		//$columns[] = new StaticTableColumn(Translation :: get(DokeosUtilities :: underscores_to_camelcase(GlossaryItem :: PROPERTY_DESCRIPTION)));
		$alias = RepositoryDataManager :: get_instance()->get_database()->get_alias(ContentObject :: get_table_name());
		$columns[] = new ObjectTableColumn(GlossaryItem :: PROPERTY_TITLE, true, $alias);
		$columns[] = new ObjectTableColumn(GlossaryItem :: PROPERTY_DESCRIPTION, true, $alias);
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
			self :: $action_column = new ObjectTableColumn();
		}
		return self :: $action_column;
	}
}
?>