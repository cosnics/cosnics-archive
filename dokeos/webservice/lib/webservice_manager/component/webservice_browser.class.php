<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../webservice_manager.class.php';
require_once dirname(__FILE__).'/../webservice_manager_component.class.php';
//require_once dirname(__FILE__).'/role_browser_table/role_browser_table.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class WebserviceManagerWebserviceBrowserComponent extends WebserviceManagerComponent
{
	private $action_bar;
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{

		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Translation :: get('Webservices')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseServices')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		$this->action_bar = $this->get_action_bar();
		$output = $this->get_user_html();
		
		$this->display_header($trail, false);
		echo '<br />' . $this->action_bar->as_html() . '<br />';
		echo $output;
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		$table = new RoleBrowserTable($this, array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_BROWSE_ROLES), $this->get_condition());
		
		$html = array();
		$html[] = '<div style="float: right; width: 100%;">';
		$html[] = $table->as_html();		 
		$html[] = '</div>';
		
		return implode($html, "\n");
	}

	function get_condition()
	{	
		$query = $this->action_bar->get_query();
		if(isset($query) && $query != '')
		{
			$condition = new LikeCondition(HelpItem :: PROPERTY_NAME, $query);
		}
		
		return $condition;
	}
	
	function get_webservice()
	{
		return (isset($_GET[WebserviceManager :: PARAM_WEBSERVICE_ID]) ? $_GET[WebserviceManager :: PARAM_WEBSERVICE_ID] : 0);
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$action_bar->set_search_url($this->get_url(array(WebserviceManager :: PARAM_WEBSERVICE_ID => $this->get_webservice())));
		//$action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path().'action_add.png', $this->get_url(array(RightsManager :: PARAM_ACTION => RightsManager :: ACTION_CREATE_ROLE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_tool_action(HelpManager :: get_tool_bar_help_item('webservices'));
		
		return $action_bar;
	}
}
?>