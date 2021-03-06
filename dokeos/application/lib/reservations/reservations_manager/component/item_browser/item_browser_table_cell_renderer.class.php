<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/item_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/item_table/default_item_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../item.class.php';
require_once dirname(__FILE__).'/../../reservations_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ItemBrowserTableCellRenderer extends DefaultItemTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	protected $browser;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function ItemBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $item)
	{
		if ($column === ItemBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($item);
		}
		
		return parent :: render_cell($column, $item);
	}
	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($item)
	{
		$toolbar_data = array();
		
		if(get_class($this->browser) == 'ReservationsManagerAdminItemBrowserComponent')
		{
			if($this->browser->has_right('item', $item->get_id(), ReservationsRights :: DELETE_RIGHT) || $item->get_responsible() == $this->browser->get_user_id())
			{
				$toolbar_data[] = array(
						'href' => $this->browser->get_delete_item_url($item->get_id(), $this->browser->get_category()),
						'label' => Translation :: get('Delete'),
						'img' => Theme :: get_common_image_path() . 'action_delete.png',
						'confirm' => true
				);
			}
			
			if($this->browser->has_right('item', $item->get_id(), ReservationsRights :: EDIT_RIGHT) || $item->get_responsible() == $this->browser->get_user_id())
			{
				$toolbar_data[] = array(
						'href' => $this->browser->get_update_item_url($item->get_id(), $this->browser->get_category()),
						'label' => Translation :: get('Edit'),
						'img' => Theme :: get_common_image_path() . 'action_edit.png'
				);
				
				$toolbar_data[] = array(
					'href' => $this->browser->get_modify_rights_url('item', $item->get_id()),
					'label' => Translation :: get('ModifyRights'),
					'img' => Theme :: get_common_image_path() . 'action_rights.png'		
				);
			}
		}
		
		if(get_class($this->browser) == 'ReservationsManagerAdminItemBrowserComponent')
		{
			$url = $this->browser->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_RESERVATIONS, 
												 ReservationsManager :: PARAM_ITEM_ID => $item->get_id()));
		}
		else
		{
			$url = $this->browser->get_browse_reservations_url($item->get_id());
		}
		
		if($this->browser->has_right('item', $item->get_id(), ReservationsRights :: VIEW_RIGHT))
		{
			$toolbar_data[] = array(
					'href' => $url,
					'label' => Translation :: get('BrowseReservations'),
					'img' => Theme :: get_common_image_path() . 'action_browser.png'
			);
		}
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>