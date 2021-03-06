<?php
class ToolToggleVisibilityComponent extends ToolComponent
{
	function run()
	{
		if($this->is_allowed(DELETE_RIGHT))
		{
			if(Request :: get(Tool :: PARAM_PUBLICATION_ID))
			{
				$publication_ids = Request :: get(Tool :: PARAM_PUBLICATION_ID);
			}
			else
			{
				$publication_ids = $_POST[Tool :: PARAM_PUBLICATION_ID];
			}

			if (!is_array($publication_ids))
			{
				$publication_ids = array ($publication_ids);
			}

			$datamanager = WeblcmsDataManager :: get_instance();

			foreach($publication_ids as $index => $pid)
			{
				$publication = $datamanager->retrieve_content_object_publication($pid);

				if(Request :: get(PARAM_VISIBILITY))
				{
					$publication->set_hidden(Request :: get(PARAM_VISIBILITY));
				}
				else
				{
					$publication->toggle_visibility();
				}

				$publication->update();
			}

			if(count($publication_ids) > 1)
			{
				$message = htmlentities(Translation :: get('ContentObjectPublicationsVisibilityChanged'));
			}
			else
			{
				$message = htmlentities(Translation :: get('ContentObjectPublicationVisibilityChanged'));
			}

			$params = array();
			$params['tool_action'] = null;
			if(Request :: get('details') == 1)
			{
				$params['pid'] = $pid;
				$params['tool_action'] = 'view';
			}

			//$this->redirect($message, '', $params);

			$this->redirect($message, false, $params);
		}
	}
}
?>
