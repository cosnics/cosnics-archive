<?php
/**
 * @package user.groupsmanager
 */
require_once Path :: get_library_path() . 'core_application.class.php';

require_once dirname(__FILE__).'/help_manager_component.class.php';
require_once dirname(__FILE__).'/../help_data_manager.class.php';
require_once dirname(__FILE__).'/../help_item.class.php';
require_once dirname(__FILE__).'/../../../common/html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../../../common/condition/or_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/and_condition.class.php';
require_once dirname(__FILE__).'/../../../common/condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/component/help_item_browser_table/help_item_browser_table.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';

/**
 * A user manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
class HelpManager extends CoreApplication
{
 	const APPLICATION_NAME = 'help';

	const PARAM_HELP_ITEM = 'help_item';

	const ACTION_UPDATE_HELP_ITEM = 'update';
	const ACTION_BROWSE_HELP_ITEMS = 'browse';

    function HelpManager($user = null)
    {
        parent :: __construct($user);
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
			case self :: ACTION_BROWSE_HELP_ITEMS :
				$component = HelpManagerComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_UPDATE_HELP_ITEM :
				$component = HelpManagerComponent :: factory('Updater', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_HELP_ITEMS);
				$component = HelpManagerComponent :: factory('Browser', $this);
		}
		$component->run();
	}

	public function get_application_platform_admin_links()
	{
		$links		= array();
		$links[]	= array('name' => Translation :: get('List'),
							'description' => Translation :: get('ListDescription'),
							'action' => 'list',
							'url' => $this->get_link(array(Application :: PARAM_ACTION => HelpManager :: ACTION_BROWSE_HELP_ITEMS)));

		$info = parent :: get_application_platform_admin_links();
		$info['links'] = $links;

		return $info;
	}

	public function count_help_items($condition)
	{
		return HelpDataManager :: get_instance()->count_help_items($condition);
	}

	public function retrieve_help_items($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return HelpDataManager :: get_instance()->retrieve_help_items($condition, $offset, $count, $order_property);
	}

	public function retrieve_help_item($name, $language)
	{
		return HelpDataManager :: get_instance()->retrieve_help_item($name, $language);
	}

	public static function get_help_url($name)
	{
		$help_item = self :: get_help_item_by_name($name);
		if($help_item)
			return '<a class="help" href="' . $help_item->get_url() . '" target="about:blank">' . Translation :: get('Help') . '</a>';
	}

	public static function get_tool_bar_help_item($name)
	{
		$help_item = self :: get_help_item_by_name($name);
		if($help_item)
		{

			return new ToolbarItem(Translation :: get('Help'), Theme :: get_common_image_path().'action_help.png', $help_item?$help_item->get_url():'', ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'help', 'about:blank');
		}
		else
		{
			return false;
		}
	}

	private static function get_help_item_by_name($name)
	{
		$user_id = Session :: get_user_id();
		$user = UserDataManager :: get_instance()->retrieve_user($user_id);

		if(!$user || !$user->get_language())
		{
			$language = PlatformSetting :: get('platform_language');
		}
		else
		{
			$language = $user->get_language();
		}

		$help_item = HelpDataManager :: get_instance()->retrieve_help_item_by_name_and_language($name, $language);
		return $help_item;
	}

 	/**
	 * Helper function for the Application class,
	 * pending access to class constants via variables in PHP 5.3
	 * e.g. $name = $class :: APPLICATION_NAME
	 *
	 * DO NOT USE IN THIS APPLICATION'S CONTEXT
	 * Instead use:
	 * - self :: APPLICATION_NAME in the context of this class
	 * - YourApplicationManager :: APPLICATION_NAME in all other application classes
	 */
	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}
}
?>