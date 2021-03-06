<?php
/**
 * @package dcda.lib.dcda_manager
 */
require_once dirname(__FILE__).'/../reservations_data_manager.class.php';
require_once dirname(__FILE__).'/../reservations_rights.class.php';
require_once dirname(__FILE__).'/reservations_manager_component.class.php';
require_once dirname(__FILE__).'/../../web_application.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';

class ReservationsManager extends WebApplication
{

 	const APPLICATION_NAME = 'reservations';

 	const PARAM_ACTION = 'go';
	const PARAM_MESSAGE = 'message';
	const PARAM_ERROR_MESSAGE = 'error_message';

	const PARAM_CATEGORY_ID = 'category_id';
	const PARAM_ITEM_ID = 'item_id';
	const PARAM_RESERVATION_ID = 'reservation_id';
	const PARAM_SUBSCRIPTION_ID = 'subscription_id';
	const PARAM_QUOTA_ID = 'quota_id';
	const PARAM_QUOTA_BOX_ID = 'quota_box_id';
	const PARAM_CATEGORY_QUOTA_BOX_ID = 'category_quota_box_id';
	const PARAM_REF_QUOTA_ID = 'ref_quota_id';
	const PARAM_REF_QUOTA_GROUP = 'ref_quota_group';
	const PARAM_BLACKOUT = 'blackout';

	const PARAM_REMOVE_SELECTED_ITEMS = 'remove_selected_items';
	const PARAM_REMOVE_SELECTED_CATEGORIES = 'remove_selected_categories';
	const PARAM_REMOVE_SELECTED_RESERVATIONS = 'remove_selected_reservations';
	const PARAM_REMOVE_SELECTED_SUBSCRIPTIONS = 'remove_selected_subscriptions';
	const PARAM_REMOVE_SELECTED_QUOTAS = 'remove_selected_quotas';
	const PARAM_REMOVE_SELECTED_REF_QUOTAS = 'remove_selected_ref_quotas';
	const PARAM_REMOVE_SELECTED_CATEGORY_QUOTA_BOXES = 'remove_selected_category_boxes';
	const PARAM_REMOVE_SELECTED_QUOTA_BOXES = 'remove_selected_quota_boxes';
	const PARAM_DIRECTION = 'direction';

	const ACTION_BROWSE_CATEGORIES = 'browse_categories';
	const ACTION_ADMIN_BROWSE_CATEGORIES = 'admin_browse_categories';
	const ACTION_CREATE_CATEGORY = 'create_category';
	const ACTION_UPDATE_CATEGORY = 'update_category';
	const ACTION_DELETE_CATEGORY = 'delete_category';
	const ACTION_MOVE_CATEGORY = 'move_category';
	const ACTION_BLACKOUT_CATEGORY = 'blackout_category';
	const ACTION_CREDIT_CATEGORY = 'credit_category';
	const ACTION_SEARCH_POOL = 'search_pool';

	const ACTION_BROWSE_ITEMS = 'browse_items';
	const ACTION_ADMIN_BROWSE_ITEMS = 'admin_browse_items';
	const ACTION_CREATE_ITEM = 'create_item';
	const ACTION_UPDATE_ITEM = 'update_item';
	const ACTION_DELETE_ITEM = 'delete_item';

	const ACTION_BROWSE_RESERVATIONS = 'browse_reservations';
	const ACTION_ADMIN_BROWSE_RESERVATIONS = 'admin_browse_reservations';
	const ACTION_CREATE_RESERVATION = 'create_reservation';
	const ACTION_UPDATE_RESERVATION = 'update_reservation';
	const ACTION_DELETE_RESERVATION = 'delete_reservation';

	const ACTION_BROWSE_SUBSCRIPTIONS = 'browse_subscriptions';
	const ACTION_ADMIN_BROWSE_SUBSCRIPTIONS = 'admin_browse_subscriptions';
	const ACTION_CREATE_SUBSCRIPTION = 'create_subscription';
	const ACTION_DELETE_SUBSCRIPTION = 'delete_subscription';
	const ACTION_APPROVE_SUBSCRIPTION = 'approve_subscription';
	const ACTION_BROWSE_SUBSCRIPTION_USERS = 'browse_subscription_users';
	const ACTION_UPDATE_SUBSCRIPTION_USER = 'update_subscription_user';

	const ACTION_BROWSE_QUOTAS = 'browse_quotas';
	const ACTION_CREATE_QUOTA = 'create_quota';
	const ACTION_UPDATE_QUOTA = 'update_quota';
	const ACTION_DELETE_QUOTA = 'delete_quota';

	const ACTION_BROWSE_QUOTA_BOXES = 'browse_quota_boxes';
	const ACTION_CREATE_QUOTA_BOX = 'create_quota_box';
	const ACTION_UPDATE_QUOTA_BOX = 'update_quota_box';
	const ACTION_DELETE_QUOTA_BOX = 'delete_quota_box';

	const ACTION_BROWSE_CATEGORY_QUOTA_BOXES = 'browse_category_boxes';
	const ACTION_CREATE_CATEGORY_QUOTA_BOX = 'create_category_box';
	const ACTION_UPDATE_CATEGORY_QUOTA_BOX = 'update_category_box';
	const ACTION_DELETE_CATEGORY_QUOTA_BOX = 'delete_category_box';

	const ACTION_OVERVIEW = 'overview';
	const ACTION_MANAGE_OVERVIEW = 'manage_overview';

	const ACTION_EDIT_RIGHTS = 'edit_rights';

	private $parameters;
	private $search_parameters;
	private $search_form;
	private $user_id;
	private $user;
	private $dm;

    function ReservationsManager($user)
    {
		$this->dm = ReservationsDataManager :: get_instance();
    	parent :: __construct($user);
    	$this->parse_input_from_table();
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
			case self :: ACTION_ADMIN_BROWSE_CATEGORIES :
				$component = ReservationsManagerComponent :: factory('AdminCategoryBrowser', $this);
				break;
			case self :: ACTION_ADMIN_BROWSE_ITEMS :
				$component = ReservationsManagerComponent :: factory('AdminItemBrowser', $this);
				break;
			case self :: ACTION_ADMIN_BROWSE_RESERVATIONS :
				$component = ReservationsManagerComponent :: factory('AdminReservationBrowser', $this);
				break;
			case self :: ACTION_ADMIN_BROWSE_SUBSCRIPTIONS :
				$component = ReservationsManagerComponent :: factory('AdminSubscriptionBrowser', $this);
				break;
			case self :: ACTION_BROWSE_CATEGORIES :
				$component = ReservationsManagerComponent :: factory('CategoryBrowser', $this);
				break;
			case self :: ACTION_CREATE_CATEGORY :
				$component = ReservationsManagerComponent :: factory('CategoryCreator', $this);
				break;
			case self :: ACTION_UPDATE_CATEGORY :
				$component = ReservationsManagerComponent :: factory('CategoryUpdater', $this);
				break;
			case self :: ACTION_DELETE_CATEGORY :
				$component = ReservationsManagerComponent :: factory('CategoryDeleter', $this);
				break;
			case self :: ACTION_MOVE_CATEGORY :
				$component = ReservationsManagerComponent :: factory('CategoryMover', $this);
				break;
			case self :: ACTION_BLACKOUT_CATEGORY :
				$component = ReservationsManagerComponent :: factory('CategoryBlackout', $this);
				break;
			case self :: ACTION_CREDIT_CATEGORY :
				$component = ReservationsManagerComponent :: factory('CategoryCredit', $this);
				break;
			case self :: ACTION_BROWSE_ITEMS :
				$component = ReservationsManagerComponent :: factory('ItemBrowser', $this);
				break;
			case self :: ACTION_CREATE_ITEM :
				$component = ReservationsManagerComponent :: factory('ItemCreator', $this);
				break;
			case self :: ACTION_UPDATE_ITEM :
				$component = ReservationsManagerComponent :: factory('ItemUpdater', $this);
				break;
			case self :: ACTION_DELETE_ITEM :
				$component = ReservationsManagerComponent :: factory('ItemDeleter', $this);
				break;
			case self :: ACTION_BROWSE_RESERVATIONS :
				$component = ReservationsManagerComponent :: factory('ReservationBrowser', $this);
				break;
			case self :: ACTION_CREATE_RESERVATION :
				$component = ReservationsManagerComponent :: factory('ReservationCreator', $this);
				break;
			case self :: ACTION_UPDATE_RESERVATION :
				$component = ReservationsManagerComponent :: factory('ReservationUpdater', $this);
				break;
			case self :: ACTION_DELETE_RESERVATION :
				$component = ReservationsManagerComponent :: factory('ReservationDeleter', $this);
				break;
			case self :: ACTION_BROWSE_SUBSCRIPTIONS :
				$component = ReservationsManagerComponent :: factory('SubscriptionBrowser', $this);
				break;
			case self :: ACTION_CREATE_SUBSCRIPTION :
				$component = ReservationsManagerComponent :: factory('SubscriptionCreator', $this);
				break;
			case self :: ACTION_DELETE_SUBSCRIPTION :
				$component = ReservationsManagerComponent :: factory('SubscriptionDeleter', $this);
				break;
			case self :: ACTION_APPROVE_SUBSCRIPTION :
				$component = ReservationsManagerComponent :: factory('SubscriptionApprover', $this);
				break;
			case self :: ACTION_BROWSE_SUBSCRIPTION_USERS :
				$component = ReservationsManagerComponent :: factory('SubscriptionUserBrowser', $this);
				break;
			case self :: ACTION_UPDATE_SUBSCRIPTION_USER :
				$component = ReservationsManagerComponent :: factory('SubscriptionUserUpdater', $this);
				break;
			case self :: ACTION_BROWSE_QUOTAS :
				$component = ReservationsManagerComponent :: factory('QuotaBrowser', $this);
				break;
			case self :: ACTION_CREATE_QUOTA :
				$component = ReservationsManagerComponent :: factory('QuotaCreator', $this);
				break;
			case self :: ACTION_UPDATE_QUOTA :
				$component = ReservationsManagerComponent :: factory('QuotaUpdater', $this);
				break;
			case self :: ACTION_DELETE_QUOTA :
				$component = ReservationsManagerComponent :: factory('QuotaDeleter', $this);
				break;
			case self :: ACTION_SEARCH_POOL :
				$component = ReservationsManagerComponent :: factory('PoolSearcher', $this);
				break;
			case self :: ACTION_EDIT_RIGHTS :
				$component = ReservationsManagerComponent :: factory('RightsEditor', $this);
				break;
			case self :: ACTION_BROWSE_QUOTA_BOXES :
				$component = ReservationsManagerComponent :: factory('QuotaBoxBrowser', $this);
				break;
			case self :: ACTION_CREATE_QUOTA_BOX :
				$component = ReservationsManagerComponent :: factory('QuotaBoxCreator', $this);
				break;
			case self :: ACTION_UPDATE_QUOTA_BOX :
				$component = ReservationsManagerComponent :: factory('QuotaBoxUpdater', $this);
				break;
			case self :: ACTION_DELETE_QUOTA_BOX :
				$component = ReservationsManagerComponent :: factory('QuotaBoxDeleter', $this);
				break;
			case self :: ACTION_BROWSE_CATEGORY_QUOTA_BOXES :
				$component = ReservationsManagerComponent :: factory('CategoryQuotaBoxBrowser', $this);
				break;
			case self :: ACTION_CREATE_CATEGORY_QUOTA_BOX :
				$component = ReservationsManagerComponent :: factory('CategoryQuotaBoxCreator', $this);
				break;
			case self :: ACTION_UPDATE_CATEGORY_QUOTA_BOX :
				$component = ReservationsManagerComponent :: factory('CategoryQuotaBoxUpdater', $this);
				break;
			case self :: ACTION_DELETE_CATEGORY_QUOTA_BOX :
				$component = ReservationsManagerComponent :: factory('CategoryQuotaBoxDeleter', $this);
				break;
			case self :: ACTION_OVERVIEW :
				$component = ReservationsManagerComponent :: factory('OverviewBrowser', $this);
				break;
			case self :: ACTION_MANAGE_OVERVIEW :
				$component = ReservationsManagerComponent :: factory('ManageOverview', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_ITEMS);
				$component = ReservationsManagerComponent :: factory('ItemBrowser', $this);
		}
		$component->run();
	}

 	function display_header($breadcrumb_trail)
    {
    	parent :: display_header($breadcrumb_trail);
    	echo $this->display_menu();
    	echo '<div id="tool_browser_left">';
    }

    function display_footer()
    {
    	echo '</div>';
    	echo parent :: display_footer();
    }

    function display_menu()
	{
		$html = array();

		$html[] = '<div id="tool_bar" class="tool_bar tool_bar_left">';

		$html[] = '<div id="tool_bar_hide_container" class="hide">';
		$html[] = '<a id="tool_bar_hide" href="#"><img src="'. Theme :: get_common_image_path() .'action_action_bar_left_hide.png" /></a>';
		$html[] = '<a id="tool_bar_show" href="#"><img src="'. Theme :: get_common_image_path() .'action_action_bar_left_show.png" /></a>';
		$html[] = '</div>';

		$html[] = '<div class="tool_menu">';
		$html[] = '<ul>';

		$html[] = '<li class="tool_list_menu title">' . Translation :: get('Use') . '</li>';
		$html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_browser.png)"><a href="' . $this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_SUBSCRIPTIONS)) . '">' . Translation :: get('MyReservations') . '</a></li>';
		$html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_statistics.png)"><a href="' . $this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_OVERVIEW)) . '">' . Translation :: get('Statistics') . '</a></li>';

		$html[] = '<div class="splitter"></div>';

		$html[] = '<li class="tool_list_menu title" style="font-weight: bold">' . Translation :: get('Manage') . '</li>';

		$html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_item.png)"><a href="' . $this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS)) . '">' . Translation :: get('ManageItems') . '</a></li>';
		if($this->has_right('root', 0, ReservationsRights :: MANAGE_CATEGORIES_RIGHT))
		{
			$html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_category.png)"><a href="' . $this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_CATEGORIES)) . '">' . Translation :: get('ManageCategories') . '</a></li>';
			$html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_quota.png)"><a href="' . $this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTAS)) . '">' . Translation :: get('ManageQuota') . '</a></li>';
			$html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_browser.png)"><a href="' . $this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTA_BOXES)) . '">' . Translation :: get('ManageQuotaBoxes') . '</a></li>';
		}
		$html[] = '</ul>';
		$html[] = '</div>';

		$html[] = '</div>';
		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/tool_bar.js' .'"></script>';
		$html[] = '<div class="clear"></div>';

		return implode($html, "\n");
	}


	function has_right($type, $id, $right)
	{
		//$location_id = ReservationsRights :: get_location_id_by_identifier($type, $id);
		return ReservationsRights :: is_allowed($right, $id, $type);
	}

	function has_enough_credits_for($item, $start_date, $stop_date, $user_id)
	{
		return $this->dm->has_enough_credits_for($item, $start_date, $stop_date, $user_id);
	}

	/**
	 * Gets the available links to display in the platform admin
	 * @retun array of links and actions
	 */
	public function get_application_platform_admin_links()
	{
		$links = array();
		$links[] = array('name' => Translation :: get('Categories'), 'action' => 'category', 'url' => $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_CATEGORIES)));
		$links[] = array('name' => Translation :: get('Items'), 'action' => 'list', 'url' => $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_ITEMS)));
		$links[] = array('name' => Translation :: get('Quotas'), 'action' => 'list', 'url' => $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_QUOTAS)));
		$links[] = array('name' => Translation :: get('QuotaBoxes'), 'action' => 'list', 'url' => $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_QUOTA_BOXES)));

		$info = parent :: get_application_platform_admin_links();
		$info['links'] = $links;
		return $info;
	}

	/**
	 * Parse the input from the sortable tables and process input accordingly
	 */
	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			$selected_ids = $_POST['reservations_table'.ObjectTable :: CHECKBOX_NAME_SUFFIX];

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
				case self :: PARAM_REMOVE_SELECTED_CATEGORIES :
					$this->set_action(self :: ACTION_DELETE_CATEGORY);
					$_GET[self :: PARAM_CATEGORY_ID] = $selected_ids;
					break;
				case self :: PARAM_REMOVE_SELECTED_ITEMS:
					$this->set_action(self :: ACTION_DELETE_ITEM);
					$_GET[self :: PARAM_ITEM_ID] = $selected_ids;
					break;
				case self :: PARAM_REMOVE_SELECTED_RESERVATIONS:
					$this->set_action(self :: ACTION_DELETE_RESERVATION);
					$_GET[self :: PARAM_RESERVATION_ID] = $selected_ids;
					break;
				case self :: PARAM_REMOVE_SELECTED_SUBSCRIPTIONS:
					$this->set_action(self :: ACTION_DELETE_SUBSCRIPTION);
					$_GET[self :: PARAM_SUBSCRIPTION_ID] = $selected_ids;
					break;
				case self :: PARAM_REMOVE_SELECTED_QUOTAS:
					$this->set_action(self :: ACTION_DELETE_QUOTA);
					$_GET[self :: PARAM_QUOTA_ID] = $selected_ids;
					break;
				case self :: PARAM_REMOVE_SELECTED_QUOTA_BOXES:
					$this->set_action(self :: ACTION_DELETE_QUOTA_BOX);
					$_GET[self :: PARAM_QUOTA_BOX_ID] = $selected_ids;
					break;
				case self :: PARAM_REMOVE_SELECTED_REF_QUOTAS:
					$this->set_action(self :: ACTION_DELETE_REF_QUOTA);
					$_GET[self :: PARAM_QUOTA_ID] = $selected_ids;
					break;
			}
		}
	}

	/**
	 * DataManager Functions
	 */
	function count_items($condition = null)
	{
		return $this->dm->count_items($condition);
	}

	function retrieve_items($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->dm->retrieve_items($condition, $offset, $count, $order_property);
	}

	function count_categories($condition = null)
	{
		return $this->dm->count_categories($condition);
	}

	function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->dm->retrieve_categories($condition, $offset, $count, $order_property);
	}

	function count_reservations($condition = null)
	{
		return $this->dm->count_reservations($condition);
	}

	function retrieve_reservations($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->dm->retrieve_reservations($condition, $offset, $count, $order_property);
	}

	function count_subscriptions($condition = null)
	{
		return $this->dm->count_subscriptions($condition);
	}

	function retrieve_quotas($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->dm->retrieve_quotas($condition, $offset, $count, $order_property);
	}

	function count_quotas($condition = null)
	{
		return $this->dm->count_quotas($condition);
	}

 	function retrieve_quota_boxes($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->dm->retrieve_quota_boxes($condition, $offset, $count, $order_property);
	}

	function count_quota_boxes($condition = null)
	{
		return $this->dm->count_quota_boxes($condition);
	}

	function retrieve_quota_box_rel_categories($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->dm->retrieve_quota_box_rel_categories($condition, $offset, $count, $order_property);
	}

 	function count_quota_box_rel_categories($condition = null)
	{
		return $this->dm->count_quota_box_rel_categories($condition);
	}

	function retrieve_subscriptions($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->dm->retrieve_subscriptions($condition, $offset, $count, $order_property);
	}

	function retrieve_subscription_users($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->dm->retrieve_subscription_users($condition, $offset, $count, $order_property);
	}

	function count_subscription_users($condition = null)
	{
		return $this->dm->count_subscription_users($condition);
	}

	function retrieve_overview_items($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return $this->dm->retrieve_overview_items($condition, $offset, $count, $order_property);
	}

 	function count_overview_items($condition)
	{
		return $this->dm->count_overview_items($condition);
	}

	/**
	 * URL Functions
	 */
	function get_browse_categories_url($category_id = 0)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_CATEGORIES,
								    self :: PARAM_CATEGORY_ID => $category_id));
	}

	function get_create_category_url($category_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CATEGORY,
									self :: PARAM_CATEGORY_ID => $category_id));
	}

	function get_update_category_url($category_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_CATEGORY,
								    self :: PARAM_CATEGORY_ID => $category_id));
	}

	function get_delete_category_url($category_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CATEGORY,
								    self :: PARAM_CATEGORY_ID => $category_id));
	}

	function get_move_category_url($category_id, $direction = 1)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_CATEGORY,
								    self :: PARAM_CATEGORY_ID => $category_id,
								    self :: PARAM_DIRECTION => $direction));
	}

	function get_blackout_category_url($category_id, $blackout)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BLACKOUT_CATEGORY,
								    self :: PARAM_CATEGORY_ID => $category_id,
								    self :: PARAM_BLACKOUT => $blackout));
	}

	function get_credit_category_url($category_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREDIT_CATEGORY,
								    self :: PARAM_CATEGORY_ID => $category_id));
	}

	function get_browse_items_url($item_id = 0)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_ITEMS,
								    self :: PARAM_ITEM_ID => $item_id));
	}

	function get_create_item_url($cat_id = 0)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_ITEM,
									self :: PARAM_CATEGORY_ID => $cat_id));
	}

	function get_update_item_url($item_id, $cat_id = 0)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_ITEM,
								    self :: PARAM_ITEM_ID => $item_id,
								    self :: PARAM_CATEGORY_ID => $cat_id));
	}

	function get_delete_item_url($item_id ,$cat_id = 0)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_ITEM,
								    self :: PARAM_ITEM_ID => $item_id,
								    self :: PARAM_CATEGORY_ID => $cat_id));
	}

	function get_browse_reservations_url($item_id = 0)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_RESERVATIONS,
								    self :: PARAM_ITEM_ID => $item_id));
	}

	function get_create_reservation_url($item_id = 0)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_RESERVATION,
									self :: PARAM_ITEM_ID => $item_id));
	}

	function get_update_reservation_url($reservation_id, $item_id = 0)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_RESERVATION,
								    self :: PARAM_RESERVATION_ID => $reservation_id,
								    self :: PARAM_ITEM_ID => $item_id));
	}

	function get_delete_reservation_url($reservation_id ,$item_id = 0)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_RESERVATION,
								    self :: PARAM_RESERVATION_ID => $reservation_id,
								    self :: PARAM_ITEM_ID => $item_id));
	}

	function get_browse_subscriptions_url($reservation_id = 0)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SUBSCRIPTIONS,
								    self :: PARAM_ITEM_ID => $reservation_id));
	}

	function get_create_subscription_url($item_id = 0)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_SUBSCRIPTION,
									self :: PARAM_ITEM_ID => $item_id));
	}

	function get_delete_subscription_url($subscription_id ,$item_id = 0)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_SUBSCRIPTION,
								    self :: PARAM_SUBSCRIPTION_ID => $subscription_id,
								    self :: PARAM_ITEM_ID => $item_id));
	}

	function get_browse_quotas_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_QUOTAS));
	}

	function get_create_quota_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_QUOTA));
	}

	function get_update_quota_url($quota_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_QUOTA,
								    self :: PARAM_QUOTA_ID => $quota_id));
	}

	function get_delete_quota_url($quota_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_QUOTA,
								    self :: PARAM_QUOTA_ID => $quota_id));
	}

	function get_browse_ref_quotas_url($ref_id, $group)
	{
		return $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_REF_QUOTAS,
									self :: PARAM_REF_QUOTA_ID => $ref_id,
									self :: PARAM_REF_QUOTA_GROUP => $group));
	}

	function get_create_ref_quota_url($ref_id, $group)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_REF_QUOTA,
								    self :: PARAM_REF_QUOTA_ID => $ref_id,
									self :: PARAM_REF_QUOTA_GROUP => $group));
	}

	function get_delete_ref_quota_url($quota_id, $ref_id, $group)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_REF_QUOTA,
								    self :: PARAM_REF_QUOTA_ID => $ref_id,
								    self :: PARAM_QUOTA_ID => $quota_id,
									self :: PARAM_REF_QUOTA_GROUP => $group));
	}

	function get_admin_browse_subscription_url($reservation_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADMIN_BROWSE_SUBSCRIPTIONS,
								    self :: PARAM_RESERVATION_ID => $reservation_id));
	}

	function get_approve_subscription_url($subscription_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_APPROVE_SUBSCRIPTION,
								    self :: PARAM_SUBSCRIPTION_ID => $subscription_id));
	}

	function get_subscription_user_browser_url($subscription_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SUBSCRIPTION_USERS,
								    self :: PARAM_SUBSCRIPTION_ID => $subscription_id));
	}

	function get_subscription_user_updater_url($subscription_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_SUBSCRIPTION_USER,
								    self :: PARAM_SUBSCRIPTION_ID => $subscription_id));
	}

	function get_modify_rights_url($type, $id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_RIGHTS,
								    'type' => $type, 'id' => $id));
	}

 	function get_browse_quota_boxes_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_QUOTA_BOXES));
	}

	function get_create_quota_box_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_QUOTA_BOX));
	}

	function get_update_quota_box_url($quota_box_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_QUOTA_BOX,
								    self :: PARAM_QUOTA_BOX_ID => $quota_box_id));
	}

	function get_delete_quota_box_url($quota_box_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_QUOTA_BOX,
								    self :: PARAM_QUOTA_BOX_ID => $quota_box_id));
	}

 	function get_browse_category_quota_boxes_url($category_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CATEGORY_QUOTA_BOXES,
									self :: PARAM_CATEGORY_ID => $category_id));
	}

	function get_create_category_quota_box_url($category_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CATEGORY_QUOTA_BOX,
									self :: PARAM_CATEGORY_ID => $category_id));
	}

	function get_update_category_quota_box_url($category_quota_box_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_CATEGORY_QUOTA_BOX,
								    self :: PARAM_CATEGORY_QUOTA_BOX_ID => $category_quota_box_id));
	}

	function get_delete_category_quota_box_url($category_quota_box_id, $category_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CATEGORY_QUOTA_BOX,
								    self :: PARAM_CATEGORY_QUOTA_BOX_ID => $category_quota_box_id,
								    self :: PARAM_CATEGORY_ID => $category_id ));
	}

	function get_manage_overview_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_OVERVIEW));
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