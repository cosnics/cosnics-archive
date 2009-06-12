<?php
/**
 * @package wiki.tables.wiki_publication_table
 */
require_once dirname(__FILE__).'/wiki_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/wiki_publication_table/default_wiki_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../wiki_publication.class.php';
require_once dirname(__FILE__).'/../../wiki_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 * @author Sven Vanpoucke & Stefan Billiet
 */

class WikiPublicationBrowserTableCellRenderer extends DefaultWikiPublicationTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function WikiPublicationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $wiki_publication)
	{
		if ($column === WikiPublicationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($wiki_publication);
		}

		return parent :: render_cell($column, $wiki_publication);
	}

	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($wiki_publication)
	{
		$toolbar_data = array();

		$toolbar_data[] = array(
			'href' => $this->browser->get_update_wiki_publication_url($wiki_publication),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_image_path().'action_edit.png'
		);

		$toolbar_data[] = array(
			'href' => $this->browser->get_delete_wiki_publication_url($wiki_publication),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png',
		);

		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>