<?php
/**
 * @package application.menu
 */
require_once dirname(__FILE__).'/../lib/menu_data_manager.class.php';
require_once dirname(__FILE__).'/../lib/menu_item.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
require_once Path :: get_tracking_path() .'lib/events.class.php';
require_once Path :: get_tracking_path() .'install/tracking_installer.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * menu application.
 */
class MenuInstaller extends Installer
{
	private $values;
	
	/**
	 * Constructor
	 */
    function MenuInstaller($values)
    {
    	$this->values = $values;
    	parent :: __construct($values, MenuDataManager :: get_instance());
    }
	/**
	 * Runs the install-script.
	 * @todo This function now uses the function of the RepositoryInstaller
	 * class. These shared functions should be available in a common base class.
	 */
	function install_extra()
	{
		if (!$this->create_basic_menu())
		{
			return false;
		}
		else
		{
			$this->add_message(self :: TYPE_NORMAL, Translation :: get('MenuCreated'));
		}
		
		return true;
	}
	
	function create_basic_menu()
	{
		$applications = FileSystem :: get_directory_content(Path :: get_application_path() . 'lib/', FileSystem :: LIST_DIRECTORIES, false);
		$values = $this->values;
		
		sort($applications);
		
		foreach($applications as $application)
		{
			// TODO: Temporary fix.
			if(isset($values['install_' . $application]) && $application != '.svn')
			{
				$menu_item = new MenuItem();
				$menu_item->set_title(Translation :: get(DokeosUtilities :: underscores_to_camelcase($application)));
				$menu_item->set_application($application);
				$menu_item->set_section($application);
				$menu_item->set_category(0);
				$menu_item->create();
			}
		}
		
		return true;
	}
	
	function get_path()
	{
		return dirname(__FILE__);
	}
}
?>