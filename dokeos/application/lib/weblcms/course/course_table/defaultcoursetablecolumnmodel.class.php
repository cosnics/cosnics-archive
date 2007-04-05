<?php
/**
 * @package repository.coursetable
 */
require_once dirname(__FILE__).'/coursetablecolumnmodel.class.php';
require_once dirname(__FILE__).'/coursetablecolumn.class.php';
require_once dirname(__FILE__).'/../course.class.php';

/**
 * TODO: Add comment
 */
class DefaultCourseTableColumnModel extends CourseTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultCourseTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 0);
	}
	/**
	 * Gets the default columns for this model
	 * @return LearningObjectTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new CourseTableColumn(Course :: PROPERTY_VISUAL, true);
		$columns[] = new CourseTableColumn(Course :: PROPERTY_NAME, true);
		return $columns;
	}
}
?>