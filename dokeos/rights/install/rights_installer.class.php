<?php
/**
 * @package application.weblcms
 */
require_once dirname(__FILE__).'/../lib/rightsdatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';
require_once Path :: get_library_path().'filesystem/filesystem.class.php';
require_once Path :: get_tracking_path() .'lib/events.class.php';
require_once Path :: get_tracking_path() .'install/tracking_installer.class.php';
/**
 *	This installer can be used to create the storage structure for the
 * weblcms application.
 */
class RightsInstaller extends Installer
{
	/**
	 * Constructor
	 */
    function RightsInstaller($values)
    {
    	parent :: __construct($values, RightsDataManager :: get_instance());
    }
	/**
	 * Runs the install-script.
	 * @todo This function now uses the function of the RepositoryInstaller
	 * class. These shared functions should be available in a common base class.
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
					return false;
				}
			}
		}
		
		if(!$this->register_trackers())
		{
			return false;
		}
		
		return $this->installation_successful();
	}
}
?>