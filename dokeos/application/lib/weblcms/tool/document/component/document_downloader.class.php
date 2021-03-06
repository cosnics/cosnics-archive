<?php

require_once dirname(__FILE__) . '/../document_tool.class.php';
require_once dirname(__FILE__) . '/../document_tool_component.class.php';

class DocumentToolDownloaderComponent extends DocumentToolComponent
{
	private $action_bar;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		$dm = WeblcmsDataManager :: get_instance();
		$publication_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);
		$publication = $dm->retrieve_content_object_publication($publication_id);
		$document = $publication->get_content_object();
		$document->send_as_download();
		return '';
	}
	
}

?>