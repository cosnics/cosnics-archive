<?php
require_once dirname(__FILE__) . '/../../learningobject_form.class.php';
class DocumentForm extends LearningObjectForm
{
	public function DocumentForm($formName, $method = 'post', $action = null)
	{
		parent :: __construct($formName, $method, $action);
	}
	public function build_create_form()
	{
		parent :: build_create_form();
		$this->addElement('file', 'filename', 'Filename');
		$this->addRule('filename',get_lang('DiskQuotaExceeded'),'disk_quota');
		$this->add_submit_button();
	}
	public function build_edit_form($object)
	{
		parent :: build_edit_form($object);
		$this->setDefaults();
		$this->addElement('text', 'path', 'Path');
		$this->addElement('text', 'filename', 'Filename');
		$this->add_submit_button();
	}
	public function setDefaults($defaults = array ())
	{
		$lo = $this->get_learning_object();
		if (isset ($lo))
		{
			$defaults['path'] = $lo->get_path();
			$defaults['filename'] = $lo->get_filename();
		}
		parent :: setDefaults($defaults);
	}
	public function create_learning_object($owner)
	{
		$values = $this->exportValues();
		$file = $_FILES['filename'];
		$filename = strtolower(ereg_replace('[^0-9a-zA-Z\.]','',$file['name']));
		$path = api_get_user_id().'/'.$filename;
		$filename_t = Configuration::get_instance()->get_parameter('general', 'upload_path').$path;
		$dataManager = RepositoryDataManager::get_instance();
		$document = new Document();
		$document->set_owner_id($owner);
		$document->set_title($values['title']);
		$document->set_description($values['description']);
		$document->set_path($path);
		$document->set_filename($filename);
		$document->set_category_id($values['category']);
		$document->create();
		move_uploaded_file($_FILES['filename']['tmp_name'], Configuration::get_instance()->get_parameter('general', 'upload_path').$path);
		return $document;
	}
	public function update_learning_object(& $object)
	{
		$values = $this->exportValues();	
		$object->set_title($values['title']);
		$object->set_description($values['description']);
		$object->set_path($values['path']);
		$object->set_filename($values['filename']);
		$object->set_category_id($values['category']);
		$object->update();
	}
}
?>