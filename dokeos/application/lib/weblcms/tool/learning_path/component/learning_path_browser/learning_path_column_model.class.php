<?php

require_once dirname(__FILE__) . '/../../../../browser/object_publication_table/object_publication_table_column_model.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class LearningPathColumnModel extends ObjectPublicationTableColumnModel
{
	function LearningPathColumnModel()
	{
		parent :: __construct($this->get_columns());
	}
	
	function get_columns()
	{
		$columns = parent :: get_basic_columns();
		$columns[] = new ObjectTableColumn('progress', false);
		$columns[] = parent :: get_action_column();
		return $columns;
	}
}
?>