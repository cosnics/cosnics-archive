<?php
/**
 * @package webconferencing.tables.webconference_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
/**
 * Data provider for a webconference table
 * @author Stefaan Vanbillemont
 */
class WebconferenceBrowserTableDataProvider extends ObjectTableDataProvider
{
/**
 * Constructor
 * @param ApplicationComponent $browser
 * @param Condition $condition
 */
    function WebconferenceBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }
    /**
     * Retrieves the objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of objects
     */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);

        return $this->get_browser()->retrieve_webconferences($this->get_condition(), $offset, $count, $order_property);
    }
    /**
     * Gets the number of objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_webconferences($this->get_condition());
    }
}
?>