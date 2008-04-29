<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../event_table/defaulteventtablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../event.class.php';
/**
 * Table column model for the user browser table
 */
class EventBrowserTableColumnModel extends DefaultEventTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	/**
	 * Constructor
	 */
	function EventBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(1);
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
			self :: $modification_column = new EventTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>
