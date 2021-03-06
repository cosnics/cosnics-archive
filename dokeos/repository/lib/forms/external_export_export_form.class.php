<?php
require_once dirname(__FILE__) . '/../../../common/html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__) . '/../repository_manager/repository_manager.class.php';
require_once dirname(__FILE__) . '/../repository_data_manager.class.php';

class ExternalExportExportForm extends FormValidator
{
    private $catalogs;
    
    /**
     * @var ContentObject
     */
    private $content_object;
    
    /**
     * @var FedoraExternalExporter
     */
    private $export;
    
    protected function ExternalExportExportForm($content_object, $export, $action, $catalogs)
	{ 
		parent :: __construct('external_export_browser', 'post', $action);
		
		$this->content_object = $content_object;
		$this->export          = $export;
		$this->catalogs        = $catalogs;
		
		//$this->build_form();
		
		//debug($this->catalogs);
	}
	
	/**
	 * Return an instance of ExternalExportExportForm or a child of ExternalExportExportForm
	 * 
	 * @param $content_object ContentObject
	 * @param $export ExternalExport
	 * @param $action string
	 * @param $catalogs	array 
	 * @return ExternalExportExportForm
	 */
	public function get_instance($content_object, $export, $action, $catalogs)
	{
	    $export_type  = strtolower($export->get_type());
	    $catalog_name = strtolower($export->get_catalog_name());
	    
	    $class_name = null;
	    if(file_exists(Path :: get_repository_path() . '/lib/export/external_export/' . strtolower($export_type) . '/custom/' . strtolower($catalog_name)  . '_external_export_form.class.php'))
	    {
	        require_once Path :: get_repository_path() . '/lib/export/external_export/' . strtolower($export_type) . '/custom/' . strtolower($catalog_name)  . '_external_export_form.class.php';
	        $class_name = DokeosUtilities :: underscores_to_camelcase($catalog_name) . 'ExternalExportForm';
	    }
	    else
	    {
	        $class_name = 'ExternalExportExportForm';
	    }
	    
	    if(isset($class_name))
	    {
	        return new $class_name($content_object, $export, $action, $catalogs);
	    }
	    else
	    {
	        throw new Exception('Export form for \'' . $export_type . '\' not found');
	    }
	}
	
	
	protected function build_form()
	{
	    echo '<div>';
	    
	    $this->display_export_confirmation();
	    
	    echo '</div>';
	}
	
	public function display()
	{
	    $this->build_form();
	    
	    parent :: display();
	}
	
	
	public function display_repository_details($external_export)
	{
	    //debug(array($this->export));
	    
	    $table = array();
	    $table[] = '<table border="0" cellpadding="5" cellspacing="0">';
	    
	    if(method_exists($external_export, 'get_title'))
	    {
    	    $table[] = '<tr>';
    	    //$table[] = '<td><h3>' . Translation :: translate('Title') . '</h3></td>';
    	    //$table[] = '<td></td>';
    	    $table[] = '<td colspan="2"><h3>' . $external_export->get_title() . '</h3></td>';
    	    $table[] = '</tr>';
	    }
	    
	    if(method_exists($external_export, 'get_base_url'))
	    {
    	    $table[] = '<tr>';
    	    $table[] = '<td>' . Translation :: translate('BaseURL') . '</td>';
    	    $table[] = '<td>' . $external_export->get_base_url() . '</td>';
    	    $table[] = '</tr>';
	    }
	    
	    $table[] = '</table>';
	    
	    echo implode($table);
	}

	public function display_export_confirmation()
	{
	    echo '<div>';
	    
	    echo '<p>' . str_replace('{ContentObject.title}', $this->content_object->get_title(), Translation :: translate('ExternalExportConfirmationText')) . '</p>';
	    
	    $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Confirm'), array('class' => 'positive update'));
		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		
	    echo '</div>';
	}

	/**
	 * 
	 * @param $repository_uid string
	 * @return void
	 */
	public function display_export_success($repository_uid)
	{
	    echo '<div>';
	    
	    echo '<p>' . str_replace('{ExternalRepository.uid}', $repository_uid, Translation :: translate('ExternalExportSuccess')) . '</p>';
	    
	    echo '</div>';
	}
	
}
?>