<?php
require_once dirname(__FILE__) . '/external_repository_export_component.class.php';
require_once dirname(__FILE__) . '/../../forms/external_export_browser_form.class.php';

class RepositoryManagerExternalRepositoryExportBrowserComponent extends RepositoryManagerExternalRepositoryExportComponent 
{
    function run() 
	{
		if($this->check_content_object_from_params())
		{
		    $content_object = $this->get_content_object_from_params();
	    
    	    $trail = new BreadcrumbTrail(false);
    	    $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager::PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager::PARAM_CONTENT_OBJECT_ID => $content_object->get_id())), $content_object->get_title()));
    	    $trail->add(new BreadCrumb(null, Translation::translate('ExternalExport')));
	    
		    $this->display_header($trail, false, true);
		    
    	    $form = new ExternalExportBrowserForm(Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID), '', $this->get_catalogs());
    		$form->display();
    		
    		$this->display_footer();
		}
		else
		{
		    throw new Exception('The object to export is undefined');
		}
	}
	
	
}


?>