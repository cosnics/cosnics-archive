<?php
/**
 * @package admin.lib.admin_manager
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/admincomponent.class.php';
require_once dirname(__FILE__).'/../admindatamanager.class.php';

require_once Path :: get_repository_path(). 'lib/repository_manager/repositorymanager.class.php';
require_once Path :: get_user_path(). 'lib/usermanager/usermanager.class.php';
require_once Path :: get_classgroup_path(). 'lib/classgroup_manager/classgroupmanager.class.php';
require_once Path :: get_tracking_path(). 'lib/tracking_manager/trackingmanager.class.php';
require_once Path :: get_rights_path(). 'lib/rights_manager/rightsmanager.class.php';
require_once Path :: get_home_path(). 'lib/home_manager/homemanager.class.php';
require_once Path :: get_menu_path(). 'lib/menu_manager/menumanager.class.php';
require_once Path :: get_migration_path(). 'lib/migration_manager/migrationmanager.class.php';

/**
 * The admin allows the platform admin to configure certain aspects of his platform
 */
class Admin
{
	const APPLICATION_NAME = 'admin';

	const PARAM_ACTION = 'go';
	const PARAM_APPLICATION = 'application';
	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';

	const ACTION_ADMIN_BROWSER = 'browse';
	const ACTION_SYSTEM_ANNOUNCEMENTS = 'systemannouncements';
	const ACTION_LANGUAGES = 'languages';
	const ACTION_CONFIGURE_PLATFORM = 'configure';

	private $parameters;

	private $user;

	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function Admin($user = null) {
    	$this->user = $user;
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);
    }

	/**
	 * Run this admin manager
	 */
    function run()
    {
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_CONFIGURE_PLATFORM :
				$component = AdminComponent :: factory('Configurer', $this);
				break;
			case self :: ACTION_SYSTEM_ANNOUNCEMENTS :
				$component = AdminComponent :: factory('Systemannouncements', $this);
				break;
			default :
				$component = AdminComponent :: factory('Browser', $this);
		}
		$component->run();
    }

	/**
	 * Displays the header.
	 * @param array $breadcrumbs Breadcrumbs to show in the header.
	 * @param boolean $display_search Should the header include a search form or
	 * not?
	 */
	function display_header($breadcrumbtrail = array (), $display_search = false)
	{
		if (is_null($breadcrumbtrail))
		{
			$breadcrumbtrail = new BreadcrumbTrail();
		}
		
		$title = $breadcrumbtrail->get_last()->get_name();
		$title_short = $title;
		if (strlen($title_short) > 53)
		{
			$title_short = substr($title_short, 0, 50).'&hellip;';
		}
		Display :: display_header($breadcrumbtrail, $title_short);
		echo '<h3 style="float: left;" title="'.$title.'">'.$title_short.'</h3>';
		if ($display_search)
		{
			$this->display_search_form();
		}
		echo '<div class="clear">&nbsp;</div>';
		
		$message = Request :: get(self :: PARAM_MESSAGE);
		if (isset($message))
		{
			$this->display_message($message);
		}
		$message = Request :: get(self :: PARAM_ERROR_MESSAGE);
		if(isset($message))
		{
			$this->display_error_message($message);
		}
	}
	/**
	 * Displays the footer.
	 */
	function display_footer()
	{
		echo '<div class="clear">&nbsp;</div>';
		Display :: display_footer();
	}
	/**
	 * Displays a normal message.
	 * @param string $message The message.
	 */
	function display_message($message)
	{
		Display :: display_normal_message($message);
	}
	/**
	 * Displays an error message.
	 * @param string $message The message.
	 */
	function display_error_message($message)
	{
		Display :: display_error_message($message);
	}
	/**
	 * Displays a warning message.
	 * @param string $message The message.
	 */
	function display_warning_message($message)
	{
		Display :: display_warning_message($message);
	}
	/**
	 * Displays an error page.
	 * @param string $message The message.
	 */
	function display_error_page($message)
	{
		$this->display_header();
		$this->display_error_message($message);
		$this->display_footer();
	}

	/**
	 * Displays a warning page.
	 * @param string $message The message.
	 */
	function display_warning_page($message)
	{
		$this->display_header();
		$this->display_warning_message($message);
		$this->display_footer();
	}

	/**
	 * Displays a popup form.
	 * @param string $message The message.
	 */
	function display_popup_form($form_html)
	{
		Display :: display_normal_message($form_html);
	}
	
	/**
	 * Gets the parameter list
	 * @param boolean $include_search Include the search parameters in the
	 * returned list?
	 * @return array The list of parameters.
	 */
	function get_parameters($include_search = false)
	{
		return $this->parameters;
	}

	/**
	 * Gets the current action.
	 * @see get_parameter()
	 * @return string The current action.
	 */
	function get_action()
	{
		return $this->get_parameter(self :: PARAM_ACTION);
	}

	/**
	 * Sets the current action.
	 * @param string $action The new action.
	 */
	function set_action($action)
	{
		return $this->set_parameter(self :: PARAM_ACTION, $action);
	}

	/**
	 * Gets the value of a parameter.
	 * @param string $name The parameter name.
	 * @return string The parameter value.
	 */
	function get_parameter($name)
	{
		return $this->parameters[$name];
	}
	
	/**
	 * Gets an URL.
	 * @param array $additional_parameters Additional parameters to add in the
	 * query string (default = no additional parameters).
	 * @param boolean $include_search Include the search parameters in the
	 * query string of the URL? (default = false).
	 * @param boolean $encode_entities Apply php function htmlentities to the
	 * resulting URL ? (default = false).
	 * @return string The requested URL.
	 */
	function get_url($additional_parameters = array (), $include_search = false, $encode_entities = false, $x = null)
	{
		$eventual_parameters = array_merge($this->get_parameters($include_search), $additional_parameters);
		$url = $_SERVER['PHP_SELF'].'?'.http_build_query($eventual_parameters);
		if ($encode_entities)
		{
			$url = htmlentities($url);
		}

		return $url;
	}
	
	public function get_link($parameters = array (), $encode = false)
	{
		$link = 'index_'. self :: APPLICATION_NAME;
		if (count($parameters))
		{
			$link .= '?'.http_build_query($parameters);
		}
		if ($encode)
		{
			$link = htmlentities($link);
		}
		return $link;
	}

	function get_user()
	{
		return $this->user;
	}

	/**
	 * Sets the value of a parameter.
	 * @param string $name The parameter name.
	 * @param mixed $value The parameter value.
	 */
	function set_parameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}

	function get_application_platform_admin_links()
	{
		$info = array();
		$user = $this->get_user();

		// 1. Admin-core components
		$links		= array();
		$links[]	= array('name' => Translation :: get('Settings'), 'action' => 'manage', 'url' => $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CONFIGURE_PLATFORM)));
		$links[]	= array('name' => Translation :: get('SystemAnnouncements'), 'action' => 'announce', 'url' => $this->get_link());
		$info[]		= array('application' => array('name' => Translation :: get('Admin'), 'class' => self :: APPLICATION_NAME), 'links' => $links);
		
		// 2. Repository
		$repository_manager = new RepositoryManager($user);
		$info[] = $repository_manager->get_application_platform_admin_links();
		
		// 3. UserManager
		$user_manager = new UserManager($user->get_user_id());
		$info[] = $user_manager->get_application_platform_admin_links();
		
		// 4. Roles'n'Rights
		$rights_manager = new RightsManager($user->get_user_id());
		$info[] = $rights_manager->get_application_platform_admin_links();
		
		// 5. Classgroups
		$classgroup_manager = new ClassGroupManager($user->get_user_id());
		$info[] = $classgroup_manager->get_application_platform_admin_links();
		
		// 6. Tracking
		$tracking_manager = new TrackingManager($user);
		$info[] = $tracking_manager->get_application_platform_admin_links();
		
		// 7. Home
		$home_manager = new HomeManager($user->get_user_id());
		$info[] = $home_manager->get_application_platform_admin_links();		
		
		// 8. Menu
		$menu_manager = new MenuManager($user->get_user_id());
		$info[] = $menu_manager->get_application_platform_admin_links();		
		
		// 9. Migration
		$migration_manager = new MigrationManager($user->get_user_id());
		$info[] = $migration_manager->get_application_platform_admin_links();	

		// 10.The links for the plugin applications running on top of the essential Dokeos components
		$applications = Application :: load_all();
		foreach($applications as $index => $application_name)
		{
			$application = Application::factory($application_name);
			$links = $application->get_application_platform_admin_links();
			if ($links['application']['name'])
			{
				$links['application']['name'] = Translation :: get(Application::application_to_class($links['application']['name']));
				$info[] = $links;
			}
		}

		return $info;
	}

	/**
	 * Gets the URL to the Dokeos claroline folder.
	 */
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}
}
?>