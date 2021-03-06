<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
/**
 * Repository manager component which provides functionality to revert a
 * learning object from the users repository to a previous state.
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class RepositoryManagerReverterComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			$failures = 0;
			foreach ($ids as $object_id)
			{
				$object = $this->get_parent()->retrieve_content_object($object_id);
				// TODO: Roles & Rights.
				if ($object->get_owner_id() == $this->get_user_id())
				{
					if ($this->get_parent()->content_object_revert_allowed($object))
					{
						$object->version();
					}
					else
					{
						$failures ++;
					}

				}
				else
				{
					$failures ++;
				}
			}

			if ($failures)
			{
				$message = 'SelectedObjectNotReverted';
			}
			else
			{
				$message = 'SelectedObjectReverted';
			}
			$this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>