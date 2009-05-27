<?php
/**
 * @author Michael Kyndt
 */

class ComplexDisplayDeleterComponent extends ComplexDisplayComponent
{
    function run()
	{
        if($this->get_parent()->get_parent()->is_allowed(DELETE_RIGHT) /*&& !WikiTool :: is_wiki_locked(Request :: get(Tool :: PARAM_PUBLICATION_ID))*/)
		{
			if(isset($_GET[Tool :: PARAM_PUBLICATION_ID]))
				$publication_ids = $_GET[Tool :: PARAM_PUBLICATION_ID];
			else
				$publication_ids = $_POST[Tool :: PARAM_PUBLICATION_ID];

			if (!is_array($publication_ids))
			{
				$publication_ids = array ($publication_ids);
			}

            $datamanager = RepositoryDataManager :: get_instance();

			foreach($publication_ids as $index => $pid)
			{
				$publication = $datamanager->retrieve_learning_object($pid);
				$publication->delete();
			}
			if(count($publication_ids) > 1)
			{
				$message = htmlentities(Translation :: get('LearningObjectsDeleted'));
			}
			else
			{
				$message = htmlentities(Translation :: get('LearningObjectDeleted'));
			}

			$this->redirect($message, '', array('pid' => null));
		}
	}
}
?>