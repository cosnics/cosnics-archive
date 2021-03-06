<?php
/**
 * @package users.lib.usermanager
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../user.class.php';
require_once dirname(__FILE__).'/../user_data_manager.class.php';

ini_set("max_execution_time", -1);
ini_set("memory_limit", -1);

class UserExportForm extends FormValidator {

	const TYPE_EXPORT = 1;

	private $current_tag;
	private $current_value;
	private $user;
	private $users;


	/**
	 * Creates a new UserImportForm
	 * Used to export users to a file
	 */
    function UserExportForm($form_type, $action) {
    	parent :: __construct('user_export', 'post', $action);

		$this->form_type = $form_type;
		$this->failedcsv = array();
		if ($this->form_type == self :: TYPE_EXPORT)
		{
			$this->build_exporting_form();
		}
    }

    function build_exporting_form()
    {
    	$this->addElement('select', 'file_type', Translation :: get('OutputFileType'),Export::get_supported_filetypes(array('ical')));
		//$this->addElement('submit', 'user_export', Translation :: get('Ok'));
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Export'), array('class' => 'positive export'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		$this->setDefaults(array('file_type'=>'csv'));

    }
}
?>