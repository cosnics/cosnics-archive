<?php
/**
 * @package install.installmanager
 * 
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__).'/../install_manager.class.php';
require_once dirname(__FILE__).'/../install_manager_component.class.php';
require_once dirname(__FILE__).'/inc/install_wizard.class.php';
/**
 * Installer install manager component which allows the user to install the platform
 */
class InstallManagerInstallerComponent extends InstallManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$wizard = new InstallWizard($this);
		$wizard->run();
	}
}
?>
