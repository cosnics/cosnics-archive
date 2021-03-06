<?php
/**
 * @package users.lib.usermanager
 */
require_once dirname(__FILE__).'/user_manager_component.class.php';
require_once Path :: get_library_path() . 'core_application.class.php';
require_once dirname(__FILE__).'/../user_data_manager.class.php';
require_once dirname(__FILE__).'/../user_block.class.php';

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
 class UserManager extends CoreApplication
 {

 	const APPLICATION_NAME = 'user';

	const PARAM_USER_USER_ID = 'user_id';
	const PARAM_REMOVE_SELECTED = 'delete';
	const PARAM_FIRSTLETTER = 'firstletter';

	const ACTION_CREATE_USER = 'create';
	const ACTION_BROWSE_USERS = 'adminbrowse';
	const ACTION_EXPORT_USERS = 'export';
	const ACTION_IMPORT_USERS = 'import';
	const ACTION_UPDATE_USER = 'update';
	const ACTION_DELETE_USER = 'delete';
	const ACTION_REGISTER_USER = 'register';
	const ACTION_VIEW_ACCOUNT = 'account';
	const ACTION_USER_QUOTA = 'quota';
	const ACTION_RESET_PASSWORD = 'reset_password';
	const ACTION_CHANGE_USER = 'change_user';
	const ACTION_MANAGE_RIGHTS_TEMPLATES = 'manage_user_rights_templates';
    const ACTION_REPORTING = 'reporting';
    const ACTION_VIEW_QUOTA = 'view_quota';

	const ACTION_VIEW_BUDDYLIST = 'buddy_view';
	const ACTION_CREATE_BUDDYLIST_CATEGORY = 'buddy_create_category';
	const ACTION_DELETE_BUDDYLIST_CATEGORY = 'buddy_delete_category';
	const ACTION_UPDATE_BUDDYLIST_CATEGORY = 'buddy_update_category';
	const ACTION_CREATE_BUDDYLIST_ITEM = 'buddy_create_item';
	const ACTION_DELETE_BUDDYLIST_ITEM = 'buddy_delete_item';
	const ACTION_CHANGE_BUDDYLIST_ITEM_STATUS = 'buddy_status_change';
	const ACTION_CHANGE_BUDDYLIST_ITEM_CATEGORY = 'buddy_category_change';

	const PARAM_BUDDYLIST_CATEGORY = 'buddylist_category';
	const PARAM_BUDDYLIST_ITEM = 'buddylist_item';

	private $quota_url;
	private $publication_url;
	private $create_url;
	private $recycle_bin_url;

    function UserManager($user = null)
    {
    	$user = $this->load_user($user);
    	parent :: __construct($user);

    	$this->load_user_theme();

    	// Can users set their own theme and if they
    	// can, do they have one set ? If so apply it
    	$user = $this->get_user();

    	if (is_object($user))
    	{
    		$user_can_set_theme = $this->get_platform_setting('allow_user_theme_selection');

    		if ($user_can_set_theme && $user->has_theme())
    		{
    			Theme :: set_theme($user->get_theme());
    		}
    	}
		$this->create_url = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_USER));
    }

	/**
	 * Sets the current user based on the input passed on to the UserManager.
	 * @param mixed $user The user.
	 */
    function load_user($user)
    {
    	if (isset($user))
    	{
    		if (is_object($user))
    		{
   				return $user;
    		}
    		else
    		{
   				if (!is_null($user))
   				{
   					return($this->retrieve_user($user));
   				}
   				else
   				{
   					return null;
   				}
    		}
    	}
    }

	/**
	 * Sets the platform theme to the user's selection if allowed.
	 */
    function load_user_theme()
    {
    	// TODO: Add theme to userforms.
    	$user = $this->get_user();

    	if (is_object($user))
    	{
    		$user_can_set_theme = $this->get_platform_setting('allow_user_theme_selection');

    		if ($user_can_set_theme && $user->has_theme())
    		{
    			Theme :: set_theme($user->get_theme());
    		}
    	}
    }

    /**
	 * Run this user manager
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_CREATE_USER :
				$component = UserManagerComponent :: factory('Creator', $this);
				break;
			case self :: ACTION_REGISTER_USER :
				$component = UserManagerComponent :: factory('Register', $this);
				break;
			case self :: ACTION_UPDATE_USER :
				$component = UserManagerComponent :: factory('Updater', $this);
				break;
			case self :: ACTION_DELETE_USER:
				$component = UserManagerComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_IMPORT_USERS :
				//$this->force_menu_url($this->create_url, true);
				$component = UserManagerComponent :: factory('Importer', $this);
				break;
			case self :: ACTION_EXPORT_USERS :
				//$this->force_menu_url($this->create_url, true);
				$component = UserManagerComponent :: factory('Exporter', $this);
				break;
			case self :: ACTION_USER_QUOTA :
				$component = UserManagerComponent :: factory('quota', $this);
				break;
			case self :: ACTION_BROWSE_USERS :
				$component = UserManagerComponent :: factory('AdminUserBrowser', $this);
				break;
			case self :: ACTION_VIEW_ACCOUNT :
				$component = UserManagerComponent :: factory('Account', $this);
				break;
			case self :: ACTION_RESET_PASSWORD :
				$component = UserManagerComponent :: factory('ResetPassword', $this);
				break;
			case self :: ACTION_CHANGE_USER :
				$component = UserManagerComponent :: factory('ChangeUser', $this);
				break;
			case self :: ACTION_MANAGE_RIGHTS_TEMPLATES :
				$component = UserManagerComponent :: factory('UserRightsTemplateManager', $this);
				break;
			case self :: ACTION_VIEW_BUDDYLIST :
				$component = UserManagerComponent :: factory('BuddyListViewer', $this);
				break;
			case self :: ACTION_CREATE_BUDDYLIST_CATEGORY :
				$component = UserManagerComponent :: factory('BuddyListCategoryCreator', $this);
				break;
			case self :: ACTION_DELETE_BUDDYLIST_CATEGORY :
				$component = UserManagerComponent :: factory('BuddyListCategoryDeleter', $this);
				break;
			case self :: ACTION_UPDATE_BUDDYLIST_CATEGORY :
				$component = UserManagerComponent :: factory('BuddyListCategoryEditor', $this);
				break;
			case self :: ACTION_CREATE_BUDDYLIST_ITEM :
				$component = UserManagerComponent :: factory('BuddyListItemCreator', $this);
				break;
			case self :: ACTION_DELETE_BUDDYLIST_ITEM :
				$component = UserManagerComponent :: factory('BuddyListItemDeleter', $this);
				break;
			case self :: ACTION_CHANGE_BUDDYLIST_ITEM_STATUS :
				$component = UserManagerComponent :: factory('BuddyListItemStatusChanger', $this);
				break;
			case self :: ACTION_CHANGE_BUDDYLIST_ITEM_CATEGORY :
				$component = UserManagerComponent :: factory('BuddyListItemCategoryChanger', $this);
				break;
            case self :: ACTION_REPORTING:
                $component = UserManagerComponent :: factory('Reporting',$this);
                break;
            case self :: ACTION_VIEW_QUOTA:
            	$component = UserManagerComponent :: factory('QuotaViewer',$this);
                break;
			default :
				$this->set_action(self :: ACTION_BROWSE_USERS);
				$component = UserManagerComponent :: factory('AdminUserBrowser', $this);
		}
		$component->run();
	}

    /**
	 * Renders the users block and returns it.
	 */
	function render_block($block)
	{
		$user_block = UserBlock :: factory($this, $block);
		return $user_block->run();
	}

	/**
	 * Counts the users
	 * @param $condition
	 */
	function count_users($condition = null)
	{
		return UserDataManager :: get_instance()->count_users($condition);
	}

	/**
	 * Retrieve the users
	 * @param $condition
	 * @param $offset
	 * @param $count
	 * @param $order_property
	 */
	function retrieve_users($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return UserDataManager :: get_instance()->retrieve_users($condition, $offset, $count, $order_property);
	}

	/**
	 * Retrieves a user.
	 * @param int $id The id of the user.
	 */
	function retrieve_user($id)
	{
		$udm = UserDataManager :: get_instance();
		return $udm->retrieve_user($id);
	}

	function retrieve_user_by_username($username)
	{
		$udm = UserDataManager :: get_instance();
		return $udm->retrieve_user_by_username($username);
	}

	/**
	 * @see RepositoryDataManager::content_object_deletion_allowed()
	 */
	function user_deletion_allowed($user)
	{
		$udm = UserDataManager :: get_instance();
		return $udm->user_deletion_allowed($user);
	}

	/**
	 * Gets the available links to display in the platform admin
	 * @retun array of links and actions
	 */
	public function get_application_platform_admin_links()
	{
		$links		= array();
		$links[]	= array('name' => Translation :: get('List'),
							'description' => Translation :: get('ListDescription'),
							'action' => 'list',
							'url' => $this->get_link(array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)));
		$links[]	= array('name' => Translation :: get('Create'),
							'description' => Translation :: get('CreateDescription'),
							'action' => 'add',
							'url' => $this->get_link(array(Application :: PARAM_ACTION => UserManager :: ACTION_CREATE_USER)));
		$links[]	= array('name' => Translation :: get('Export'),
							'description' => Translation :: get('ExportDescription'),
							'action' => 'export',
							'url' => $this->get_link(array(Application :: PARAM_ACTION => UserManager :: ACTION_EXPORT_USERS)));
		$links[]	= array('name' => Translation :: get('Import'),
							'description' => Translation :: get('ImportDescription'),
							'action' => 'import',
							'url' => $this->get_link(array(Application :: PARAM_ACTION => UserManager :: ACTION_IMPORT_USERS)));

		$info = parent :: get_application_platform_admin_links();
		$info['links'] = $links;
		$info['search'] = $this->get_link(array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));

		return $info;
	}

	/**
	 * gets the user editing url
	 * @param return the requested url
	 */
	function get_user_editing_url($user)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_UPDATE_USER, self :: PARAM_USER_USER_ID => $user->get_id()));
	}

	function get_change_user_url($user)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CHANGE_USER,
															   self :: PARAM_USER_USER_ID => $user->get_id()));
	}

	/**
	 * gets the user quota url
	 * @param return the requested url
	 */
	function get_user_quota_url($user)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_USER_QUOTA, self :: PARAM_USER_USER_ID => $user->get_id()));
	}
	/**
	 * gets the user delete url
	 * @param return the requested url
	 */
	function get_user_delete_url($user)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_USER, self :: PARAM_USER_USER_ID => $user->get_id()));
	}

	function get_manage_user_rights_url($user)
	{
		return $this->get_url(array (Application :: PARAM_APPLICATION => RightsManager :: APPLICATION_NAME, Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_USER_RIGHTS, UserRightManager :: PARAM_USER_RIGHT_ACTION => UserRightManager :: ACTION_BROWSE_USER_RIGHTS, UserRightManager :: PARAM_USER => $user->get_id()));
	}

	function get_create_buddylist_category_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_BUDDYLIST_CATEGORY));
	}

 	function get_delete_buddylist_category_url($category_id)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_BUDDYLIST_CATEGORY,
									 self :: PARAM_BUDDYLIST_CATEGORY => $category_id));
	}

 	function get_update_buddylist_category_url($category_id)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_UPDATE_BUDDYLIST_CATEGORY,
									 self :: PARAM_BUDDYLIST_CATEGORY => $category_id));
	}

 	function get_create_buddylist_item_url()
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_BUDDYLIST_ITEM));
	}

 	function get_delete_buddylist_item_url($item_id)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_BUDDYLIST_ITEM,
									 self :: PARAM_BUDDYLIST_ITEM => $item_id));
	}

 	function get_change_buddylist_item_status_url($item_id, $status)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CHANGE_BUDDYLIST_ITEM_STATUS,
									 self :: PARAM_BUDDYLIST_ITEM => $item_id, 'status' => $status));
	}

    function get_reporting_url($classname, $params)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REPORTING, ReportingManager::PARAM_TEMPLATE_NAME => $classname, ReportingManager::PARAM_TEMPLATE_FUNCTION_PARAMETERS => $params));
    }

    function get_application_name()
    {
    	return self :: APPLICATION_NAME;
    }

}
?>