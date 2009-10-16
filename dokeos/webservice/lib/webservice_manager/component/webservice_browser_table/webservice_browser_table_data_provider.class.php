<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
/**
 * Data provider for a repository browser table.
 *
 * This class implements some functions to allow repository browser tables to
 * retrieve information about the learning objects to display.
 */
class WebserviceBrowserTableDataProvider extends ObjectTableDataProvider
{
  /**
   * Constructor
   * @param RepositoryManagerComponent $browser
   * @param Condition $condition
   */
  function WebserviceBrowserTableDataProvider($browser, $condition)
  {
		parent :: __construct($browser, $condition);
  }
  /**
   * Gets the learning objects
   * @param int $offset
   * @param int $count
   * @param string $order_property
   * @return ResultSet A set of matching learning objects.
   */
    function get_objects($offset, $count, $order_property = null)
    {
		$order_property = $this->get_order_property($order_property);

        $webservices = WebserviceDataManager :: get_instance()->retrieve_webservices($this->get_condition(), $offset, $count, $order_property);

        return $webservices;
    }
  /**
   * Gets the number of learning objects in the table
   * @return int
   */
    function get_object_count()
    {
      return WebserviceDataManager :: get_instance()->count_webservices($this->get_condition());
    }
}
?>