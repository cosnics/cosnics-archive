<?php
/**
 * $Id: default_content_object_table_column_model.class.php 21940 2009-07-09 09:45:03Z scara84 $
 * @package repository.learningobjecttable
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once dirname(__FILE__).'/../content_object.class.php';

/**
 * This is the default column model, used when a ContentObjectTable does not
 * provide its own model.
 *
 * The default model contains the following columns:
 *
 * - The type of the learning object
 * - The title of the learning object
 * - The description of the learning object
 * - The date when the learning object was last modified
 *
 * Although this model works best in conjunction with the default cell
 * renderer, it can be used with any ContentObjectTableCellRenderer.
 *
 * @see ContentObjectTable
 * @see ContentObjectTableColumnModel
 * @see DefaultContentObjectTableCellRenderer
 * @author Tim De Pauw
 */
class DefaultSharedContentObjectsTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultSharedContentObjectsTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}
	/**
	 * Gets the default columns for this model
	 * @return ContentObjectTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TYPE);
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE);
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION);
		$columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_MODIFICATION_DATE);
		$columns[] = new StaticTableColumn(Translation :: get('Versions'));
                $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_OWNER_ID);
		return $columns;
	}
}
?>