<?php
/**
 * @package admin
 * @subpackage package_manager
 * @author Hans De Bisschop
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/registration_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
/**
 * Admin component
 */
class PackageManagerBrowserComponent extends PackageManagerComponent
{
	private $action_bar;

	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('PackageManager')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('InstalledPackageList')));
		$trail->add_help('administration install');

		if (!AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT, 'root', 'root'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}

		$this->action_bar = $this->get_action_bar();
		$table = new RegistrationBrowserTable($this, array(Application :: PARAM_ACTION => AdminManager :: ACTION_MANAGE_PACKAGES), $this->get_condition());

		$this->display_header($trail);
		echo $this->action_bar->as_html();
		echo '<div class="clear"></div>';
		echo $table->as_html();
		$this->display_footer();
	}

	function get_condition()
	{
		$query = $this->action_bar->get_query();

		if(isset($query) && $query != '')
		{
			$condition = new PatternMatchCondition(Registration :: PROPERTY_NAME, '*' . $query . '*');
		}
		else
		{
		    $condition = null;
		}

		return $condition;
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->set_search_url($this->get_url());

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('InstallRemote'), Theme :: get_image_path().'action_install_remote.png', $this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_REMOTE_PACKAGE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('InstallArchive'), Theme :: get_image_path().'action_install_archive.png', $this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_INSTALL_PACKAGE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		return $action_bar;
	}
}
?>