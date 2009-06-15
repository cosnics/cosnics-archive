<?php
/**
 * @package wiki.tables.wiki_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
/**
 * Data provider for a wiki_publication table
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiPublicationBrowserTableDataProvider extends ObjectTableDataProvider
{
  /**
   * Constructor
   * @param ApplicationComponent $browser
   * @param Condition $condition
   */
  function WikiPublicationBrowserTableDataProvider($browser, $condition)
  {
		parent :: __construct($browser, $condition);
  }
  /**
   * Retrieves the objects
   * @param int $offset
   * @param int $count
   * @param string $order_property
   * @param int $order_direction (SORT_ASC or SORT_DESC)
   * @return ResultSet A set of objects
   */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
		$order_property = $this->get_order_property($order_property);
		$order_direction = $this->get_order_property($order_direction);
        $publications = $this->get_browser()->retrieve_wiki_publications($this->get_condition())->as_array();
        foreach($publications as $publication)
        {
            $learning_objects[] = RepositoryDataManager :: get_instance()->retrieve_learning_object($publication->get_learning_object());
        }
        return $learning_objects;
     	//return $this->get_browser()->retrieve_wiki_publications($this->get_condition(), $offset, $count, $order_property, $order_direction);
    }
  /**
   * Gets the number of objects in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->get_browser()->count_wiki_publications($this->get_condition());
    }
}
?>