<?php
/**
 * @package user.groupsmanager
 */
require_once dirname(__FILE__).'/group_manager_component.class.php';
require_once dirname(__FILE__).'/../forms/group_search_form.class.php';
require_once dirname(__FILE__).'/../forms/group_user_search_form.class.php';
require_once dirname(__FILE__).'/../group_data_manager.class.php';
require_once dirname(__FILE__).'/../group.class.php';
require_once dirname(__FILE__).'/../../../common/html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../common/condition/or_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/and_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/component/group_browser/group_browser_table.class.php';
require_once dirname(__FILE__).'/component/group_rel_user_browser/group_rel_user_browser_table.class.php';

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
 class GroupManager
 {

 	const APPLICATION_NAME = 'group';

 	const PARAM_ACTION = 'go';
	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';
	const PARAM_GROUP_ID = 'group_id';
	const PARAM_GROUP_REL_USER_ID = 'group_rel_user_id';
	const PARAM_USER_ID = 'user_id';
	const PARAM_REMOVE_SELECTED = 'delete';
	const PARAM_UNSUBSCRIBE_SELECTED = 'unsubscribe_selected';
	const PARAM_SUBSCRIBE_SELECTED = 'subscribe_selected';
	const PARAM_TRUNCATE_SELECTED = 'truncate';
	const PARAM_FIRSTLETTER = 'firstletter';
	const PARAM_COMPONENT_ACTION = 'action';

	const ACTION_CREATE_GROUP = 'create';
	const ACTION_BROWSE_GROUPS = 'browse';
	const ACTION_EDIT_GROUP = 'edit';
	const ACTION_DELETE_GROUP = 'delete';
	const ACTION_MOVE_GROUP = 'move';
	const ACTION_TRUNCATE_GROUP = 'truncate';
	const ACTION_VIEW_GROUP = 'view';
	const ACTION_EXPORT = 'export';
	const ACTION_IMPORT = 'import';
	const ACTION_SUBSCRIBE_USER_TO_GROUP = 'subscribe';
	const ACTION_SUBSCRIBE_USER_BROWSER = 'subscribe_browser';
	const ACTION_UNSUBSCRIBE_USER_FROM_GROUP = 'unsubscribe';
	const ACTION_MANAGE_ROLES = 'manage_group_roles';

	private $parameters;
	private $search_parameters;
	private $user_search_parameters;
	private $search_form;
	private $user_search_form;
	private $user_id;
	private $user;
	private $category_menu;
	private $quota_url;
	private $publication_url;
	private $create_url;
	private $recycle_bin_url;
	private $breadcrumbs;

    function GroupManager($user = null) {
    	$this->user = $user;
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);
		$this->parse_input_from_table();
		$this->create_url = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_GROUP));
    }

    /**
	 * Run this user manager
	 */
	function run()
	{
		/*
		 * Only setting breadcrumbs here. Some stuff still calls
		 * forceCurrentUrl(), but that should not affect the breadcrumbs.
		 */
		//$this->breadcrumbs = $this->get_category_menu()->get_breadcrumbs();
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_CREATE_GROUP :
				$component = GroupManagerComponent :: factory('Creator', $this);
				break;
			case self :: ACTION_EDIT_GROUP :
				$component = GroupManagerComponent :: factory('Editor', $this);
				break;
			case self :: ACTION_DELETE_GROUP :
				$component = GroupManagerComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_MOVE_GROUP :
				$component = GroupManagerComponent :: factory('Mover', $this);
				break;
			case self :: ACTION_TRUNCATE_GROUP :
				$component = GroupManagerComponent :: factory('Truncater', $this);
				break;
			case self :: ACTION_VIEW_GROUP :
				$component = GroupManagerComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_EXPORT :
				$component = GroupManagerComponent :: factory('Exporter', $this);
				break;
			case self :: ACTION_IMPORT :
				$component = GroupManagerComponent :: factory('Importer', $this);
				break;
			case self :: ACTION_BROWSE_GROUPS :
				$component = GroupManagerComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_UNSUBSCRIBE_USER_FROM_GROUP :
				$component = GroupManagerComponent :: factory('Unsubscriber', $this);
				break;
			case self :: ACTION_SUBSCRIBE_USER_TO_GROUP :
				$component = GroupManagerComponent :: factory('Subscriber', $this);
				break;
			case self :: ACTION_SUBSCRIBE_USER_BROWSER :
				$component = GroupManagerComponent :: factory('SubscribeUserBrowser', $this);
				break;
			case self :: ACTION_MANAGE_ROLES :
				$component = GroupManagerComponent :: factory('GroupRoleManager', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_GROUPS);
				$component = GroupManagerComponent :: factory('Browser', $this);
		}
		$component->run();
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
	 * Displays the header.
	 * @param array $breadcrumbs Breadcrumbs to show in the header.
	 * @param boolean $display_search Should the header include a search form or
	 * not?
	 */
	function display_header($breadcrumbtrail, $display_search = false)
	{
		if (is_null($breadcrumbtrail))
		{
			$breadcrumbtrail = new BreadcrumbTrail();
		}

		$categories = $this->breadcrumbs;
		if (count($categories) > 0)
		{
			foreach($categories as $category)
			{
				$breadcrumbtrail->add(new Breadcrumb($category['url'], $category['title']));
			}
		}

		$title = $breadcrumbtrail->get_last()->get_name();
		$title_short = $title;
		if (strlen($title_short) > 53)
		{
			$title_short = substr($title_short, 0, 50).'&hellip;';
		}
		Display :: header($breadcrumbtrail);
		echo '<h3 style="float: left;" title="'.$title.'">'.$title_short.'</h3>';
		if ($display_search)
		{
			$this->display_search_form();
		}

		echo '<div class="clear">&nbsp;</div>';
		if ($msg = $_GET[self :: PARAM_MESSAGE])
		{
			$this->display_message($msg);
		}
		if($msg = $_GET[self::PARAM_ERROR_MESSAGE])
		{
			$this->display_error_message($msg);
		}
	}

	private function display_search_form()
	{
		echo $this->get_search_form()->display();
	}

	function display_user_search_form()
	{
		echo $this->get_user_search_form()->display();
	}

	function count_groups($condition = null)
	{
		return GroupDataManager :: get_instance()->count_groups($condition);
	}

	function count_group_rel_users($condition = null)
	{
		return GroupDataManager :: get_instance()->count_group_rel_users($condition);
	}

	/**
	 * Displays the footer.
	 */
	function display_footer()
	{
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: footer();
	}

	/**
	 * Displays a normal message.
	 * @param string $message The message.
	 */
	function display_message($message)
	{
		Display :: normal_message($message);
	}
	/**
	 * Displays an error message.
	 * @param string $message The message.
	 */
	function display_error_message($message)
	{
		Display :: error_message($message);
	}
	/**
	 * Displays a warning message.
	 * @param string $message The message.
	 */
	function display_warning_message($message)
	{
		Display :: warning_message($message);
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
		Display :: normal_message($form_html);
	}

	function get_search_condition()
	{
		return $this->get_search_form()->get_condition();
	}

	function get_user_search_condition()
	{
		return $this->get_user_search_form()->get_condition();
	}

	private function get_search_form()
	{
		if (!isset ($this->search_form))
		{
			$this->search_form = new GroupSearchForm($this, $this->get_url());
		}
		return $this->search_form;
	}

	private function get_user_search_form()
	{
		if (!isset ($this->user_search_form))
		{
			$this->user_search_form = new UserSearchForm($this, $this->get_url(array(self :: PARAM_GROUP_ID => $_GET[self :: PARAM_GROUP_ID])));
		}
		return $this->user_search_form;
	}

	function get_search_validate()
	{
		return $this->get_search_form()->validate();
	}

	function get_user_search_validate()
	{
		return $this->get_user_search_form()->validate();
	}

	/**
	 * Gets the parameter list
	 * @param boolean $include_search Include the search parameters in the
	 * returned list?
	 * @return array The list of parameters.
	 */
	function get_parameters($include_search = false, $include_user_search = false)
	{
		$parms = $this->parameters;

		if ($include_search && isset ($this->search_parameters))
		{
			$parms = array_merge($this->search_parameters, $parms);
		}

		if ($include_user_search && isset ($this->user_search_parameters))
		{
			$parms = array_merge($this->user_search_parameters, $parms);
		}

		return $parms;
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
	 * Sets the value of a parameter.
	 * @param string $name The parameter name.
	 * @param mixed $value The parameter value.
	 */
	function set_parameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}

	function retrieve_groups($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return GroupDataManager :: get_instance()->retrieve_groups($condition, $offset, $count, $order_property, $order_direction);
	}

	function retrieve_group_rel_users($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return GroupDataManager :: get_instance()->retrieve_group_rel_users($condition, $offset, $count, $order_property, $order_direction);
	}

	function retrieve_group_rel_user($user_id, $group_id)
	{
		return GroupDataManager :: get_instance()->retrieve_group_rel_user($user_id, $group_id);
	}

	/**
	 * Redirect the end user to another location.
	 * @param string $action The action to take (default = browse learning
	 * objects).
	 * @param string $message The message to show (default = no message).
	 * @param int $new_category_id The category to show (default = root
	 * category).
	 * @param boolean $error_message Is the passed message an error message?
	 */
	function redirect($type = 'url', $message = null, $error_message = false, $extra_params = null)
	{
		$params = array ();
		if (isset ($message))
		{
			$params[$error_message ? self :: PARAM_ERROR_MESSAGE :  self :: PARAM_MESSAGE] = $message;
		}
		if (isset($extra_params))
		{
			foreach($extra_params as $key => $extra)
			{
				$params[$key] = $extra;
			}
		}
		if ($type == 'url')
		{
			$url = $this->get_url($params);
		}
		elseif ($type == 'link')
		{
			$url = 'index.php';
		}

		header('Location: '.$url);
	}

	/**
	 * Sets the active URL in the navigation menu.
	 * @param string $url The active URL.
	 */
	function force_menu_url($url)
	{
		//$this->get_category_menu()->forceCurrentUrl($url);
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
	function get_url($additional_parameters = array (), $include_search = false, $encode_entities = false, $x = null, $include_user_search = false)
	{
		$eventual_parameters = array_merge($this->get_parameters($include_search, $include_user_search), $additional_parameters);
		$url = $_SERVER['PHP_SELF'].'?'.http_build_query($eventual_parameters);
		if ($encode_entities)
		{
			$url = htmlentities($url);
		}

		return $url;
	}
	/**
	 * Gets the user id.
	 * @return int The requested user id.
	 */
	function get_user_id()
	{
		return $this->user->get_id();
	}

	function get_user()
	{
		return $this->user;
	}

	function retrieve_group($id)
	{
		$gdm = GroupDataManager :: get_instance();
		return $gdm->retrieve_group($id);
	}

	/**
	 * Gets the URL to the Dokeos claroline folder.
	 */
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}
	/**
	 * Wrapper for api_not_allowed().
	 */
	function not_allowed()
	{
		Display :: not_allowed();
	}

	public function get_application_platform_admin_links()
	{
		$links		= array();
		$links[]	= array('name' => Translation :: get('List'),
							'description' => Translation :: get('ListDescription'),
							'action' => 'list',
							'url' => $this->get_link(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)));
		$links[]	= array('name' => Translation :: get('Create'),
							'description' => Translation :: get('CreateDescription'),
							'action' => 'add',
							'url' => $this->get_link(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_CREATE_GROUP, GroupManager :: PARAM_GROUP_ID => 0)));
		$links[]	= array('name' => Translation :: get('Export'),
							'description' => Translation :: get('ExportDescription'),
							'action' => 'export',
							'url' => $this->get_link(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_EXPORT)));
		$links[]	= array('name' => Translation :: get('Import'),
							'description' => Translation :: get('ImportDescription'),
							'action' => 'import',
							'url' => $this->get_link(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_IMPORT)));
		return array('application' => array('name' => Translation :: get('Group'), 'class' => 'group'), 'links' => $links, 'search' => $this->get_link(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)));
	}

	public function get_link($parameters = array (), $encode = false)
	{
		$link = 'index_'. self :: APPLICATION_NAME .'.php';
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

	function get_group_editing_url($group)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_GROUP, self :: PARAM_GROUP_ID => $group->get_id()));
	}

	function get_create_group_url($parent_id)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_GROUP, self :: PARAM_GROUP_ID => $parent_id));
	}

	function get_group_emptying_url($group)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_TRUNCATE_GROUP, self :: PARAM_GROUP_ID => $group->get_id()));
	}

	function get_group_viewing_url($group)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_GROUP, self :: PARAM_GROUP_ID => $group->get_id()));
	}

	function get_group_rel_user_unsubscribing_url($groupreluser)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_UNSUBSCRIBE_USER_FROM_GROUP, self :: PARAM_GROUP_REL_USER_ID => $groupreluser->get_group_id() . '|' . $groupreluser->get_user_id()));
	}

	function get_group_rel_user_subscribing_url($group, $user)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER_TO_GROUP, self :: PARAM_GROUP_ID => $group->get_id(), self :: PARAM_USER_ID => $user->get_id()));
	}

	function get_group_suscribe_user_browser_url($group)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_SUBSCRIBE_USER_BROWSER, self :: PARAM_GROUP_ID => $group->get_id()));
	}

	function get_group_delete_url($group)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_GROUP, self :: PARAM_GROUP_ID => $group->get_id()));
	}

	function get_import_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_IMPORT));
	}

	function get_export_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EXPORT));
	}

	function get_move_group_url($group)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MOVE_GROUP,
			self :: PARAM_GROUP_ID => $group->get_id()));
	}

	private function parse_input_from_table()
	{
//		print_r($_POST); echo("<br />"); print_r($_GET);
		if (isset ($_POST['action']))
		{
			$selected_ids = $_POST[GroupRelUserBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

			if (empty ($selected_ids))
			{
				$selected_ids = array ();
			}
			elseif (!is_array($selected_ids))
			{
				$selected_ids = array ($selected_ids);
			}
			switch ($_POST['action'])
			{
				case self :: PARAM_UNSUBSCRIBE_SELECTED :
					$this->set_action(self :: ACTION_UNSUBSCRIBE_USER_FROM_GROUP);
					$_GET[self :: PARAM_GROUP_REL_USER_ID] = $selected_ids;
					break;
				case self :: PARAM_SUBSCRIBE_SELECTED :
					$this->set_action(self :: ACTION_SUBSCRIBE_USER_TO_GROUP);
					$_GET[self :: PARAM_USER_ID] = $selected_ids;
					break;
				case self :: PARAM_REMOVE_SELECTED :
					$this->set_action(self :: ACTION_DELETE_GROUP);
					$_GET[self :: PARAM_GROUP_ID] = $selected_ids;
					break;
				case self :: PARAM_TRUNCATE_SELECTED :
					$this->set_action(self :: ACTION_TRUNCATE_GROUP);
					$_GET[self :: PARAM_GROUP_ID] = $selected_ids;
					break;
			}

		}
	}

	function get_manage_roles_url($group)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MANAGE_ROLES, self :: PARAM_GROUP_ID => $group->get_id()));
	}
}
?>