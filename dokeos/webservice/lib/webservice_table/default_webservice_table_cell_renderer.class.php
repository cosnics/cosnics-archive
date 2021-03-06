<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../webservice_registration.class.php';
/**
 * TODO: Add comment
 */
class DefaultWebserviceTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultWebserviceTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $content_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $webservice)
	{
		switch ($column->get_name())
		{
			case WebserviceRegistration :: PROPERTY_NAME :
				return $webservice->get_name();
			case WebserviceRegistration :: PROPERTY_DESCRIPTION :
				$description = strip_tags($webservice->get_description());
                return DokeosUtilities::truncate_string($description);
			default :
			    return '&nbsp;';
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>