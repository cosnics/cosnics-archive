<?php
/**
 * @package application.lib.personal_messenger.pm_publication_table
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once Path :: get_repository_path(). 'lib/content_object.class.php';
require_once Path :: get_application_path() . 'lib/distribute/announcement_distribution.class.php';

class DefaultAnnouncementDistributionTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultAnnouncementDistributionTableColumnModel()
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
		$columns[] = new ObjectTableColumn(AnnouncementDistribution :: PROPERTY_STATUS);
		$columns[] = new ObjectTableColumn(AnnouncementDistribution :: PROPERTY_ANNOUNCEMENT);
		$columns[] = new ObjectTableColumn(AnnouncementDistribution :: PROPERTY_PUBLISHER);
		$columns[] = new ObjectTableColumn(AnnouncementDistribution :: PROPERTY_PUBLISHED);
		return $columns;
	}
}
?>