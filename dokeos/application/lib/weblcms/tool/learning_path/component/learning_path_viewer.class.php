<?php
require_once dirname(__FILE__).'/../../../content_object_repo_viewer.class.php';
//require_once Path::get_library_path().'/html/action_bar/action_bar_renderer.class.php';
//require_once Path :: get_repository_path() . 'lib/complex_content_object_menu.class.php';
require_once dirname(__FILE__) . '/learning_path_viewer/learning_path_tree.class.php';
require_once dirname(__FILE__) . '/learning_path_viewer/learning_path_content_object_display.class.php';
require_once dirname(__FILE__).'/../../../trackers/weblcms_lp_attempt_tracker.class.php';
require_once dirname(__FILE__).'/../../../trackers/weblcms_lpi_attempt_tracker.class.php';
require_once dirname(__FILE__).'/../../../trackers/weblcms_lpi_attempt_objective_tracker.class.php';
require_once dirname(__FILE__).'/../../../trackers/weblcms_learning_path_question_attempts_tracker.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/complex_display.class.php';
require_once dirname(__FILE__) . '/learning_path_viewer/prerequisites_translator.class.php';

class LearningPathToolViewerComponent extends LearningPathToolComponent
{
	private $pid;
	private $trackers;
	private $lpi_attempt_data;
	private $cloi;

	function run()
	{
		// Check for rights
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();
		$trail->add_help('courses learnpath tool');

		// Check and retrieve publication
		$pid = Request :: get('pid');
		$this->pid = $pid;

		if(!$pid)
		{
			$this->display_header($trail, true);
			$this->display_error_message(Translation :: get('NoObjectSelected'));
			$this->display_footer();
		}

		$dm = WeblcmsDataManager :: get_instance();
		$publication = $dm->retrieve_content_object_publication($pid);
		$root_object = $publication->get_content_object();

		// Do tracking stuff
		$this->trackers['lp_tracker'] = $this->retrieve_lp_tracker($publication);
		$lpi_attempt_data = $this->retrieve_tracker_items($this->trackers['lp_tracker']);

		// Retrieve tree menu
		if(Request :: get('lp_action') == 'view_progress')
		{
			$step = null;
		}
		else
		{
			$step = Request :: get(LearningPathTool :: PARAM_LP_STEP)?Request :: get(LearningPathTool :: PARAM_LP_STEP):1;
		}

		$menu = $this->get_menu($root_object->get_id(), $step, $pid, $lpi_attempt_data);
		$object = $menu->get_current_object();
		$cloi = $menu->get_current_cloi();
		$this->cloi = $cloi;

		// Update main tracker
		$this->trackers['lp_tracker']->set_progress($menu->get_progress());
		$this->trackers['lp_tracker']->update();

		$trail->merge($menu->get_breadcrumbs());

		$navigation = $this->get_navigation_menu($menu->count_steps(), $step, $object, $menu);
		$objects = $menu->get_objects();
		
		// Retrieve correct display and show it on screen
		if(Request :: get('lp_action') == 'view_progress')
		{
			$url = $this->get_url(array('tool_action' => 'view', 'pid' => $pid, 'lp_action' => 'view_progress'));
			require_once(Path :: get_application_path() . 'lib/weblcms/reporting/templates/learning_path_progress_reporting_template.class.php');
			
			$cid = Request :: get('cid');
			$details = Request :: get('details');

			if($cid)
			{
				$trail->add(new BreadCrumb($this->get_url(array('tool_action' => 'view', 'pid' => $pid, 'lp_action' => 'view_progress', 'cid' => $cid)), Translation :: get('ItemDetails')));
			}

			if($details)
			{
				$trail->add(new BreadCrumb($this->get_url(array('tool_action' => 'view', 'pid' => $pid, 'lp_action' => 'view_progress', 'cid' => $cid, 'details' => $details)), Translation :: get('AssessmentResult')));
				
				$this->set_parameter('tool_action', 'view');
				$this->set_parameter('pid', $pid);
				$this->set_parameter('lp_action', 'view_progress');
				$this->set_parameter('cid', $cid);
				$this->set_parameter('details', $details);
				$_GET['display_action'] = 'view_result';
				
				$object = $objects[$cid];
				
				$display = ComplexDisplay :: factory($this, $object->get_type());
        		$display->set_root_lo($object);				
			}
			else 
			{
				$parameters = array('objects' => $objects, 'attempt_data' => $lpi_attempt_data, 'cid' => $cid, 'url' => $url);
				$template = new LearningPathProgressReportingTemplate($this ,0, $parameters, $trail, $objects[$cid]);
				$template->set_reporting_blocks_function_parameters($parameters);
				$display = $template->to_html();
			}
		}
		else
		{
			if($cloi)
			{
				$allowed = true;
				
				if($root_object->get_version() != 'SCORM2004')
				{
					$translator = new PrerequisitesTranslator($lpi_attempt_data, $objects, $root_object->get_version());
					if(!$translator->can_execute_item($cloi))
					{
						$display = '<div class="error-message">' . Translation :: get('NotYetAllowedToView') . '</div>';
						$allowed = false;	
					}
				}
				
				if($allowed)
				{
					$lpi_tracker = $menu->get_current_tracker();
					if(!$lpi_tracker)
					{
						$lpi_tracker = $this->create_lpi_tracker($this->trackers['lp_tracker'], $cloi);
						$lpi_attempt_data[$cloi->get_id()]['active_tracker'] = $lpi_tracker;
					}
					else
					{
						$lpi_tracker->set_start_time(time());
						$lpi_tracker->update();
					}
	
					$this->trackers['lpi_tracker'] = $lpi_tracker;
	
					$display = LearningPathContentObjectDisplay :: factory($this, $object->get_type())->display_content_object($object, $lpi_attempt_data[$cloi->get_id()], $menu->get_continue_url(), $menu->get_previous_url(), $menu->get_jump_urls());
				}
			}
			else
			{
				$this->display_header($trail, true);
				$this->display_error_message(Translation :: get('EmptyLearningPath'));
				$this->display_footer();
				exit();
			}
		}

		$this->display_header($trail, true);
		//echo '<br />';
		echo '<div style="width: 17%; overflow: auto; float: left;">';
		echo $menu->render_as_tree(). '<br /><br />';
		echo $this->get_progress_bar($menu->get_progress());
		echo $navigation . '<br /><br />';
		echo '</div>';
		echo '<div style="width: 82%; float: right; padding-left: 10px; min-height: 500px;">';
		
		if(get_class($display) == 'AssessmentDisplay')
		{
			$display->run();
		}
		else 
		{
			echo $display;
		}
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		$this->display_footer();
	}

	/**
	 * Creates the tree menu for the learning path
	 *
	 * @param int $root_object_id
	 * @param int $selected_object_id
	 * @param int $pid
	 * @param LearningPathAttemptTracker $lp_tracker
	 * @return HTML code of the menu
	 */
	private function get_menu($root_object_id, $selected_object_id, $pid, $lp_tracker)
	{
		$menu = new LearningPathTree($root_object_id, $selected_object_id,
			Path :: get(WEB_PATH) . 'run.php?go=courseviewer&course=' . Request :: get('course') . '&application=weblcms&tool=learning_path&tool_action=view&pid=' .
			$pid . '&'.LearningPathTool :: PARAM_LP_STEP.'=%s', $lp_tracker);

		return $menu;
	}

	// Getters & Setters

	function get_publication_id()
	{
		return $this->pid;
	}

	function get_trackers()
	{
		return $this->trackers;
	}
	
	function get_cloi()
	{
		return $this->cloi;
	}

	// Layout functionality

	/**
	 * Retrieves the navigation menu for the learning path
	 *
	 * @param int $total_steps
	 * @param int $current_step
	 * @param ContentObject - The current object
	 * @return HTML of the navigation menu
	 */
	private function get_navigation_menu($total_steps, $current_step, $object, $menu)
	{
		if(!$current_step)
		{
			$previous_url = $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH, LearningPathTool :: PARAM_PUBLICATION_ID => Request :: get('pid'), 'step' => $total_steps));

			$actions[] = array(
				'href' => $previous_url,
				'label' => Translation :: get('Previous'),
				'img' => Theme :: get_common_image_path().'action_prev.png'
			);

			$actions[] = array(
				'label' => Translation :: get('NextNA'),
				'img' => Theme :: get_common_image_path().'action_next_na.png'
			);
		}
		else
		{

			if(get_class($object) == 'ScormItem')
			{
				$hide_lms_ui = $object->get_hide_lms_ui();
			}

			if(!$hide_lms_ui) $hide_lms_ui = array($hide_lms_ui);

			$add_previous_na = false;

			if($current_step > 1 && $menu->get_previous_url())
			{
				//$previous_url = $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH, LearningPathTool :: PARAM_PUBLICATION_ID => Request :: get('pid'), 'step' => $current_step - 1));
				$previous_url = $menu->get_previous_url();

				if(!in_array('previous', $hide_lms_ui))
				{
					$actions[] = array(
						'href' => $previous_url,
						'label' => Translation :: get('Previous'),
						'img' => Theme :: get_common_image_path().'action_prev.png'
					);
				}
				else
				{
					$add_previous_na = true;
				}
			}
			else
			{
				$add_previous_na = true;
			}

			if($add_previous_na)
			{
				$actions[] = array(
					'label' => Translation :: get('PreviousNA'),
					'img' => Theme :: get_common_image_path().'action_prev_na.png'
				);
			}

			$add_continue_na = false;

			if(($current_step < $total_steps))
			{
				//$continue_url = $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH, LearningPathTool :: PARAM_PUBLICATION_ID => Request :: get('pid'), 'step' => $current_step + 1));

				$continue_url = $menu->get_continue_url();

				if(!in_array('continue', $hide_lms_ui))
				{
					$actions[] = array(
						'href' => $continue_url,
						'label' => Translation :: get('Next'),
						'img' => Theme :: get_common_image_path().'action_next.png'
					);
				}
				else
				{
					$add_continue_na = true;
				}
			}
			else
			{
				//$continue_url = $this->get_url(array(Tool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_LEARNING_PATH, LearningPathTool :: PARAM_PUBLICATION_ID => Request :: get('pid'), 'lp_action' => 'view_progress'));

				$continue_url = $menu->get_continue_url();

				if(!in_array('continue', $hide_lms_ui))
				{
					$actions[] = array(
						'href' => $continue_url,
						'label' => Translation :: get('Next'),
						'img' => Theme :: get_common_image_path().'action_next.png'
					);
				}
				else
				{
					$add_continue_na = true;
				}
			}

			if($add_continue_na)
			{
				$actions[] = array(
						'label' => Translation :: get('NextNA'),
						'img' => Theme :: get_common_image_path().'action_next_na.png'
					);
			}
		}

		return DokeosUtilities :: build_toolbar($actions);
	}

	/**
	 * Retrieves the progress bar for the learning path
	 *
	 * @param int $progress - The current progress
	 * @return HTML code of the progress bar
	 */
	private function get_progress_bar($progress)
	{
		$html[] = '<div style="position: relative; text-align: center; border: 1px solid black; height: 14px; width:100px;">';
		$html[] = '<div style="background-color: lightblue; height: 14px; width:' . $progress . 'px; text-align: center;">';
		$html[] = '</div>';
		$html[] = '<div style="width: 100px; text-align: center; position: absolute; top: 0px;">' . round($progress) . '%</div></div>';

		return implode("\n", $html);
	}

	// Tracker functionality

	/**
	 * Retrieves the learning path tracker for the current user
	 * @param LearningPath $lp
	 * @return A LearningPathAttemptTracker
	 */
	private function retrieve_lp_tracker($lp)
	{
		$conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_LP_ID, $lp->get_id());
		$conditions[] = new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_USER_ID, $this->get_user_id());
		//$conditions[] = new NotCondition(new EqualityCondition(WeblcmsLpAttemptTracker :: PROPERTY_PROGRESS, 100));
		$condition = new AndCondition($conditions);

		$dummy = new WeblcmsLpAttemptTracker();
		$trackers = $dummy->retrieve_tracker_items($condition);
		$lp_tracker = $trackers[0];

		if(!$lp_tracker)
		{
			$return = Events :: trigger_event('attempt_learning_path', 'weblcms', array('user_id' => $this->get_user_id(), 'course_id' => $this->get_course_id(), 'lp_id' => $lp->get_id()));
			$lp_tracker = $return[0];
		}

		return $lp_tracker;
	}

	/**
	 * Retrieve the tracker items for the current LearningPathAttemptTracker
	 *
	 * @param LearningPathAttemptTracker $lp_tracker
	 * @return array of LearningPathItemAttemptTracker
	 */
	private function retrieve_tracker_items($lp_tracker)
	{
		$lpi_attempt_data = array();

		$condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_LP_VIEW_ID, $lp_tracker->get_id());

		$dummy = new WeblcmsLpiAttemptTracker();
		$trackers = $dummy->retrieve_tracker_items($condition);

		foreach($trackers as $tracker)
		{
			$item_id = $tracker->get_lp_item_id();
			if(!$lpi_attempt_data[$item_id])
			{
				$lpi_attempt_data[$item_id]['score'] = 0;
				$lpi_attempt_data[$item_id]['time'] = 0;
			}

			$lpi_attempt_data[$item_id]['trackers'][] = $tracker;
			$lpi_attempt_data[$item_id]['size']++;
			$lpi_attempt_data[$item_id]['score'] += $tracker->get_score();
			if($tracker->get_total_time())
				$lpi_attempt_data[$item_id]['time'] += $tracker->get_total_time();

			if($tracker->get_status() == 'completed' || $tracker->get_status() == 'passed')
				$lpi_attempt_data[$item_id]['completed'] = 1;
			else
				$lpi_attempt_data[$item_id]['active_tracker'] = $tracker;
		}
		//dump($lpi_attempt_data);
		return $lpi_attempt_data;

	}

	/**
	 * Creates a learning path item tracker
	 *
	 * @param LearningPathAttemptTracker $lp_tracker
	 * @param ComplexContentObjectItem $current_cloi
	 * @return array LearningPathItemAttemptTracker
	 */
	private function create_lpi_tracker($lp_tracker, $current_cloi)
	{
		$return = Events :: trigger_event('attempt_learning_path_item', 'weblcms', array('lp_view_id' => $lp_tracker->get_id(), 'lp_item_id' => $current_cloi->get_id(), 'start_time' => time(), 'status' => 'not attempted'));
		$lpi_tracker = $return[0];

		return $lpi_tracker;
	}

	function retrieve_assessment_results()
	{
		$condition = new EqualityCondition(WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_LPI_ATTEMPT_ID, Request :: get('details'));

		$dummy = new WeblcmsLearningPathQuestionAttemptsTracker();
		$trackers = $dummy->retrieve_tracker_items($condition);
		
		$results = array();
		
		foreach($trackers as $tracker)
		{
			$results[$tracker->get_question_cid()] = array(
				'answer' => $tracker->get_answer(),
				'feedback' => $tracker->get_feedback(),
				'score' => $tracker->get_score() 
			);
		}
		
		return $results;
	}
	
	function can_change_answer_data()
	{
		return false;
	}
}
?>
