<?php
/**
 * @package application.wiki.wiki.component
 */
require_once dirname(__FILE__).'/../wiki_manager.class.php';
require_once dirname(__FILE__).'/../wiki_manager_component.class.php';

/**
 * Component to delete wiki_publications objects
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiManagerWikiPublicationDeleterComponent extends WikiManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[WikiManager :: PARAM_WIKI_PUBLICATION];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$wiki_publication = $this->retrieve_wiki_publication($id);

				if (!$wiki_publication->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedWikiPublicationDeleted';
				}
				else
				{
					$message = 'SelectedWikiPublicationDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedWikiPublicationsDeleted';
				}
				else
				{
					$message = 'SelectedWikiPublicationsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoWikiPublicationsSelected')));
		}
	}
}
?>