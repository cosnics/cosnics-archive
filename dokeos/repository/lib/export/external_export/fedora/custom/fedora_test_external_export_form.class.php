<?php
require_once Path :: get_repository_path() . '/lib/forms/external_export_export_form.class.php';

class FedoraTestExternalExportForm extends ExternalExportExportForm
{
	function FedoraTestExternalExportForm($content_object, $export, $action, $catalogs)
	{
		parent :: ExternalExportExportForm($content_object, $export, $action, $catalogs);
		
		$this->build_fedora_test_form();
	}
	
	private function build_fedora_test_form()
	{ 
	    $this->addElement('html', '<h3>Test</h3>');
	    $this->addElement('html', '<p>Please choose an animal that will be set in the <em><strong>ANIMAL</strong></em> datastream</p>');
	    
	    $licenses = array('' => '', 'tiger' => 'tiger', 'bear' => 'bear', 'mouse' => 'mouse');

	    $this->addElement('select', 'animal', 'My animal', $licenses);
	    
	    $this->addRule('animal', 'An animal is required', 'required');
	}
	
	public function validate()
	{
	    if(parent :: validate())
	    {
	        /*
	         * Store the choosed animal in session in order to use it in FedoraTestExternalExporter
	         */
	        $submitted_values = $this->getSubmitValues();
	        Session :: register('external_export.fedora_test.animal', $submitted_values['animal']);
	        
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	}
	

}
?>