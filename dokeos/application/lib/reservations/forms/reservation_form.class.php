<?php
/**
 * @package reservations.lib.forms
 */
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__).'/../reservation.class.php';
require_once dirname(__FILE__).'/../reservations_data_manager.class.php';

class ReservationForm extends FormValidator {

	const TYPE_CREATE = 1;
	const TYPE_EDIT = 2;
	const RESULT_SUCCESS = 'ReservationUpdated';
	const RESULT_ERROR = 'ReservationUpdateFailed';

	private $reservation;
	private $user;
	private $form_type;

	/**
	 * Creates a new LanguageForm
	 */
    function ReservationForm($form_type, $action, $reservation, $user) 
    {
    	parent :: __construct('reservation_form', 'post', $action);

		$this->reservation = $reservation;
		$this->user = $user;
		$this->form_type = $form_type;
		
		$this->build_basic_form();
		
		if ($this->form_type == self :: TYPE_EDIT)
			$this->build_editing_form();
		else
			$this->build_creating_form();

		$this->build_basic_form_footer();

		$this->setDefaults();
    }

    /**
     * Creates a new basic form
     */
    function build_basic_form()
    {	
    	//$this->addElement('html', '<div style="float: left;width: 100%;">');
    	
    	//Required
    	$this->addElement('html', '<div class="configuration_form">');
		$this->addElement('html', '<span class="category">' . Translation :: get('Required') . '</span>');
		
		$this->add_timewindow(Reservation :: PROPERTY_START_DATE, Reservation :: PROPERTY_STOP_DATE, 
							  Translation :: get('StartDate'), Translation :: get('StopDate'));
		$this->addRule(Reservation :: PROPERTY_START_DATE, Translation :: get('ThisFieldIsRequired'), 'required');
		$this->addRule(Reservation :: PROPERTY_STOP_DATE, Translation :: get('ThisFieldIsRequired'), 'required');
		
		$this->addElement('html', '<div style="clear: both;"></div>');
		$this->addElement('html', '</div>');
		
		//Subscription
		$this->addElement('html', '<div class="configuration_form">');
		$this->addElement('html', '<span class="category">' . Translation :: get('Subscription') . '</span>');
    	
    	$this->addElement('checkbox', 'use_subscription', Translation :: get('UseSubscription'));
		$this->add_timewindow(Reservation :: PROPERTY_START_SUBSCRIPTION, Reservation :: PROPERTY_STOP_SUBSCRIPTION, 
							  Translation :: get('StartSubscription'), Translation :: get('StopSubscription'));
		
		$this->addElement('html', '<div style="clear: both;"></div>');
		$this->addElement('html', '</div>');
		
    }
    
    function build_basic_form_footer()
    {
    	//Optional
		$this->addElement('html', '<div class="configuration_form">');
		$this->addElement('html', '<span class="category">' . Translation :: get('Optional') . '</span>');
    	
		$this->addElement('text', Reservation :: PROPERTY_MAX_USERS, Translation :: get('MaxUsers'));
		$this->addElement('html_editor', Reservation :: PROPERTY_NOTES, Translation :: get('Notes'));
		$this->addElement('checkbox', Reservation :: PROPERTY_AUTO_ACCEPT, null, Translation :: get('AutoAccept'));
		
		$this->addElement('html', '<div style="clear: both;"></div>');
		$this->addElement('html', '</div>');
		
		// Submit button
		$this->addElement('submit', 'submit', 'OK');
    }
    
    function build_creating_form()
    {
    	//Timepicker
		$this->addElement('html', '<div class="configuration_form">');
		$this->addElement('html', '<span class="category">' . Translation :: get('TimePicker') . '</span>');
    	
    	$this->addElement('checkbox', 'use_timepicker', Translation :: get('UseTimePicker'));
	
		$options = array();
		for($i = 0; $i < 1440; $i++)
			$options[$i] = $i;
		
		$this->addElement('select', Reservation :: PROPERTY_TIMEPICKER_MIN, Translation :: get('MinTimePicker'), $options);
		$this->addElement('select', Reservation :: PROPERTY_TIMEPICKER_MAX, Translation :: get('MaxTimePicker'), $options);
		
		$this->addElement('html', '<div style="clear: both;"></div>');
		$this->addElement('html', '</div>');
    	
    	//Repeat
		$this->addElement('html', '<div class="configuration_form">');
		$this->addElement('html', '<span class="category">' . Translation :: get('Repeat') . '</span>');
    	
    	$this->addElement('checkbox', 'repeat', Translation :: get('Repeat'));
	
		$this->addElement('text', 'repeat_every', Translation :: get('Repeat Every'));
		
		$options = array( 1 => Translation :: get('Hour(s)'), 24 => Translation :: get('Day(s)'), 168 => Translation :: get('Week(s)'));
		$this->addElement('select', 'repeat_every_select', '', $options);
		
		$this->add_datepicker('repeat_untill', Translation :: get('RepeatUntill'));
		
		$this->addElement('html', '<div style="clear: both;"></div>');
		$this->addElement('html', '</div>');
    }

    /**
     * Builds an editing form
     */
    function build_editing_form()
    {
    	$this->addElement('hidden', Reservation :: PROPERTY_ID);
    }

	function create_reservation()
	{
		$res = $this->reservation;
		$success = $res->create();
		
		$values = $this->exportValues();
		if($values['repeat'] == 1)
		{
			$recurrence_date_start = strtotime($res->get_start_date());
			$recurrence_date_end = strtotime($res->get_stop_date());
			
			$recurrence_period_end = strtotime($values['repeat_untill']);
			$recurrence_subscribe_from = strtotime($res->get_start_subscription());
			$recurrence_subscribe_until = strtotime($res->get_stop_subscription());
			
			while($recurrence_date_end < $recurrence_period_end)
			{
				$reservation = new Reservation();
				
				$recurrence_date_start = strtotime('+'.($values['repeat_every'] * $values['repeat_every_select']).' Hours',$recurrence_date_start);
				$recurrence_date_end = strtotime('+'.($values['repeat_every'] * $values['repeat_every_select']).' Hours',$recurrence_date_end);
				
				if ($values['use_subscription'] == 1) 
				{
					$recurrence_subscribe_from = strtotime('+'.($values['repeat_every'] * $values['repeat_every_select']).' Days',$recurrence_subscribe_from);
					$recurrence_subscribe_until = strtotime('+'.($values['repeat_every'] * $values['repeat_every_select']).' Days',$recurrence_subscribe_until);
					$reservation->set_start_subscription(DokeosUtilities :: to_db_date($recurrence_subscribe_from));
					$reservation->set_stop_subscription(DokeosUtilities :: to_db_date($recurrence_subscribe_until));
				}
				
				$reservation->set_item($res->get_item());
				$reservation->set_start_date(DokeosUtilities :: to_db_date($recurrence_date_start));
				$reservation->set_stop_date(DokeosUtilities :: to_db_date($recurrence_date_end));
				
				$status = $this->allow_create_reservation($reservation);
	
				if($status == 1)
					$reservation->create();
					
			}
		}
		
		return $success;
		
	}
	
	function allow_create_reservation($reservation)
	{
		if($this->validate())
		{
			$values = $this->exportValues();
			if(!isset($reservation))
				$reservation = $this->reservation;
			
			$reservation->set_notes($values[Reservation :: PROPERTY_NOTES]);
			
			if($reservation->get_start_date() == null)
			{
				$reservation->set_start_date($values[Reservation :: PROPERTY_START_DATE]);
				$reservation->set_stop_date($values[Reservation :: PROPERTY_STOP_DATE]);
			}	
			
			$reservation->set_max_users($values[Reservation :: PROPERTY_MAX_USERS]);
			$aa = $values[Reservation :: PROPERTY_AUTO_ACCEPT];
			$reservation->set_auto_accept($aa?$aa:0);
			$reservation->set_type(Reservation :: TYPE_BLOCK);
			
			if($values['use_subscription'] == 1 && $reservation->get_start_subscription() == null )
			{
				$reservation->set_start_subscription($values[Reservation :: PROPERTY_START_SUBSCRIPTION]);
				$reservation->set_stop_subscription($values[Reservation :: PROPERTY_STOP_SUBSCRIPTION]);
			}	
			
			if($values['use_timepicker'] == 1)
			{
				$reservation->set_type(Reservation :: TYPE_TIMEPICKER);
				$reservation->set_timepicker_min($values[Reservation :: PROPERTY_TIMEPICKER_MIN]);
				$reservation->set_timepicker_max($values[Reservation :: PROPERTY_TIMEPICKER_MAX]);
				$reservation->set_max_users(0);
			}
			
			return $reservation->allow_create();
		}
		
		return 0;
	}
	
	function allow_update_reservation()
	{
		if($this->validate())
		{
			$values = $this->exportValues();
			$reservation = $this->reservation;
			
			$reservation->set_notes($values[Reservation :: PROPERTY_NOTES]);
			$reservation->set_start_date($values[Reservation :: PROPERTY_START_DATE]);
			$reservation->set_stop_date($values[Reservation :: PROPERTY_STOP_DATE]);
			$reservation->set_max_users($values[Reservation :: PROPERTY_MAX_USERS]);
			$aa = $values[Reservation :: PROPERTY_AUTO_ACCEPT];
			$reservation->set_auto_accept($aa?$aa:0);
			
			if($values['use_subscription'] == 1)
			{
				$reservation->set_start_subscription($values[Reservation :: PROPERTY_START_SUBSCRIPTION]);
				$reservation->set_stop_subscription($values[Reservation :: PROPERTY_STOP_SUBSCRIPTION]);
			}	
			else
			{
				$reservation->set_start_subscription(null);
				$reservation->set_stop_subscription(null);
			}
			
			return $reservation->allow_update();
		}
		
		return 0;
		
	}

    function update_reservation()
    {	
		return $this->reservation->update();
    }

	/**
	 * Sets default values.
	 * @param array $defaults Default values for this form's parameters.
	 */
	function setDefaults($defaults = array ())
	{
		$reservation = $this->reservation;
		$defaults[Reservation :: PROPERTY_ID] = $reservation->get_id();
		
		$start_date = $reservation->get_start_date()?$reservation->get_start_date():date('Y-m-d H:i:s');
		$stop_date = $reservation->get_stop_date()?$reservation->get_stop_date():date('Y-m-d H:i:s', time() + (60 * 60));
		$subscription_start_date = $reservation->get_start_subscription()?$reservation->get_start_subscription():date('Y-m-d H:i:s', time() - (60 * 60));
		$subscription_stop_date = $reservation->get_stop_subscription()?$reservation->get_stop_subscription():date('Y-m-d H:i:s');

		$defaults[Reservation :: PROPERTY_START_DATE] = $start_date;
		$defaults[Reservation :: PROPERTY_STOP_DATE] = $stop_date;
		$defaults[Reservation :: PROPERTY_START_SUBSCRIPTION] = $subscription_start_date;
		$defaults[Reservation :: PROPERTY_STOP_SUBSCRIPTION] = $subscription_stop_date;
		$defaults[Reservation :: PROPERTY_NOTES] = $reservation->get_notes();
		$aa = $reservation->get_auto_accept();
		$defaults[Reservation :: PROPERTY_AUTO_ACCEPT] = $aa?$aa:1;
		$defaults[Reservation :: PROPERTY_MAX_USERS] = $reservation->get_max_users()?$reservation->get_max_users():1;
		$defaults[Reservation :: PROPERTY_TIMEPICKER_MIN] = $reservation->get_timepicker_min();
		$defaults[Reservation :: PROPERTY_TIMEPICKER_MAX] = $reservation->get_timepicker_max();
		
		$defaults['use_timepicker'] = ($reservation->get_type() == Reservation :: TYPE_TIMEPICKER);
		$subscription = (($reservation->get_start_subscription() != 0) && ($reservation->get_stop_subscription() != 0));
		$defaults['use_subscription'] = $subscription;
		$defaults['repeat_every'] = 1;
		$defaults['repeat_untill'] = date('Y-m-d H:i:s', time() + (14 * 24 * 60 * 60));
		parent :: setDefaults($defaults);
	}
}
?>