<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once dirname(__FILE__).'/../../webapplication.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/configuration.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositoryutilities.class.php';
require_once dirname(__FILE__).'/../renderer/personal_calendar_mini_month_renderer.class.php';
require_once dirname(__FILE__).'/../renderer/personal_calendar_list_renderer.class.php';
require_once dirname(__FILE__).'/../renderer/personal_calendar_month_renderer.class.php';
require_once dirname(__FILE__).'/../renderer/personal_calendar_week_renderer.class.php';
require_once dirname(__FILE__).'/../renderer/personal_calendar_day_renderer.class.php';
require_once dirname(__FILE__).'/../connector/personal_calendar_weblcms_connector.class.php';
require_once dirname(__FILE__).'/../publisher/personalcalendarpublisher.class.php';
require_once dirname(__FILE__).'/../personalcalendarevent.class.php';
require_once dirname(__FILE__).'/../personalcalendardatamanager.class.php';
/**
 * This application gives each user the possibility to maintain a personal
 * calendar.
 */
class PersonalCalendar extends WebApplication
{
	const APPLICATION_NAME = 'personal_calendar';

	/**
	 * The owner of this personal calendar
	 */
	private $user;
	/**
	 * Constructor
	 * @param int $user_id
	 */
	public function PersonalCalendar($user)
	{
		parent :: __construct();
		$this->user = $user;
	}
	/**
	 * Runs the personal calendar application
	 */
	public function run()
	{
		if (isset ($_GET['publish']) && $_GET['publish'] == 1)
		{
			$_SESSION['personal_calendar_publish'] = true;
		}
		elseif (isset ($_GET['publish']) && $_GET['publish'] == 0)
		{
			$_SESSION['personal_calendar_publish'] = false;
		}
		if ($_SESSION['personal_calendar_publish'])
		{
			$out = '<p><a href="'.$this->get_url(array ('publish' => 0), true).'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/browser.gif" alt="'.get_lang('BrowserTitle').'" style="vertical-align:middle;"/> '.get_lang('BrowserTitle').'</a></p>';
			$publisher = new PersonalCalendarPublisher($this);
			$out .=  $publisher->as_html();
		}
		else
		{
			$out =  '<p><a href="'.$this->get_url(array ('publish' => 1), true).'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/publish.gif" alt="'.get_lang('Publish').'" style="vertical-align:middle;"/> '.get_lang('Publish').'</a></p>';
			$time = isset ($_GET['time']) ? intval($_GET['time']) : time();
			$view = isset ($_GET['view']) ? $_GET['view'] : 'month';
			$this->set_parameter('time', $time);
			$this->set_parameter('view', $view);
			$toolbar_data = array ();
			$toolbar_data[] = array ('href' => $this->get_url(array ('view' => 'list')), 'img' => api_get_path(WEB_CODE_PATH).'/img/calendar_down.gif', 'label' => get_lang('ListView'), 'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			$toolbar_data[] = array ('href' => $this->get_url(array ('view' => 'month')), 'img' => api_get_path(WEB_CODE_PATH).'/img/calendar_month.gif', 'label' => get_lang('MonthView'), 'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			$toolbar_data[] = array ('href' => $this->get_url(array ('view' => 'week')), 'img' => api_get_path(WEB_CODE_PATH).'/img/calendar_week.gif', 'label' => get_lang('WeekView'), 'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			$toolbar_data[] = array ('href' => $this->get_url(array ('view' => 'day')), 'img' => api_get_path(WEB_CODE_PATH).'/img/calendar_day.gif', 'label' => get_lang('DayView'), 'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
			$out .=  '<div style="margin-bottom: 1em;">'.RepositoryUtilities :: build_toolbar($toolbar_data).'</div>';
			$minimonthcalendar = new PersonalCalendarMiniMonthRenderer($this, $time);
			$out .=   '<div style="float: left; width: 20%;">';
			$out .=   $minimonthcalendar->render();
			$out .=   '</div>';
			$out .=   '<div style="float: left; width: 80%;">';
			$show_calendar = true;
			if(isset($_GET['pid']))
			{
				$pid = $_GET['pid'];
				$event = PersonalCalendarEvent::load($pid);
				if(isset($_GET['action']) && $_GET['action'] == 'delete')
				{
					$event->delete();
					$out .= Display::display_normal_message(get_lang('LearningObjectPublicationDeleted'),true);
				}
				else
				{
					$show_calendar = false;
					$learning_object = $event->get_event();
					$display = LearningObjectDisplay :: factory($learning_object);
					$out .= '<h3>'.$learning_object->get_title().'</h3>';
					$out  .= $display->get_full_html();
					$toolbar_data = array();
					$toolbar_data[] = array(
						'href' => $this->get_url(),
						'label' => get_lang('Back'),
						'img' => api_get_path(WEB_CODE_PATH).'img/prev.png',
						'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
					);
					$toolbar_data[] = array(
						'href' => $this->get_url(array('action'=>'delete','pid'=>$pid)),
						'label' => get_lang('Delete'),
						'img' => api_get_path(WEB_CODE_PATH).'img/delete.gif',
						'display' => RepositoryUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
					);
					$out .= RepositoryUtilities :: build_toolbar($toolbar_data, array(), 'margin-top: 1em;');
				}
			}
			if($show_calendar)
			{
				switch ($view)
				{
					case 'list' :
						$renderer = new PersonalCalendarListRenderer($this, $time);
						break;
					case 'day' :
						$renderer = new PersonalCalendarDayRenderer($this, $time);
						break;
					case 'week' :
						$renderer = new PersonalCalendarWeekRenderer($this, $time);
						break;
					default :
						$renderer = new PersonalCalendarMonthRenderer($this, $time);
						break;
				}
				$out .=   $renderer->render();
			}
			$out .=   '</div>';
		}
		Display :: display_header(get_lang('MyAgenda'));
		api_display_tool_title(get_lang('MyAgenda'));
		echo $out;
		Display :: display_footer();
	}
	/**
	 * Gets the events
	 * @param int $from_date
	 * @param int $to_date
	 */
	public function get_events($from_date, $to_date)
	{
		$dm = PersonalCalendarDatamanager::get_instance();
		$events = $dm->retrieve_personal_calendar_events($this->get_user_id());
		foreach($events as $index => $event)
		{
			$lo = $event->get_event();
			if(! ($lo->get_start_date() >= $from_date && $lo->get_start_date() <= $to_date))
			{
				unset($events[$index]);
			}
		}
		$connector = new PersonalCalendarWeblcmsConnector();
		$events = array_merge($events,$connector->get_events($this->user, $from_date, $to_date));
		return $events;
	}
	/**
	 * @see Application::learning_object_is_published()
	 */
	public function learning_object_is_published($object_id)
	{
		$dm = PersonalCalendarDatamanager::get_instance();
		return $dm->learning_object_is_published($object_id);
	}
	/**
	 * @see Application::any_learning_object_is_published()
	 */
	public function any_learning_object_is_published($object_ids)
	{
		$dm = PersonalCalendarDatamanager::get_instance();
		return $dm->any_learning_object_is_published($object_ids);
	}
	/**
	 * @see Application::get_learning_object_publication_attributes()
	 */
	public function get_learning_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		$dm = PersonalCalendarDatamanager::get_instance();
		return $dm->get_learning_object_publication_attributes($object_id, $type , $offset , $count , $order_property , $order_direction );
	}
	/**
	 * @see Application::get_learning_object_publication_attribute()
	 */
	public function get_learning_object_publication_attribute($publication_id)
	{
		$dm = PersonalCalendarDatamanager::get_instance();
		return $dm->get_learning_object_publication_attribute($publication_id);
	}
	/**
	 * @see Application::count_publication_attributes()
	 */
	public function count_publication_attributes($type = null, $condition = null)
	{
		$dm = PersonalCalendarDatamanager::get_instance();
		return $dm->count_publication_attributes($type, $condition );
	}
	/**
	 * @see Application::delete_learning_object_publications()
	 */
	public function delete_learning_object_publications($object_id)
	{
		$dm = PersonalCalendarDatamanager::get_instance();
		return $dm->delete_learning_object_publications($object_id);
	}
	/**
	 * @see Application::update_learning_object_publication_id()
	 */
	public function update_learning_object_publication_id($publication_attr)
	{
		return PersonalCalendarDatamanager :: get_instance()->update_learning_object_publication_id($publication_attr);
	}
	/**
	 * @see Application::get_application_platform_admin_links()
	 */
	public function get_application_platform_admin_links()
	{
		$links = array ();
		$links[] = array ('name' => get_lang('NoOptionsAvailable'), action => 'empty', 'url' => $this->get_link());
		return array ('application' => array ('name' => self :: APPLICATION_NAME, 'class' => self :: APPLICATION_NAME), 'links' => $links);
	}
	/**
	 * Gets a link to the personal calendar application
	 * @param array $parameters
	 * @param boolean $encode
	 */
	public function get_link($parameters = array (), $encode = false)
	{
		$link = 'index_'.self :: APPLICATION_NAME.'.php';
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
	/**
	 * Gets the user id of this personal calendars owner
	 * @return int
	 */
	function get_user_id()
	{
		return $this->user->get_user_id();
	}
}
?>