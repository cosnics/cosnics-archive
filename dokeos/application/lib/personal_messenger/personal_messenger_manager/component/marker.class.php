<?php
/**
 * @package application.personal_messenger.personal_messenger_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

require_once dirname(__FILE__).'/../personal_messenger_manager.class.php';
require_once dirname(__FILE__).'/../personal_messenger_manager_component.class.php';

class PersonalMessengerManagerMarkerComponent extends PersonalMessengerManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[PersonalMessengerManager :: PARAM_PERSONAL_MESSAGE_ID];
		$mark_type = $_GET[PersonalMessengerManager :: PARAM_MARK_TYPE];
		$failures = 0;
		$folder = $_GET[PersonalMessengerManager :: PARAM_FOLDER];
		
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}
			
			foreach ($ids as $id)
			{
				$publication = $this->get_parent()->retrieve_personal_message_publication($id);
				if ($mark_type == PersonalMessengerManager :: PARAM_MARK_SELECTED_READ)
				{
					$publication->set_status(0);
				}
				elseif($mark_type == PersonalMessengerManager :: PARAM_MARK_SELECTED_UNREAD)
				{
					$publication->set_status(1);
				}
				
				if (!$publication->update())
				{
					$failures++;
				}
			}
			
			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPublicationNotUpdated';
				}
				else
				{
					$message = 'SelectedPublicationsNotUpdated';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedPublicationUpdated';
				}
				else
				{
					$message = 'SelectedPublicationsUpdated';
				}
			}
			
			$this->redirect(null, Translation :: get($message), ($failures ? true : false), array(PersonalMessengerManager :: PARAM_ACTION => PersonalMessengerManager :: ACTION_BROWSE_MESSAGES, PersonalMessengerManager :: PARAM_FOLDER => $folder));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoPublicationSelected')));
		}
	}
}
?>