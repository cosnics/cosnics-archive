<?php

/**
 * @package migration.migrationmanager
 */
 
require_once dirname(__FILE__).'/../trackingmanager.class.php';
require_once dirname(__FILE__).'/../trackingmanagercomponent.class.php';
require_once dirname(__FILE__).'/archiver/archivewizard.class.php';

/**
 * Tracking Manager Archiver component which allows the administrator to archive the trackers
 *
 * @author Sven Vanpoucke
 */
class TrackingManagerArchiverComponent extends TrackingManagerComponent 
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		if (!$this->get_user() || !$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$wizard = new ArchiveWizard($this);
		$wizard->run();
	}	
}
?>