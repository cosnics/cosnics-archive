<?php
/**
 * @author Sven Vanpoucke
 */
require_once Path :: get_reporting_path(). 'lib/reporting_template.class.php';
require_once Path :: get_reporting_path().'lib/reporting_manager/reporting_manager.class.php';
require_once dirname(__FILE__) . '/../../trackers/assessment_assessment_attempts_tracker.class.php';

class AssessmentAttemptsTemplate extends ReportingTemplate
{
	private $pid;
	
	function AssessmentAttemptsTemplate($parent=null,$id,$params,$trail, $pid)
	{
		$this->pid = $pid;
		
		$this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("AssessmentAttempts"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
            
        parent :: __construct($parent,$id,$params,$trail);
        
        $this->action_bar->add_common_action(new ToolbarItem(Translation :: get('DeleteAllResults'), Theme :: get_common_image_path().'action_delete.png', $params['url'] . '&delete=aid_' . $pid, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $this->action_bar->add_common_action(new ToolbarItem(Translation :: get('DownloadDocuments'), Theme :: get_common_image_path().'action_export.png', $params['url'], ToolbarItem :: DISPLAY_ICON_AND_LABEL));
	}

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'AssessmentAttemptsTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'AssessmentAttemptsTemplateDescription';

        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
    	//template header
        $html[] = $this->get_header();
        //$html[] = '<div class="reporting_center">';
        //show visible blocks
        
        $html[] = $this->get_learning_object_data();
        
        $html[] = $this->get_visible_reporting_blocks();
        //$html[] = '</div>';
    	//template footer
        $html[] = $this->get_footer();

    	return implode("\n", $html);
    }
    
    function get_learning_object_data()
    {
    	$pub = AssessmentDataManager :: get_instance()->retrieve_assessment_publication($this->pid);
    	$assessment = $pub->get_publication_object();
    	
    	$html[] = '<div class="learning_object" style="background-image: url('. Theme :: get_common_image_path(). 'learning_object/assessment.png);">';
		$html[] = '<div class="title">';
		$html[] = $assessment->get_title();
		$html[] = '</div>';
		$html[] = $assessment->get_description();
		$html[] = '<div class="title">';
		$html[] = Translation :: get('Statistics');
		$html[] = '</div>';
		$track = new AssessmentAssessmentAttemptsTracker();
		
		$avg = $track->get_average_score($pub);
		if (!isset($avg))
		{
			$avg_line = 'No results';
		}
		else
		{
			$avg_line = $avg . '%';
		}
		$html[] = Translation :: get('AverageScore').': '.$avg_line;
		$html[] = '<br/>'.Translation :: get('TimesTaken').': '.$track->get_times_taken($pub);
		$html[] = '</div>';
		
		return implode("\n", $html);
    }
}
?>