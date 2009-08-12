<?php
/**
 * $Id: document_publication_list_renderer.class.php 22452 2009-07-29 12:04:00Z vanpouckesven $
 * Document tool - list renderer
 * @package application.weblcms.tool
 * @subpackage document
 */
require_once dirname(__FILE__).'/../../../../browser/list_renderer/table_learning_object_publication_list_renderer.class.php';
class GeolocationPublicationListRenderer extends TableLearningObjectPublicationListRenderer
{
    function GeolocationPublicationListRenderer($browser)
    {
    	parent :: __construct($browser);
    	$column = 0;
    	if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
    	{
    		$this->set_header($column++, '', false);
    	}
    	$this->set_header($column++, Translation :: get('Title'), false);
		$this->set_header($column++, Translation :: get('Description'), false);
		$this->set_header($column++, Translation :: get('PublishedOn'), false);
		$this->set_header($column++, Translation :: get('PublishedBy'), false);
		$this->set_header($column++, Translation :: get('PublishedFor'), false);
    	//if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
    	{
    		$this->set_header($column++, '', false);
    	}
    }

    /*function render_title($publication) 
    {
    	$html[] = '<a href="'.htmlentities($publication->get_learning_object()->get_url()).'" style="float:left;margin-right: 20px;">'.parent::render_title($publication).'</a>';
    	$download_parameters[Tool :: PARAM_ACTION] = DocumentTool :: ACTION_DOWNLOAD;
    	$download_parameters[Tool :: PARAM_PUBLICATION_ID] =  $publication->get_id();
    	$html[] = '<a href="'.$this->get_url($download_parameters).'"><img src="'.Theme :: get_common_image_path().'action_save.png" alt="" style="float: right;"/></a>';
    	return implode("\n",$html);
    }*/
    
    function render_description($publication)
    {
    	return strip_tags($publication->get_learning_object()->get_description());
    }
}
?>