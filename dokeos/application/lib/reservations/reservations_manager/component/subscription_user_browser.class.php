<?php
/**
 * @package reservation.lib.reservation_manager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/subscription_user_browser/subscription_user_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class ReservationsManagerSubscriptionUserBrowserComponent extends ReservationsManagerComponent
{
	private $item;
	private $reservation;
	private $subscription;
	private $ab;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$this->subscription = $this->retrieve_subscriptions(new EqualityCondition(Subscription :: PROPERTY_ID, $this->get_subscription_id()))->next_result();
		$this->reservation = $this->retrieve_reservations(new EqualityCondition(Reservation :: PROPERTY_ID, $this->subscription->get_reservation_id()))->next_result();
		$this->item = $this->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $this->reservation->get_item()))->next_result();
		
		$trail = new BreadCrumbTrail();
		
		$trail->add(new BreadCrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
		
		if($this->get_user()->is_platform_admin())
		{
			$trail->add(new BreadCrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS)), Translation :: get('ManageItems')));
			$trail->add(new BreadCrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_RESERVATIONS, ReservationsManager :: PARAM_ITEM_ID => $this->item->get_id())), Translation :: get('ManageReservations')));
			$trail->add(new BreadCrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_SUBSCRIPTIONS, ReservationsManager :: PARAM_RESERVATION_ID => $this->reservation->get_id())), Translation :: get('ManageSubscriptions')));
		}
		else
		{
			$trail->add(new BreadCrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_SUBSCRIPTIONS)), Translation :: get('MySubscriptions')));			
		}
		
		$trail->add(new BreadCrumb($this->get_url(array(ReservationsManager :: PARAM_SUBSCRIPTION_ID => $this->subscription->get_id())), Translation :: get('View subscription')));
		
		$this->ab = $this->get_action_bar();
		
		$this->display_header($trail);
		
		echo $this->ab->as_html() . '<br />';
		
		$this->display_reservation_information();
		
		echo '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path().'action_users.png);">';
		echo '<div class="title">' . Translation :: get('Additional Users') . '</div>';
		echo '<div class="description">';
		echo $this->get_user_html();
		echo '</div></div>';
		
		$this->display_footer();
	}
	
	function get_user_html()
	{		
		$table = new SubscriptionUserBrowserTable($this, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_SUBSCRIPTIONS), $this->get_condition());
		
		$html = array();
		$html[] = $table->as_html();
		
		return implode($html, "\n");
	}
	
	function get_condition()
	{
		$subscription_id = $this->get_subscription_id();
		return new EqualityCondition(SubscriptionUser :: PROPERTY_SUBSCRIPTION_ID, $subscription_id);
	}
	
	function get_subscription_id()
	{
		return $_GET[ReservationsManager :: PARAM_SUBSCRIPTION_ID]?$_GET[ReservationsManager :: PARAM_SUBSCRIPTION_ID]:0;
	}
	
	function display_reservation_information()
	{
		$item = $this->item;
		$reservation = $this->reservation;
		$subscription = $this->subscription;
		
		$start = $subscription->get_start_time()?$subscription->get_start_time():$reservation->get_start_date();
		$stop = $subscription->get_stop_time()?$subscription->get_stop_time():$reservation->get_stop_date();
		
		//$responsible = UserDataManager :: get_instance()->retrieve_user($item->get_responsible())->get_fullname();
		$responsible = $item->get_responsible();
		$sub_user = UserDataManager :: get_instance()->retrieve_user($subscription->get_user_id())->get_fullname();
		
		$html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path().'treemenu_types/calendar_event.png);">';
		$html[] = '<div class="title">';
		$html[] = $item->get_name();
		$html[] = '</div>';
		$html[] = '<div class="description">';
		$html[] = $item->get_description();
		$html[] = '<b>' . Translation :: get('Responsible') . '</b>: ' . $responsible;
		$html[] = '<br /><b>' . Translation :: get('Type') . '</b>: ' . $this->get_type($reservation);
		$html[] = '<br /><b>' . Translation :: get('Start') . '</b>: ' . $start;
		$html[] = '<br /><b>' . Translation :: get('End') . '</b>: ' . $stop;
		$html[] = '<br /><b>' . Translation :: get('Reservator') . '</b>: ' . $sub_user;
		$html[] = '</div>';
		$html[] = '</div>';
		echo implode("\n", $html);
	}
	
	function get_type($reservation)
	{
		switch($reservation->get_type())
		{
			case Reservation :: TYPE_TIMEPICKER: return Translation :: get('Timepicker');
			case Reservation :: TYPE_BLOCK: return Translation :: get('Block');
		}
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ChangeAdditionalUsers'), Theme :: get_common_image_path().'action_edit.png', $this->get_subscription_user_updater_url($this->subscription->get_id()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		return $action_bar;
	}
}