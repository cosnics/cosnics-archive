<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once Path :: get_library_path().'import/import.class.php';
require_once dirname(__FILE__).'/course.class.php';
require_once dirname(__FILE__).'/../category_manager/course_category.class.php';
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';

ini_set("max_execution_time", -1);
ini_set("memory_limit", -1);

class CourseImportForm extends FormValidator {
	
	const TYPE_IMPORT = 1;
	
	private $failedcsv;
	private $udm;

    function CourseImportForm($form_type, $action) {
    	parent :: __construct('course_import', 'post', $action);
    	
		$this->form_type = $form_type;
		$this->failedcsv = array();
		if ($this->form_type == self :: TYPE_IMPORT)
		{
			$this->build_importing_form();
		}
    }
    
    function build_importing_form()
    {
    	$this->addElement('file', 'file', Translation :: get('FileName'));
		//$this->addElement('submit', 'course_import', Translation :: get('Ok'));
		$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Import'), array('class' => 'positive import'));
	//	$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
    function import_courses()
    {
    	$values = $this->exportValues();

    	$csvcourses = Import :: csv_to_array($_FILES['file']['tmp_name']);
    	$failures = 0;
    	
    	foreach ($csvcourses as $csvcourse)
    	{ 
    		if ($this->validate_data($csvcourse))
    		{ 
    			$teacher_info = $this->get_teacher_info($csvcourse[Course :: PROPERTY_TITULAR]);
    			$cat = WeblcmsDataManager :: get_instance()->retrieve_course_categories(new EqualityCondition('name', $csvcourse[Course :: PROPERTY_CATEGORY]))->next_result();
    			$catid = $cat?$cat->get_id():0;
    			
    			$course = new Course();
    			
    			//$course->set_id($csvcourse[Course :: PROPERTY_ID]);
    			$course->set_visual($csvcourse['code']);
    			$course->set_name($csvcourse[Course :: PROPERTY_NAME]);
    			$course->set_language('english');
    			$course->set_category($catid);
    			$course->set_titular($teacher_info->get_id());
 			
    			if ($course->create())
    			{ 
    				// TODO: Temporary function pending revamped roles&rights system
    				//add_course_role_right_location_values($course->get_id());
    				$wdm = WeblcmsDataManager :: get_instance();
    				if ($wdm->subscribe_user_to_course($course, '1', '1', $teacher_info->get_id()))
    				{
    					
    				}
    				else
    				{
    					$failures++;
    					$this->failedcsv[] = implode($csvcourse, ';');
    				}
    			}
    			else
    			{
    				$failures++;
    				$this->failedcsv[] = implode($csvcourse, ';');
    			}
    		}
    		else
    		{
    			$failures++;
    			$this->failedcsv[] = implode($csvcourse, ';');
    			break;
    		}
    	}
    	
    	if ($failures > 0)
    	{
    		return false;
    	}
    	else
    	{
    		return true;
    	}
    }
    
    // TODO: Temporary solution pending implementation of user object
    function get_teacher_info($user_name)
    {
    	$udm = $this->udm;
    	$udm = UserDataManager :: get_instance();
    	if (!$udm->is_username_available($user_name))
    	{
    		return $udm->retrieve_user_info($user_name);
    	}
    	else
    	{
    		return null;
    	}
    }
    
    function get_failed_csv()
    {
    	return implode($this->failedcsv, '<br />');
    }
    
    function validate_data($csvcourse)
    {
    	$failures = 0;
    	$wdm = WeblcmsDataManager :: get_instance();
    	
		//1. check if mandatory fields are set

		//2. check if code isn't in use
		/*if ($wdm->is_course($csvcourse[Course :: PROPERTY_ID]))
		{
			$failures++;
		}*/

    	if($csvcourse['teacher'])
    	{
    		$csvcourse[Course :: PROPERTY_TITULAR] = $csvcourse['teacher'];
    	}
    	
		//3. check if teacher exists
		$teacher_info = $this->get_teacher_info($csvcourse[Course :: PROPERTY_TITULAR]);
		if (!isset($teacher_info))
		{
			$failures++;
		}

		//4. check if category exists
		if (!$this->is_course_category($csvcourse[Course :: PROPERTY_CATEGORY]))
		{
			$failures++;
		}
	
		if ($failures > 0)
		{
			return false;
		}
		else
		{
    		return true;
		}
    }
    
    private function is_course_category($category_name)
    {
    	$cat = WeblcmsDataManager :: get_instance()->retrieve_course_categories(new EqualityCondition('name', $category_name))->next_result();
    	if($cat) return true;
    	
    	return false;
    }
}
?>