<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../../../course/course_table/coursetable.class.php';
require_once dirname(__FILE__).'/unsubscribebrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/unsubscribebrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/unsubscribebrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../weblcms.class.php';
/**
 * Table to display a list of users subscribed to a course.
 */
class UnsubscribeBrowserTable extends CourseTable
{
	/**
	 * Constructor
	 */
	function UnsubscribeBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new UnsubscribeBrowserTableColumnModel();
		$renderer = new UnsubscribeBrowserTableCellRenderer($browser);
		$data_provider = new UnsubscribeBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_default_row_count(20);
	}
}
?>