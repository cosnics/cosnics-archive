<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
require_once dirname(__FILE__).'/../../course/courseusercategoryform.class.php';
require_once dirname(__FILE__).'/courseusercategorybrowser/courseusercategorybrowsertable.class.php';

/**
 * Weblcms component allows the user to add personal categories to his or her course list.
 */
class WeblcmsUserCategoryComponent extends WeblcmsComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$component_action = $this->get_parameter(Weblcms::PARAM_COMPONENT_ACTION);
		
		switch($component_action)
		{
			case 'edit':
				$this->edit_course_user_category();
				break;
			case 'delete':
				$this->delete_course_user_category();
				break;
			default :
				$this->add_course_user_category();
		}
	}
	
	function add_course_user_category()
	{
		$courseusercategory = new CourseUserCategory();
		
		$form = new CourseUserCategoryForm(CourseUserCategoryForm :: TYPE_CREATE, $courseusercategory, $this->get_url());
		
		if($form->validate())
		{
			$success = $form->create_course_user_category();
			$this->redirect(null, get_lang($success ? 'CourseUserCategoryAdded' : 'CourseUserCategoryNotAdded'), ($success ? false : true));
		}
		else
		{
			$this->display_header_user_categories();
			echo '<h3>'. get_lang('CreateCourseUserCategory') .'</h3>';
			$form->display();
			$this->display_footer();
		}
	}
	
	function edit_course_user_category()
	{
		$course_user_category_id = $_GET[Weblcms :: PARAM_COURSE_USER_CATEGORY_ID];
		$courseusercategory = $this->retrieve_course_user_category($course_user_category_id);
		
		$form = new CourseUserCategoryForm(CourseUserCategoryForm :: TYPE_EDIT, $courseusercategory, $this->get_url(array(Weblcms :: PARAM_COURSE_USER_CATEGORY_ID => $course_user_category_id)));
		
		if($form->validate())
		{
			$success = $form->update_course_user_category();
			$this->redirect(null, get_lang($success ? 'CourseUserCategoryUpdated' : 'CourseUserCategoryNotUpdated'), ($success ? false : true), array(Weblcms :: PARAM_COMPONENT_ACTION => 'add', Weblcms :: PARAM_COURSE_USER_CATEGORY_ID => $course_user_category_id));
		}
		else
		{
			$this->display_header_user_categories();
			echo '<h3>'. get_lang('EditCourseUserCategory') .'</h3>';
			$form->display();
			$this->display_footer();
		}
	}
	
	function delete_course_user_category()
	{
		$course_user_category_id = $_GET[Weblcms :: PARAM_COURSE_USER_CATEGORY_ID];
		$courseusercategory = $this->retrieve_course_user_category($course_user_category_id);
		
		$success = $courseusercategory->delete();
		$this->redirect(null, get_lang($success ? 'CourseUserCategoryDeleted' : 'CourseUserCategoryNotDeleted'), ($success ? false : true), array(Weblcms :: PARAM_COMPONENT_ACTION => 'add'));
	}
	
	function display_header_user_categories()
	{
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('CourseCategoryManagement'));
		$this->display_header($breadcrumbs);
		
		$table = new CourseUserCategoryBrowserTable($this, null, null, null);
		echo $table->as_html();
	}
}
?>