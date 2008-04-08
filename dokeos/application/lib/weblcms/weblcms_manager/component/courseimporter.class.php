<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
require_once dirname(__FILE__).'/../../course/courseimportform.class.php';

/**
 * Weblcms component allows the use to import a course
 */
class WeblcmsCourseImporterComponent extends WeblcmsComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		global $this_section;
		$this_section='platform_admin';
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CourseCreateCsv')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$form = new CourseImportForm(CourseImportForm :: TYPE_IMPORT, $this->get_url());
		
		if($form->validate())
		{
			$success = $form->import_courses();
			$this->redirect(null, Translation :: get($success ? 'CourseCreatedCsv' : 'CourseNotCreatedCsv'). '<br />' .$form->get_failed_csv(), ($success ? false : true));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_extra_information();
			$this->display_footer();
		}
	}
	
	function display_extra_information()
	{
		$html = array();
		$html[] = '<p>'. Translation :: get('CSVMustLookLike') .' ('. Translation :: get('MandatoryFields') .')</p>';
		$html[] = '<blockquote>';
		$html[] = '<pre>';
		$html[] = '<b>Code</b>;<b>Title</b>;<b>CourseCategory</b>;<b>Teacher</b>';
		$html[] = 'BIO0015;Biology;BIO;username';
		$html[] = '</pre>';
		$html[] = '</blockquote>';
		
		echo implode($html, "\n");		
	}
}
?>