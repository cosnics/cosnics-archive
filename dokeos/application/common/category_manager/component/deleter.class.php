<?php
/**
 */
require_once dirname(__FILE__).'/../category_manager.class.php';
require_once dirname(__FILE__).'/../category_manager_component.class.php';

/**
 * Component to delete a category
 */
class CategoryManagerDeleterComponent extends CategoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{ 
		$ids = Request :: get(CategoryManager :: PARAM_CATEGORY_ID);
		
		if (!$this->get_user())
		{
			$this->display_header($this->get_breadcrumb_trail());
			Display :: error_message(Translation :: get("NotAllowed"));
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
			$parent = -1;
			
			foreach($ids as $id)
			{
    			if(!$this->allowed_to_delete_category($id)) { $bool = false; continue; }
    			
    			$categories = $this->retrieve_categories(new EqualityCondition(PlatformCategory :: PROPERTY_ID, $id));
    			$category = $categories->next_result();
    			
    			if($parent == -1) $parent = $category->get_parent();
    			if(!$category->delete()) $bool = false;
			}
			
			if(count($ids) == 1)
				$message = $bool ? 'CategoryDeleted' : 'CategoryNotDeleted';
			else
				$message = $bool ? 'CategoriesDeleted' : 'CategoriesNotDeleted';
			
			
			/*if(get_class($this->get_parent()) == 'RepositoryCategoryManager')
				$this->repository_redirect(RepositoryManager :: ACTION_MANAGE_CATEGORIES, Translation :: get($message), 0, ($bool ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $parent));
			else*/
				$this->redirect(Translation :: get($message), ($bool ? false : true), 
					array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES,
					  	  CategoryManager :: PARAM_CATEGORY_ID => $parent));
		}
		else
		{
			$this->display_header($this->get_breadcrumb_trail());
			$this->display_error_message(Translation :: get("NoObjectSelected"));
			$this->display_footer();
		}
	}

}
?>