<?php
require_once Path :: get_repository_path().'lib/complex_display/wiki/wiki_display.class.php';

class ToolComplexDeleterComponent extends ToolComponent
{
	function run()
	{
		if($this->is_allowed(DELETE_RIGHT))
		{
			if(Request :: get(Tool :: PARAM_COMPLEX_ID))
				$cloi_ids = Request :: get(Tool :: PARAM_COMPLEX_ID);
			else
				$cloi_ids = $_POST[Tool :: PARAM_COMPLEX_ID];

			if (!is_array($cloi_ids))
			{
				$cloi_ids = array ($cloi_ids);
			}

			$datamanager = RepositoryDataManager :: get_instance();

			foreach($cloi_ids as $index => $cid)
			{
				//$publication = $datamanager->retrieve_complex_content_object_item($pid);
                //if(!WikiTool :: is_wiki_locked($cid))
                {
                    $cloi = new ComplexContentObjectItem();
                    $cloi->set_id($cid);
                    $cloi->delete();
                }

			}
            if(empty($message))
            {
                if(count($cloi_ids) > 1)
                {
                    $message = htmlentities(Translation :: get('ContentObjectPublicationsDeleted'));
                }
                else
                {
                    $message = htmlentities(Translation :: get('ContentObjectPublicationDeleted'));
                }
            }

            switch(Request :: get('tool'))
            {
                case 'wiki':
                $this->redirect($message, false, array(Tool :: PARAM_ACTION => 'view', WikiDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI, 'pid' => Request :: get('pid')));
                break;

                case 'learning_path':
                $this->redirect($message, false, array(Tool :: PARAM_ACTION => 'view_clo', 'pid' => Request :: get('pid')));
            }
		}
	}
}
?>