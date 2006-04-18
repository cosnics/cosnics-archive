<?php
require_once dirname(__FILE__).'/../repositorytool.class.php';
/**
 * This tool allows a user to publish announcements in his or her course.
 */
class AnnouncementTool extends RepositoryTool
{
	/*
	 * Inherited.
	 */
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			api_not_allowed();
			return;
		}
		if (isset($_GET['announcementadmin']))
		{
			$_SESSION['announcementadmin'] = $_GET['announcementadmin'];
		}
		if ($_SESSION['announcementadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			echo '<p>Go to <a href="' . $this->get_url(array('announcementadmin' => 0), true) . '">User Mode</a> &hellip;</p>';
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher($this, 'announcement');
			echo $pub->as_html();
		}
		else
		{
			if($this->is_allowed(ADD_RIGHT))
			{
				echo '<p>Go to <a href="' . $this->get_url(array('announcementadmin' => 1), true) . '">Publisher Mode</a> &hellip;</p>';
			}
			$this->perform_requested_actions();
			$this->display();
		}
	}

	/**
	 * Display the list of announcements
	 */
	function display()
	{
		$announcement_publications = $this->get_announcement_publications();
		$number_of_announcements = count($announcement_publications);
		foreach($announcement_publications as $index => $announcement_publication)
		{
			// If the announcement is hidden and the user is not allowed to DELETE or EDIT, don't show this announcement
			if($announcement_publication->is_hidden() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
			{
				continue;
			}
			$announcement = $announcement_publication->get_learning_object();
			$target_users = $announcement_publication->get_target_users();
			$delete_url = $this->get_url(array('action'=>'delete','pid'=>$announcement_publication->get_id()), true);
			$visible_url = $this->get_url(array('action'=>'toggle_visibility','pid'=>$announcement_publication->get_id()), true);

			if($index != 0)
			{
				$up_img = 'up.gif';
				$up_url = $this->get_url(array('action'=>'move_up','pid'=>$announcement_publication->get_id()), true);
				$up_link = '<a href="'.$up_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/'.$up_img.'"/></a>';
			}
			else
			{
				$up_link = '<img src="'.api_get_path(WEB_CODE_PATH).'/img/up_na.gif"/></a>';
			}
			if($index != $number_of_announcements-1)
			{
				$down_img = 'down.gif';
				$down_url = $this->get_url(array('action'=>'move_down','pid'=>$announcement_publication->get_id()), true);
				$down_link = '<a href="'.$down_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/'.$down_img.'"/></a>';
			}
			else
			{
				$down_link = '<img src="'.api_get_path(WEB_CODE_PATH).'/img/down_na.gif"/></a>';
			}
			$visibility_img = ($announcement_publication->is_hidden() ? 'invisible.gif' : 'visible.gif');

			$users = $announcement_publication->get_target_users();
			$groups = $announcement_publication->get_target_groups();
			if(count($users) == 0 && count($groups) == 0)
			{
				$target_list = get_lang('Everybody');
			}
			else
			{
				$target_list = array();
				$target_list[] = '<select>';
				foreach($users as $index => $user_id)
				{
					$user = api_get_user_info($user_id);
					$target_list[] = '<option>'.$user['firstName'].' '.$user['lastName'].'</option>';
				}
				foreach($groups as $index => $group_id)
				{
					//TODO: replace group id by group name (gives SQL-error now)
					//$group = GroupManager::get_group_properties($group_id);
					//$target_list[] = '<option>'.$group['name'].'</option>';
					$target_list[] = '<option>'.'GROUP: '.$group_id.'</option>';
				}
				$target_list[] = '</select>';
				$target_list = implode("\n",$target_list);
			}
			$publisher = api_get_user_info($announcement_publication->get_publisher_id());
			$html = array();
			$html[] = '<div class="learning_object">';
			$html[] = '<div class="icon"><img src="'.api_get_path(WEB_CODE_PATH).'img/'.$announcement->get_type().'.gif" alt="'.$announcement->get_type().'"/></div>';
			$html[] = '<div class="title'.($announcement_publication->is_hidden() ? ' invisible':'').'">'.htmlentities($announcement->get_title()).'</div>';
			$html[] = '<div class="description'.($announcement_publication->is_hidden() ? ' invisible':'').'">'.$announcement->get_description();
			$html[] = '<br /><i>';
			//TODO: date-formatting
			$html[] = get_lang('PublishedOn').' '.date('r',$announcement_publication->get_publication_date());
			$html[] = get_lang('By').' '.$publisher['firstName'].' '.$publisher['lastName'].'. ';
			$html[] = get_lang('SentTo').': ';
			$html[] = $target_list;
			$html[] = '</i>';
			$html[] = '<br />';
			if($this->is_allowed(DELETE_RIGHT))
			{
				$html[] = '<a href="'.$delete_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/delete.gif"/></a>';
			}
			if($this->is_allowed(EDIT_RIGHT))
			{
				$html[] = '<a href="'.$visible_url.'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/'.$visibility_img.'"/></a>';
				$html[] = $up_link;
				$html[] = $down_link;
			}
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '<br /><br />';
			echo implode("\n",$html);
		}
	}
	/**
	 * Get the list of published announcements
	 * @return array An array with all publications of announcements
	 */
	function get_announcement_publications()
	{
		$datamanager = WebLCMSDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'announcement');
		$from_date_condition = new InequalityCondition(LearningObjectPublication :: PROPERTY_FROM_DATE,InequalityCondition::LESS_THAN_OR_EQUAL,time());
		$to_date_condition = new InequalityCondition(LearningObjectPublication :: PROPERTY_TO_DATE,InequalityCondition::GREATER_THAN_OR_EQUAL,time());
		$publication_period_cond = new AndCondition($from_date_condition,$to_date_condition);
		$forever_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_FROM_DATE,0);
		$time_condition = new OrCondition($publication_period_cond,$forever_condition);
		$condition = new AndCondition($tool_condition,$time_condition);
		$announcement_publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $this->get_user_id(), $this->get_groups(),$condition);
		return $announcement_publications;
	}
}
?>