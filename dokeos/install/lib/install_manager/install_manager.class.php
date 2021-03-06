<?php
/**
 * @package install.installmanager
 */
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/install_manager_component.class.php';
require_once dirname(__FILE__).'/../install_data_manager.class.php';
require_once Path :: get_library_path() . 'core_application.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
/**
 * An install manager provides some functionalities to the end user to install
 * his Dokeos platform
 *
 * @author Hans De Bisschop
 */
class InstallManager extends CoreApplication
{
	const APPLICATION_NAME = 'install';

   /**
    * Constant defining an action of the repository manager.
 	*/
	const ACTION_INSTALL_PLATFORM = 'install';

   /**
    * Property of this repository manager.
 	*/

	private $breadcrumbs;
	/**
	 * Constructor
	 * @param int $user_id The user id of current user
	 */
	function InstallManager()
	{
		parent :: __construct(null);
	}

	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}

	/**
	 * Run this repository manager
	 */
	function run()
	{
		/*
		 * Only setting breadcrumbs here. Some stuff still calls
		 * forceCurrentUrl(), but that should not affect the breadcrumbs.
		 */
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_INSTALL_PLATFORM :
				$component = InstallManagerComponent :: factory('Installer', $this);
				break;
			default :
				$this->set_action(self :: ACTION_INSTALL_PLATFORM);
				$component = InstallManagerComponent :: factory('Installer', $this);
		}
		$component->run();
	}

	/**
	 * Displays the header.
	 * @param array $breadcrumbs Breadcrumbs to show in the header.
	 * @param boolean $display_search Should the header include a search form or
	 * not?
	 */
	function display_header()
	{
		$this->display_header_content();
	}

	function display_header_content()
	{
		global $dokeos_version;
		$output = array();

		$output[] = '<!DOCTYPE html
		     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$output[] = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
		$output[] = '<head>'."\n";
		$output[] = '<title>-- ' .$dokeos_version . ' Installation --</title>'."\n";
		$output[] = '<link rel="stylesheet" href="../layout/aqua/plugin/jquery/jquery.css" type="text/css"/>'."\n";
		$output[] = '<link rel="stylesheet" href="../layout/aqua/css/common.css" type="text/css"/>'."\n";
		$output[] = '<link rel="stylesheet" href="../layout/aqua/css/install.css" type="text/css"/>'."\n";
		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.min.js"></script>'."\n";
		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.dimensions.min.js"></script>'."\n";

		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.tabula.js"></script>'."\n";
		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.tablednd.js"></script>'."\n";
		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.ui.min.js"></script>'."\n";
		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.ui.tabs.paging.js"></script>'."\n";
		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.simplemodal.js"></script>'."\n";
		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.treeview.pack.js"></script>'."\n";

		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.treeview.async.js"></script>'."\n";
		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.timeout.interval.idle.js"></script>'."\n";
		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.mousewheel.min.js"></script>'."\n";
		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.scrollable.pack.js"></script>'."\n";
		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.xml2json.pack.js"></script>'."\n";
		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.json.js"></script>'."\n";

		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.iphone.checkboxes.js"></script>'."\n";
		$output[] = '<script type="text/javascript" src="../plugin/jquery/jquery.textarearesizer.js"></script>'."\n";
		
		$output[] = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'."\n";
		$output[] = '</head>'."\n";
		$output[] = '<body dir="'. Translation :: get('text_dir') .'">' . "\n";

		$output[] = '<!-- #outerframe container to control some general layout of all pages -->'."\n";
		$output[] = '<div id="outerframe">'."\n";

		$output[] = '<a name="top"></a>';
		$output[] = '<div id="header">  <!-- header section start -->';
		$output[] = '<div id="header1"> <!-- top of banner with institution name/hompage link -->';
		$output[] = '<div class="banner"><div class="logo"></div></div>';
		$output[] = '<div class="clear">&nbsp;</div>';
		$output[] = '</div> <!-- end of #header1 -->';
		$output[] = '<div class="clear">&nbsp;</div>';
		$output[] = '</div> <!-- end of the whole #header section -->';

		$output[] = '<div id="main"> <!-- start of #main wrapper for #content and #menu divs -->'."\n";
		$output[] = '<!--   Begin Of script Output   -->'."\n";

		echo implode("\n", $output);
	}
	/**
	 * Displays the footer.
	 */
	function display_footer()
	{
		$output = array();

//		$output[] = '</div>';
		$output[] = '<div class="clear">&nbsp;</div> <!-- "clearing" div to make sure that footer stays below the main and right column sections -->';
		$output[] = '</div> <!-- end of #main" started at the end of banner.inc.php -->';

		$output[] = '<div id="footer"> <!-- start of #footer section -->';
		$output[] = '<div id="copyright">';
		$output[] = '<div class="logo">';
		$output[] = '<a href="http://www.dokeosplanet.org"><img src="'. '../layout/aqua/img/common/dokeos_logo_small.png" /></a>';
		$output[] = '</div>';
		$output[] = '<div class="links">';

		$links = array();
		$links[] = Translation :: get('License');
		$links[] = Translation :: get('PrivacyPolicy');
		$links[] = '<a href="http://www.dokeosplanet.org">http://www.dokeosplanet.org</a>';
		$links[] = '&copy;&nbsp;' . date('Y');

		$output[] = implode('&nbsp;|&nbsp;', $links);
		$output[] = '</div>';
		$output[] = '<div class="clear"></div>';
		$output[] = '</div>';

		$output[] = '   </div> <!-- end of #footer -->';
		$output[] = '  </div> <!-- end of #outerframe opened in header -->';
		$output[] = ' </body>';
		$output[] = '</html>';

		echo implode("\n", $output);
	}
}
?>