<?php
/**
 * @package webconferencing.tables.webconference_table
 */

require_once dirname(__FILE__).'/../../../tables/webconference_table/default_webconference_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../webconference.class.php';

/**
 * Table column model for the webconference browser table
 * @author Stefaan Vanbillemont
 */

class WebconferenceBrowserTableColumnModel extends DefaultWebconferenceTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function WebconferenceBrowserTableColumnModel()
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
			self :: $modification_column = new ObjectTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>