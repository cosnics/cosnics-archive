<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/course.class.php';
require_once dirname(__FILE__).'/courseusercategory.class.php';

class CourseUserCategoryForm extends FormValidator {
	
	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	
	private $courseusercategory;
	private $user;

    function CourseUserCategoryForm($form_type, $courseusercategory, $user, $action) {
    	parent :: __construct('course_settings', 'post', $action);
    	
    	$this->courseusercategory = $courseusercategory;
    	$this->user = $user;
    	
		$this->form_type = $form_type;
		if ($this->form_type == self :: TYPE_EDIT)
		{
			$this->build_editing_form();
		}
		elseif ($this->form_type == self :: TYPE_CREATE)
		{
			$this->build_creation_form();
		}
		
		$this->setDefaults();
    }
    
    function build_basic_form()
    {
		$this->addElement('text', CourseUserCategory :: PROPERTY_TITLE, Translation :: get_lang('Title'), array("maxlength" => 50));
		$this->addRule(CourseUserCategory :: PROPERTY_TITLE, Translation :: get_lang('ThisFieldIsRequired'), 'required');
		
		$this->addElement('submit', 'course_user_category', Translation :: get_lang('Ok'));
    }
    
    function build_editing_form()
    {
    	$courseusercategory = $this->courseusercategory;
    	$parent = $this->parent;
    	
    	$this->build_basic_form();
    	
    	$this->addElement('hidden', CourseUserCategory :: PROPERTY_ID);
    }
    
    function build_creation_form()
    {
    	$this->build_basic_form();
    }
    
    function update_course_user_category()
    {
    	$courseusercategory = $this->courseusercategory;
    	$values = $this->exportValues();
    	
    	$courseusercategory->set_title($values[CourseUserCategory :: PROPERTY_TITLE]);
    	
    	return $courseusercategory->update();
    }
    
    function create_course_user_category()
    {
    	$courseusercategory = $this->courseusercategory;
    	$values = $this->exportValues();
    	
    	$courseusercategory->set_id($values[CourseUserCategory :: PROPERTY_ID]);
    	$courseusercategory->set_title($values[CourseUserCategory :: PROPERTY_TITLE]);
    	$courseusercategory->set_user($this->user->get_user_id());
    	
    	return $courseusercategory->create();
    }
    
	/**
	 * Sets default values. Traditionally, you will want to extend this method
	 * so it sets default for your learning object type's additional
	 * properties.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$courseusercategory = $this->courseusercategory;
		$defaults[CourseUserCategory :: PROPERTY_TITLE] = $courseusercategory->get_title();
		parent :: setDefaults($defaults);
	}
}
?>