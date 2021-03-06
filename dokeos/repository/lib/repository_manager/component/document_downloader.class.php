<?php
/**
 * $Id: editor.class.php 21345 2009-06-10 13:15:00Z MichaelKyndt $
 * @package repository.repositorymanager
 *
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
/**
 * Repository manager component to edit an existing learning object.
 */
class RepositoryManagerDocumentDownloaderComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$object_id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
		if(!$object_id)
		{
			$this->display_header();
			$this->display_error_message(Translation :: get('NoContentObjectSelected'));
			$this->display_footer();
			exit();
		}
		
		
		$lo = $this->retrieve_content_object($object_id);
		if($lo->get_type() != 'document')
		{
			$this->display_header();
			$this->display_error_message(Translation :: get('ContentObjectMustBeDocument'));
			$this->display_footer();
			exit();
		}
		
		if(Request :: get('display') == 1)
			$this->display_document($lo);
		else
			$lo->send_as_download();
	}
	
	function display_document($lo)
	{
		$name = $lo->get_filename();
		
		$types = array('text/html' => array('.html', '.htm'), 'text/plain' => array('.txt'), 
					 		'image/' => array('.jpg', '.bmp', '.jpeg', '.png'));
		
		foreach($types as $type => $extensions)
		{
			foreach($extensions as $extension)
			{
				$len = strlen($extension) * -1;
				if(substr(strtolower($name), $len) == $extension)
				{
					if($type == 'image/')
						$type .= substr($extension, 1);
					
					$bool = true;
					break;
				}
			}
			
			if($bool)
				break;
		}
		
		header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
		header('Content-Type: ' . $type);
		header('Content-Description: ' . $lo->get_filename());
		readfile($lo->get_full_path());
	}
}
?>