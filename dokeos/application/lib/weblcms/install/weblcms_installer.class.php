<?php
/**
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/../weblcmsdatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
require_once Path :: get_tracking_path() .'lib/events.class.php';
require_once Path :: get_tracking_path() .'install/tracking_installer.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * weblcms application.
 */
class WeblcmsInstaller extends Installer {

	private $wdm;
	/**
	 * Constructor
	 */
    function WeblcmsInstaller() {
    	$this->wdm = WeblcmsDataManager :: get_instance();
    }
	/**
	 * Runs the install-script.
	 */
	function install()
	{
		$dir = dirname(__FILE__);
		$files = FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
		foreach($files as $file)
		{
			if ((substr($file, -3) == 'xml'))
			{
				if (!$this->create_storage_unit($file))
				{
					return array('success' => false, 'message' => $this->retrieve_message());
				}
			}
		}
		
		if (!$this->create_default_categories_in_weblcms())
		{
			return array('success' => false, 'message' => $this->retrieve_message());
		}
		else
		{
			$this->add_message(Translation :: get('DefaultWeblcmsCategoriesCreated'));
		}
		
		if(!$this->register_trackers())
		{
			return array('success' => false, 'message' => $this->retrieve_message());
		}
		
		$success_message = '<span style="color: green; font-weight: bold;">' . Translation :: get('ApplicationInstallSuccess') . '</span>';
		$this->add_message($success_message);
		return array('success' => true, 'message' => $this->retrieve_message());
	}
	
	
	/**
	 * Registers the trackers, events and creates the storage units for the trackers
	 */
	function register_trackers()
	{
		$dir = dirname(__FILE__) . '/../trackers/tracker_tables';
		$files = FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
		$trkinstaller = new TrackingInstaller();
		
		foreach($files as $file)
		{
			if ((substr($file, -3) == 'xml'))
			{
				if (!$trkinstaller->create_storage_unit($file))
				{
					return false;
				}
			}
		}
		
		$weblcms_publication_events = array();
		$weblcms_publication_events[] = Events :: create_event('create_publication', 'weblcms');
		$weblcms_publication_events[] = Events :: create_event('update_publication', 'weblcms');
		$weblcms_publication_events[] = Events :: create_event('delete_publication', 'weblcms');
		$weblcms_publication_events[] = Events :: create_event('create_publication_category', 'weblcms');
		$weblcms_publication_events[] = Events :: create_event('update_publication_category', 'weblcms');
		$weblcms_publication_events[] = Events :: create_event('delete_publication_category', 'weblcms');
		
		$weblcms_course_events = array();
		$weblcms_course_events[] = Events :: create_event('create_course', 'weblcms');
		$weblcms_course_events[] = Events :: create_event('update_course', 'weblcms');
		$weblcms_course_events[] = Events :: create_event('delete_course', 'weblcms');
		$weblcms_course_events[] = Events :: create_event('subscribe_user_to_course', 'weblcms');
		$weblcms_course_events[] = Events :: create_event('subscribe_class_to_course', 'weblcms');
		$weblcms_course_events[] = Events :: create_event('unsubscribe_user_to_course', 'weblcms');
		$weblcms_course_events[] = Events :: create_event('unsubscribe_class_to_course', 'weblcms');
		$weblcms_course_events[] = Events :: create_event('create_course_category', 'weblcms');
		$weblcms_course_events[] = Events :: create_event('update_course_category', 'weblcms');
		$weblcms_course_events[] = Events :: create_event('delete_course_category', 'weblcms');
		$weblcms_course_events[] = Events :: create_event('create_course_user_category', 'weblcms');
		$weblcms_course_events[] = Events :: create_event('update_course_user_category', 'weblcms');
		$weblcms_course_events[] = Events :: create_event('delete_course_user_category', 'weblcms');
		
		$path = '/classgroup/trackers/';
		
		$dir = dirname(__FILE__) . '/../trackers/';
		$files = FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
		foreach($files as $file)
		{
			if ((substr($file, -3) == 'php'))
			{
				$filename = basename($file);
				$filename = substr($filename, 0, strlen($filename) - strlen('.class.php'));
				
				$tracker = $trkinstaller->register_tracker($path, $filename);
				if (!$tracker)
				{
					return false;
				}
				else
				{
					if($tracker->get_class() == 'WeblcmsPublicationChangesTracker')
					{
						foreach($weblcms_publication_events as $event)
						{
							if(!$trkinstaller->register_tracker_to_event($tracker, $event)) return false;
						}
						
						$this->add_message(Translation :: get('TrackersRegistered') . ': ' . $filename);
						continue;
					}
					if($tracker->get_class() == 'WeblcmsCourseChangesTracker')
					{
						foreach($weblcms_course_events as $event)
						{
							if(!$trkinstaller->register_tracker_to_event($tracker, $event)) return false;
						}
						
						$this->add_message(Translation :: get('TrackersRegistered') . ': ' . $filename);
						continue;
					}
					else
						echo($tracker->get_class());
				}
				
				
			}
		}
		
		return true;
	}
	
	/**
	 * Parses an XML file and sends the request to the database manager
	 * @param String $path
	 */
	function create_storage_unit($path)
	{
		$storage_unit_info = parent::parse_xml_file($path);
		$this->add_message(Translation :: get('StorageUnitCreation') . ': <em>'.$storage_unit_info['name'] . '</em>');
		if (!$this->wdm->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']))
		{
			$error_message = '<span style="color: red; font-weight: bold;">' . Translation :: get('StorageUnitCreationFailed') . ': <em>'.$storage_unit_info['name'] . '</em></span>';
			$this->add_message($error_message);
			$this->add_message(Translation :: get('ApplicationInstallFailed'));
			$this->add_message(Translation :: get('PlatformInstallFailed'));
			
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function create_default_categories_in_weblcms()
	{
		//Creating Language Skills
		$cat = new CourseCategory();
		$cat->set_name('Language skills');
		$cat->set_code('LANG');
		$cat->set_parent('0');
		$cat->set_tree_pos('1');
		$cat->set_children_count('0');
		$cat->set_auth_course_child('1');
		$cat->set_auth_cat_child('1');
		if (!$cat->create())
		{
			return false;
		}
	
		//creating PC Skills
		$cat = new CourseCategory();
		$cat->set_name('PC skills');
		$cat->set_code('PC');
		$cat->set_parent('0');
		$cat->set_tree_pos('2');
		$cat->set_children_count('0');
		$cat->set_auth_course_child('1');
		$cat->set_auth_cat_child('1');
		if (!$cat->create())
		{
			return false;
		}
	
		//creating Projects
		$cat = new CourseCategory();
		$cat->set_name('Projects');
		$cat->set_code('PROJ');
		$cat->set_parent('0');
		$cat->set_tree_pos('3');
		$cat->set_children_count('0');
		$cat->set_auth_course_child('1');
		$cat->set_auth_cat_child('1');
		if (!$cat->create())
		{
			return false;
		}
		
		return true;
	}
}
?>