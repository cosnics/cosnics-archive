<?php

require_once dirname(__FILE__).'/webservice_manager_component.class.php';
require_once dirname(__FILE__).'/../webservice_rights.class.php';
require_once dirname(__FILE__).'/../webservice_data_manager.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once Path :: get_library_path() . 'core_application.class.php';

/**
 * A webservice manager provides some functionalities to the admin to manage
 * his webservices.
 */
 class WebserviceManager extends  CoreApplication
 {

 	const APPLICATION_NAME = 'webservice';

	const PARAM_REMOVE_SELECTED = 'delete';
	const PARAM_FIRSTLETTER = 'firstletter';
	const PARAM_COMPONENT_ACTION = 'action';
    const PARAM_SOURCE = 'source';

    const PARAM_LOCATION_ID = 'location';
	const PARAM_WEBSERVICE_ID = 'webservice';
	const PARAM_WEBSERVICE_CATEGORY_ID = 'webservice_category_id';

	const ACTION_BROWSE_WEBSERVICES = 'browse_webservices';
	const ACTION_BROWSE_WEBSERVICE_CATEGORIES = 'browse_webservice_categories';
    const ACTION_MANAGE_ROLES = 'rights_editor';

	private $parameters;
	private $search_parameters;
	private $user;
	private $breadcrumbs;
    private $instance;

    public function WebserviceManager($user = null)
    {
    	parent :: __construct($user);
    }

    public function get_application_name()
    {
    	return self :: APPLICATION_NAME;
    }

    /**
	 * Run this webservice manager
	 */
	function run()
	{
		$action = $this->get_action();

		$component = null;
		switch ($action)
		{
			case self :: ACTION_BROWSE_WEBSERVICES :
				$component = WebserviceManagerComponent :: factory('WebserviceBrowser', $this);
				break;
            case self :: ACTION_MANAGE_ROLES :
				$component = WebserviceManagerComponent :: factory('RightsEditor', $this);
				break;
			default :
				$component = WebserviceManagerComponent :: factory('WebserviceBrowser', $this);
		}
		$component->run(); //wordt gestart

	}

	function retrieve_webservices($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return WebserviceDataManager :: get_instance()->retrieve_webservices($condition, $offset, $count, $order_property);
	}

 	function count_webservices($condition = null)
	{
		return WebserviceDataManager :: get_instance()->count_webservices($condition);
	}

	function retrieve_webservice_categories($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return WebserviceDataManager :: get_instance()->retrieve_webservice_categories($condition, $offset, $count, $order_property);
	}

	function retrieve_webservice($id)
	{
		return WebserviceDataManager :: get_instance()->retrieve_webservice($id);
	}

    function retrieve_webservice_by_name($name)
	{
		return WebserviceDataManager :: get_instance()->retrieve_webservice_by_name($name);
	}

	public function get_application_platform_admin_links()
	{
		$links		= array();
		$links[]	= array('name' => Translation :: get('List'),
							'description' => Translation :: get('ListDescription'),
							'action' => 'list',
							'url' => $this->get_link(array(Application :: PARAM_ACTION => WebserviceManager :: ACTION_BROWSE_WEBSERVICES)));
		$info = parent :: get_application_platform_admin_links();
		$info['links'] = $links;

		return $info;
	}

    public function get_manage_roles_url($webservice)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGE_ROLES, self :: PARAM_WEBSERVICE_ID => $webservice->get_id()));
	}

    public function get_manage_roles_cat_url($webserviceCategory)
	{
		if(!$webserviceCategory)
			$webserviceCategory = 0;
		else
			$webserviceCategory = $webserviceCategory->get_id();

		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGE_ROLES, self :: PARAM_WEBSERVICE_CATEGORY_ID => $webserviceCategory));
	}

    public static function get_tool_bar_item($id)
	{
        $wdm = new WebserviceManager();
		$user_id = Session :: get_user_id();
		$user = UserDataManager :: get_instance()->retrieve_user($user_id);

		if(!$user || !$user->get_language())
			$language = PlatformSetting :: get('platform_language');
		else
			$language = $user->get_language();

		$toolbar_item = WebserviceDataManager :: get_instance()->retrieve_webservice_category($id);
        if(isset($toolbar_item))
        {
            $url = $wdm->get_manage_roles_cat_url($toolbar_item);
        }
        else
        {
            $wsm = new WebserviceManager();
            $url = $wsm->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGE_ROLES, self :: PARAM_WEBSERVICE_CATEGORY_ID => null));
        }
        return new ToolbarItem('Change rights ', Theme :: get_common_image_path().'action_rights.png', $url, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false);
	}

 }