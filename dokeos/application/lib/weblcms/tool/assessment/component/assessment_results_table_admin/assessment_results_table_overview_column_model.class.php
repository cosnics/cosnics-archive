<?php
/**
 * @package application.weblcms.tool.assessment.component.assessment_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object/assessment/assessment.class.php';
/**
 * This class represents a column model for a publication candidate table
 */
class AssessmentResultsTableOverviewAdminColumnModel extends ObjectTableColumnModel {
	/**
	 * The column with the action buttons.
	 */
	private static $action_column;
	/**
	 * Constructor.
	 */
	function AssessmentResultsTableOverviewAdminColumnModel()
	{
		parent :: __construct(self :: get_columns(), 1, SORT_ASC);
	}
	/**
	 * Gets the columns of this table.
	 * @return array An array of all columns in this table.
	 * @see LearningObjectTableColumn
	 */
	private static function get_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(Translation :: get(Assessment :: PROPERTY_ASSESSMENT_TYPE), false);
		$columns[] = new ObjectTableColumn(Translation :: get(Assessment :: PROPERTY_TITLE), false);
		$columns[] = new ObjectTableColumn(Translation :: get(Assessment :: PROPERTY_TIMES_TAKEN), false);
		$columns[] = new ObjectTableColumn(Translation :: get(Assessment :: PROPERTY_AVERAGE_SCORE), false);
		$columns[] = self :: get_action_column();
		return $columns;
	}
	/**
	 * Gets the column wich contains the action buttons.
	 * @return LearningObjectTableColumn The action column.
	 */
	static function get_action_column()
	{
		if (!isset(self :: $action_column))
		{
			self :: $action_column = new ObjectTableColumn(Translation :: get('Actions'));
		}
		return self :: $action_column;
	}
}
?>