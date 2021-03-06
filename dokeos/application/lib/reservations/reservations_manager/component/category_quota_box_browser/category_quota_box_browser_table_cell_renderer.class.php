<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/category_quota_box_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/category_quota_box_table/default_category_quota_box_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../quota_box.class.php';
require_once dirname(__FILE__).'/../../../quota_box_rel_category.class.php';
require_once dirname(__FILE__).'/../../reservations_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class CategoryQuotaBoxBrowserTableCellRenderer extends DefaultCategoryQuotaBoxTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	protected $browser;
	
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function CategoryQuotaBoxBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	
	function render_cell($column, $quota_box_rel_category)
	{
		if ($column === CategoryQuotaBoxBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($quota_box_rel_category);
		}
		
		return parent :: render_cell($column, $quota_box_rel_category);
	}
	
	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($quota_box_rel_category)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
				'href' => $this->browser->get_update_category_quota_box_url($quota_box_rel_category->get_id()),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path() . 'action_edit.png'
		);
		
		$toolbar_data[] = array(
				'href' => $this->browser->get_delete_category_quota_box_url($quota_box_rel_category->get_id(), $quota_box_rel_category->get_category_id()),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path() . 'action_delete.png',
				'confirm' => true
		);
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>