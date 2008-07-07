<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../complex_learning_object_item_form.class.php';
require_once dirname(__FILE__).'/../../repository_data_manager.class.php';

/**
 * Repository manager component which gives the user the possibility to create a
 * new complex learning object item in his repository. 
 */
class RepositoryManagerComplexCreatorComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();

		$owner = $this->get_user()->get_id();
		$ref = $_GET[RepositoryManager :: PARAM_CLOI_REF]?$_GET[RepositoryManager :: PARAM_CLOI_REF]:0;
		$parent = $_GET[RepositoryManager :: PARAM_CLOI_PARENT]?$_GET[RepositoryManager :: PARAM_CLOI_PARENT]:0;

		if(!isset($ref))
		{
			$this->display_header($trail);	
			Display :: display_warning_message('Reference is not set');			
			$this->display_footer();
		}

		$cloi = ComplexLearningObjectItem :: factory(RepositoryDataManager :: get_instance()->determine_learning_object_type($ref));

		$cloi->set_ref($ref);
		$cloi->set_complex_ref(0);
		$cloi->set_parent($parent);
		$cloi->set_user_id($owner);
		
		$cloi_form = ComplexLearningObjectItemForm :: factory(ComplexLearningObjectItemForm :: TYPE_CREATE, $cloi, 'create_complex', 'post', $this->get_url(array(RepositoryManager :: PARAM_CLOI_REF => $ref)));		
		
		if ($cloi_form->validate())
		{ 
			$cloi_form->create_complex_learning_object_item();
			$cloi = $cloi_form->get_complex_learning_object_item();
			$this->redirect(RepositoryManager :: ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS, Translation :: get('ObjectCreated'), 0, false, array(RepositoryManager :: PARAM_CLOI_ID => $cloi->get_id()));
		}
		else
		{
			$this->display_header($trail);
			echo '<p>' . Translation :: get('FillIn') . '</p>';
			$cloi_form->display();
			$this->display_footer();
		}
	}
}
?>
