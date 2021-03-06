<?php
require_once dirname(__FILE__) . '/external_repository_export_component.class.php';
require_once dirname(__FILE__) . '/external_repository_export_export.class.php';
require_once dirname(__FILE__) . '/../../forms/external_export_browser_form.class.php';
require_once Path :: get_repository_path() . '/lib/export/external_export/base_external_exporter.class.php';

class RepositoryManagerExternalRepositoryMetadataReviewerComponent extends RepositoryManagerMetadataComponent 
{
    function run() 
	{
	    if($this->check_content_object_from_params())
		{
    	    $content_object = $this->get_content_object_from_params();
    	    
    	    $trail = new BreadcrumbTrail(false);
    	    $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id())), $content_object->get_title()));
    	    $trail->add(new BreadCrumb($this->get_url(array(RepositoryManager::PARAM_ACTION => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_BROWSE, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id())), Translation :: translate('ExternalExport')));
	    
		    $metadata_type = $this->get_metadata_type();
			
            $form   = null;
            $mapper = null;
            switch ($metadata_type) 
            {
                case self :: METADATA_FORMAT_LOM:
                                        
                    $mapper = new IeeeLomMapper($content_object);
                    $form = new MetadataLOMEditForm($content_object->get_id(), $mapper, $this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id(), RepositoryManagerExternalRepositoryExportComponent :: PARAM_EXPORT_ID => Request :: get(RepositoryManagerExternalRepositoryExportComponent :: PARAM_EXPORT_ID))), $this->get_catalogs());
                    break;
                
                /*
                 * Implementation of another Metadata type than LOM 
                 * could be done here
                 */
            }
            
            //do not put it here as it would block the redirection below
//            $this->display_header($trail, false, true);
            
            if(isset($form))
            {                
                $this->add_missing_fields($form);
                
                $form->build_editing_form();
             
                if($form->must_save())
                {
                    if(isset($mapper))
                    {
                        if(!$mapper->save_submitted_values($form->getSubmitValues()))
                        {
                            $this->display_header($trail, false, true);
                            $this->display_error_message($mapper->get_errors_as_html());
                        }
                        else
                        {
                            //$this->display_message(Translation :: translate('MetadataSaved'));
                            
                            /*
                             * Redirect to the export page and force the export
                             */
                            Redirect :: url(array(Application :: PARAM_APPLICATION => RepositoryManager :: APPLICATION_NAME, 
                                                    Application :: PARAM_ACTION => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_EXPORT, 
                                                    RepositoryManagerExternalRepositoryExportComponent :: PARAM_EXPORT_ID => Request :: get(RepositoryManagerExternalRepositoryExportComponent :: PARAM_EXPORT_ID), 
                                                    RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id(), 
                                                    RepositoryManagerExternalRepositoryExportExportComponent :: PARAM_FORCE_EXPORT => 1));
                        
//                        $form->set_constant_values($mapper->get_constant_values(), true);
//                        $form->display();
                        }
                    }
                    else
                    {
                        $this->display_header($trail, false, true);
                        $this->display_error_message(Translation :: translate('MetadataMapperNotFound'));
                    }
                }
                else
                {
                    $this->display_header($trail, false, true);
                    $form->display();
                }
            }
    		
    		$this->display_footer();
		}
		else
		{
		    throw new Exception('The object to export is undefined');
		}
	}
	
	/**
	 * Return the metadata type that is requested.
	 * 
	 * @return string The type of metadata requested. Default returned is LOM.
	 */
	function get_metadata_type()
	{
	    $metadata_type = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE); 
        if(!isset($metadata_type))
        {
            $metadata_type = self :: METADATA_FORMAT_LOM;
        }
        
        return $metadata_type;
	}
	
	
	/**
	 * Check if some metadata are missing for the learning object and pre-add the necessary fields to the form before showing it.
	 * It also set an error message to explain what fields are missing.
	 * 
	 * Note: to check what fields are missing, a session variable is used  
	 * 
	 * @param $form MetadataLOMEditForm
	 * @return void
	 */
	private function add_missing_fields($form)
	{
	    $missing_infos = Session :: retrieve(BaseExternalExporter :: SESSION_MISSING_FIELDS);
	    foreach ($missing_infos as $fieldname => $field_info) 
	    {
	    	//debug($field_info);
	    	
	    	$form->add_info_message($fieldname, $field_info['message']);
	    	
	    	foreach ($field_info['fields'] as $field_value) 
	    	{
	    		switch($fieldname)
	    		{
	    		    case 'general.identifier':
	    		        $form->add_identifier();
	    		        break;
	    		        
	    		    case 'general.title':
	    		        $form->add_title();
	    		        break;
	    		        
	    		    case 'general.language':
	    		        $form->add_general_language();
	    		        break;
	    		    
	    		    case 'general.description':
	    		        $form->add_description_string();
	    		        break;
	    		        
	    		    case 'lifeCycle.entity':
	    		        $form->add_lifeCycle_entity();
	    		        break;
	    		        
	    		    case 'rights.description':
	    		        $form->add_rights_description();
	    		        break;
	    		}
	    	}
	    }
	    
	    /*
	     * Destroy the missing fields session in order to be able to post back the form without adding new missing fields
	     */
	    Session :: unregister(BaseExternalExporter :: SESSION_MISSING_FIELDS);
	    
//        $form->add_identifier('ta mère', 'en short');
//        $form->add_title('ton père en français', 'fr');
//        $form->add_general_language('en');
//        $form->add_general_language('de');
//        $form->add_description_string('nono le petit robot', $lang = 'it');
//        $form->add_lifeCycle_entity();
//        $form->add_rights_description('Klüüüüüüüük !!!!', 'fr');
	}
	
}
?>