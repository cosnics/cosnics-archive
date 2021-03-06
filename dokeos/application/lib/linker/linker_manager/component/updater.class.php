<?php

require_once dirname(__FILE__).'/../linker_manager.class.php';
require_once dirname(__FILE__).'/../linker_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/link_form.class.php';

class LinkerManagerUpdaterComponent extends LinkerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => LinkerManager :: ACTION_BROWSE_LINKS)), Translation :: get('Links')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateLink')));

		$link = $this->retrieve_link(Request :: get(LinkerManager :: PARAM_LINK_ID));
		$form = new LinkForm(LinkForm :: TYPE_EDIT, $link, $this->get_url(array(LinkerManager :: PARAM_LINK_ID => $link->get_id())), $this->get_user());
		
		if($form->validate())
		{
			$success = $form->update_link();
			$this->redirect($success ? Translation :: get('LinkUpdated') : Translation :: get('LinkNotUpdated'), !$success, array(Application :: PARAM_ACTION => LinkerManager :: ACTION_BROWSE_LINKS));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>