<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__).'/../personal_calendar_manager.class.php';
require_once dirname(__FILE__).'/../personal_calendar_manager_component.class.php';
require_once Path :: get_repository_path() . 'lib/import/content_object_import.class.php';

class PersonalCalendarManagerIcalImporterComponent extends PersonalCalendarManagerComponent
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
    	$form = $this->build_importing_form();
		if ($form->validate())
		{
			$object = $this->import_ical($form);
			$this->redirect(Translation :: get('QtiImported'), false, array(PersonalCalendarManager :: PARAM_ACTION => PersonalCalendarManager :: ACTION_CREATE_PUBLICATION, 'object' => $object));
		}
		else 
		{
	    	$trail = new BreadCrumbTrail();
			$trail->add(new BreadCrumb($this->get_url(array(PersonalCalendarManager :: PARAM_ACTION => null)), Translation :: get('BrowsePersonalCalendar')));
		   	$trail->add(new BreadCrumb($this->get_url(array(PersonalCalendarManager :: PARAM_ACTION => PersonalCalendarManager :: ACTION_IMPORT_ICAL)), Translation :: get('ImportICal')));
			
		   	$this->display_header($trail, true);
	
			echo $form->toHtml();
			$this->display_footer();
		}
    }
    
	function build_importing_form()
    {
    	$url = $this->get_url(array(PersonalCalendarManager :: PARAM_ACTION => PersonalCalendarManager :: ACTION_IMPORT_ICAL));
    	$form = new FormValidator('qti_import', 'post', $url);
    	
    	$this->categories[0] = Translation :: get('MyRepository');
    	$this->retrieve_categories(0, 1);
    	$categories = $this->categories;
    	
    	$form->addElement('select', 'category', Translation :: get('Category'), $categories);
    	$form->addElement('file', 'file', sprintf(Translation :: get('FileName'), ini_get('upload_max_filesize')));

    	$allowed_upload_types = array ('ics');
		$form->addRule('file', Translation :: get('OnlyIcsAllowed'), 'filetype', $allowed_upload_types);

		$buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Import'), array('class' => 'positive import'));

		$form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		return $form;
    }
    
    function import_ical($form)
    {
    	$values = $form->exportValues();
    	$category = $values['category'];
    	
    	$file = $_FILES['file'];
    	$user = $this->get_user();

    	$importer = ContentObjectImport ::factory('ical', $file, $user, $category);
    	$result = $importer->import_content_object();
    	
    	return $result;
    }
    
    private $categories;
    
    function retrieve_categories($parent_id, $level)
    {
    	$conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $this->get_user_id());
    	$conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_PARENT, $parent_id);
    	$condition = new AndCondition($conditions);
    	
    	$category_list = RepositoryDataManager :: get_instance()->retrieve_categories($condition);
    	
    	while($category = $category_list->next_result())
    	{
    		$this->categories[$category->get_id()] = str_repeat('--', $level) . ' ' . $category->get_name();
    		$this->retrieve_categories($category->get_id(), ($level + 1));
    	}
    }
    
    
}
?>