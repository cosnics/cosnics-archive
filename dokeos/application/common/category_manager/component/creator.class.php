<?php
/**
 * @package reservations.lib.categorymanager.component
 */
require_once dirname(__FILE__).'/../category_manager.class.php';
require_once dirname(__FILE__).'/../category_manager_component.class.php';
require_once dirname(__FILE__).'/../platform_category.class.php';
require_once dirname(__FILE__).'/../category_form.class.php';

class CategoryManagerCreatorComponent extends CategoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$category_id = $_GET[CategoryManager :: PARAM_CATEGORY_ID];
		$user = $this->get_user();

		$category = $this->get_category();
		$category->set_parent(isset($category_id)?$category_id:0);
		
		$form = new CategoryForm(CategoryForm :: TYPE_CREATE, $this->get_url(array(CategoryManager :: PARAM_CATEGORY_ID => $category_id)), $category, $user);

		if($form->validate())
		{
			$success = $form->create_category();
			$this->redirect('url', Translation :: get($success ? 'CategoryCreated' : 'CategoryNotCreated'), ($success ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $category->get_id()));
		}
		else
		{
			$form->display();
		}
	}
}
?>