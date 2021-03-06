<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../complex_content_object_item_form.class.php';
require_once dirname(__FILE__).'/../../repository_data_manager.class.php';

/**
 * Repository manager component which gives the user the possibility to create a
 * new complex learning object item in his repository.
 */
class RepositoryManagerComplexUpdaterComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add_help('repository general');

		$cloi_id = Request :: get(RepositoryManager :: PARAM_CLOI_ID);
		$root_id = Request :: get(RepositoryManager :: PARAM_CLOI_ROOT_ID);

		if(!isset($cloi_id) || !isset($root_id))
		{
			$this->display_header($trail, false, true);
			Display :: warning_message('Reference is not set');
			$this->display_footer();
			exit();
		}

		$cloi = $this->retrieve_complex_content_object_item($cloi_id);

		$cloi_form = ComplexContentObjectItemForm :: factory(ComplexContentObjectItemForm :: TYPE_EDIT, $cloi, 'create_complex', 'post',
						$this->get_url(array(RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id,
											 RepositoryManager :: PARAM_CLOI_ID => $cloi_id, 'publish' => Request :: get('publish'))));

		if ($cloi_form->validate())
		{
			$cloi_form->update_complex_content_object_item();
			$cloi = $cloi_form->get_complex_content_object_item();
			$this->redirect(Translation :: get('ObjectUpdated'), false, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $cloi->get_parent(),  RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => Request :: get('publish'), 'clo_action' => 'organise'));
		}
		else
		{
			$this->display_header($trail, false, false);
			//echo '<p>' . Translation :: get('FillIn') . '</p>';
			$cloi_form->display();
			$this->display_footer();
		}
	}
}
?>