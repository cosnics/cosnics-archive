<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/user_location_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/user_location_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/user_location_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../user_right_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class UserLocationBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'location_browser_table';

	/**
	 * Constructor
	 * @see ContentObjectTable::ContentObjectTable()
	 */
	function UserLocationBrowserTable($browser, $parameters, $condition)
	{
		$model = new UserLocationBrowserTableColumnModel($browser);
		$renderer = new UserLocationBrowserTableCellRenderer($browser);
		$data_provider = new UserLocationBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, UserLocationBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$this->set_default_row_count(20);
	}

	function get_objects($offset, $count, $order_column)
	{
		$locations = $this->get_data_provider()->get_objects($offset, $count, $this->get_column_model()->get_order_column($order_column - ($this->has_form_actions() ? 1 : 0)));
		$table_data = array ();
		$column_count = $this->get_column_model()->get_column_count();
		while ($location = $locations->next_result())
		{
			$row = array ();
			if ($this->has_form_actions())
			{
				$row[] = $location->get_name();
			}
			for ($i = 0; $i < $column_count; $i ++)
			{
				$row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $location);
			}
			$table_data[] = $row;
		}
		return $table_data;
	}
}
?>