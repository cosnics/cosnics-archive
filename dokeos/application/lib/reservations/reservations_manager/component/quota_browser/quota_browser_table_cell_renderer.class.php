<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/quota_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/quota_table/default_quota_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../quota.class.php';
require_once dirname(__FILE__).'/../../reservations_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class QuotaBrowserTableCellRenderer extends DefaultQuotaTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	protected $browser;
	
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function QuotaBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	
	function render_cell($column, $quota)
	{
		if ($column === QuotaBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($quota);
		}
		
		return parent :: render_cell($column, $quota);
	}
	
	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($quota)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
				'href' => $this->browser->get_update_quota_url($quota->get_id()),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path() . 'action_edit.png'
		);
		
		$toolbar_data[] = array(
				'href' => $this->browser->get_delete_quota_url($quota->get_id()),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path() . 'action_delete.png',
				'confirm' => true
		);
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>