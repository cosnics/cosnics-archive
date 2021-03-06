<?php
/**
 * $Id: table_learning_object_publication_list_renderer.class.php 23130 2009-09-25 12:40:53Z vanpouckesven $
 * @package application.weblcms
 * @subpackage browser.listrenderer
 */
require_once dirname(__FILE__).'/../content_object_publication_list_renderer.class.php';
/**
 * Renderer to display a sortable table with learning object publications.
 */
class TableContentObjectPublicationListRenderer extends ContentObjectPublicationListRenderer
{
	/**
	 * The table with all learning object publications to be displayed
	 */
	protected $table;
	/**
	 * Create a new table renderer
	 * @param PublicationBrowser $browser The browser to associate this table
	 * renderer with.
	 */
	function TableContentObjectPublicationListRenderer($browser)
	{
		parent :: __construct($browser);
		// TODO: Assign a dynamic table name.
		$name = 'pubtbl';
		$this->table = new SortableTable($name, array($browser, 'get_publication_count'), array($browser, 'get_publications'));
		$this->table->set_additional_parameters($browser->get_parameters());
		if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
		{
			if($browser->is_allowed(EDIT_RIGHT))
			{
				$actions[] = new ObjectTableFormAction(Tool::ACTION_MOVE_SELECTED_TO_CATEGORY, Translation :: get('Move'), false);
			}
			if($browser->is_allowed(DELETE_RIGHT))
			{
				$actions[] = new ObjectTableFormAction(Tool::ACTION_DELETE, Translation :: get('Delete'));
			}
			$this->table->set_form_actions($actions,$name . '_id',Tool::PARAM_ACTION);
		}
	}
	/**
	 * Sets a header of the table
	 * @see SortableTable::set_header()
	 */
	function set_header($column, $label, $sortable = true)
	{
		return $this->table->set_header($column, $label, $sortable);
	}
	/**
	 * Returns the HTML output of this renderer.
	 * @return string The HTML output
	 */
	function as_html()
	{
		return $this->table->as_html();
	}
}
?>