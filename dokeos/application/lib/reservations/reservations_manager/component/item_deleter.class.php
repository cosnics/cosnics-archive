<?php
/**
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';

/**
 * Component to delete an item
 */
class ReservationsManagerItemDeleterComponent extends ReservationsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{ 
		$ids = $_GET[ReservationsManager :: PARAM_ITEM_ID];
		
		if (!$this->get_user())
		{
			$this->display_header(null);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		if($ids)
		{ 
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			$bool = true;
			$category = -1;
			
			foreach($ids as $id)
			{
    			$items = $this->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $id));
    			$item = $items->next_result();
    			
    			if($category == -1) $category = $item->get_category();
    			
    			$item->set_status(Item :: STATUS_DELETED);
    			if(!$item->update()) $bool = false;
    			
			}
			
			if(count($ids) == 1)
				$message = $bool ? 'ItemDeleted' : 'ItemNotDeleted';
			else
				$message = $bool ? 'ItemsDeleted' : 'ItemsNotDeleted';
			
			
			$this->redirect('url', Translation :: get($message), ($bool ? false : true), 
				array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS,
					  ReservationsManager :: PARAM_CATEGORY_ID => $category));
		}
		else
		{
			$this->display_header();
			$this->display_error_message(Translation :: get("NoObjectSelected"));
			$this->display_footer();
		}
	}

}
?>