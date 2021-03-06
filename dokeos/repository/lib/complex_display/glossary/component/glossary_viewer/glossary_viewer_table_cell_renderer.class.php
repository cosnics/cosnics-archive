<?php
/**
 * @package application.weblcms.tool.assessment.component.assessment_publication_table
 */
require_once Path :: get_repository_path(). 'lib/content_object_table/default_content_object_table_cell_renderer.class.php';
require_once Path :: get_repository_path(). 'lib/content_object.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/glossary_viewer_table_column_model.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class GlossaryViewerTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
	private $table_actions;
	private $browser;
	private $dm;
	private $glossary_item;
	/**
	 * Constructor.
	 * @param string $publish_url_format URL for publishing the selected
	 * learning object.
	 * @param string $edit_and_publish_url_format URL for editing and publishing
	 * the selected learning object.
	 */
	function GlossaryViewerTableCellRenderer($browser)
	{
		$this->table_actions = array();
		$this->browser = $browser;
		$this->dm = RepositoryDataManager :: get_instance();
	}
	/*
	 * Inherited
	 */
	function render_cell($column, $glossary_item)
	{
		if ($column === GlossaryViewerTableColumnModel :: get_action_column())
		{
			return $this->get_actions($glossary_item);
		}

		if(!$this->glossary_item || $this->glossary_item->get_id() != $glossary_item->get_ref())
			$this->glossary_item = $this->dm->retrieve_content_object($glossary_item->get_ref(), 'glossary_item');

		switch ($column->get_name())
		{
			case GlossaryItem :: PROPERTY_TITLE:
				return $this->glossary_item->get_title();
			case GlossaryItem :: PROPERTY_DESCRIPTION:
				return strip_tags($this->glossary_item->get_description());
		}
	}

	function get_actions($glossary_item)
	{
		if($this->browser->is_allowed(EDIT_RIGHT))
		{
			$actions[] = array(
				'href' => $this->browser->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_UPDATE, 'selected_cloi' => $glossary_item->get_id(), 'pid' => Request :: get('pid'))),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path().'action_edit.png'
			);
		}

		if($this->browser->is_allowed(DELETE_RIGHT))
		{
			$actions[] = array(
				'href' => $this->browser->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_DELETE, 'selected_cloi' => $glossary_item->get_id(), 'pid' => Request :: get('pid'))),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path().'action_delete.png'
			);
		}

		return DokeosUtilities :: build_toolbar($actions);
	}

}
?>