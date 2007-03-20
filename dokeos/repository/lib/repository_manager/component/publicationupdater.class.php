<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerPublicationUpdaterComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$application = $_GET[RepositoryManager :: PARAM_PUBLICATION_APPLICATION];
		$publication_id = $_GET[RepositoryManager :: PARAM_PUBLICATION_ID];
		
		if (!empty ($application) && !empty ($publication_id))
		{
			$pub = $this->get_parent()->get_learning_object_publication_attribute($publication_id, $application);
			$latest_version = $pub->get_publication_object()->get_latest_version_id();
			
			$pub->set_publication_object_id($latest_version);
			$success = $pub->update();
			
			$this->redirect(RepositoryManager :: ACTION_VIEW_MY_PUBLICATIONS, get_lang($success ? 'PublicationUpdated' : 'PublicationUpdateFailed'));
		}
		else
		{
			$this->display_warning_page(htmlentities(get_lang('NoPublicationSelected')));
		}
	}
}
?>