<?php
/**
 * @package wiki.tables.wiki_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once dirname(__FILE__).'/../../wiki_publication.class.php';

/**
 * Default column model for the wiki_publication table
 * @author Sven Vanpoucke & Stefan Billiet
 */
class DefaultWikiPublicationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultWikiPublicationTableColumnModel($columns)
	{
		parent :: __construct(empty($columns)?self :: get_default_columns():$columns, 1);
	}

	/**
	 * Gets the default columns for this model
	 * @return Array(ObjectTableColumn)
	 */
	private static function get_default_columns()
	{
		$columns = array();

//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_ID);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_PARENT_ID);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_CATEGORY);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_FROM_DATE);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_TO_DATE);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_HIDDEN);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_PUBLISHER);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_PUBLISHED);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_MODIFIED);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_DISPLAY_ORDER);
//		$columns[] = new ObjectTableColumn(WikiPublication :: PROPERTY_EMAIL_SENT);

		return $columns;
	}
}
?>