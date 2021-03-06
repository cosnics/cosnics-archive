<?php
/**
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path(). 'lib/reporting_template.class.php';
class CourseTrackerReportingTemplate extends ReportingTemplate
{
	function CourseTrackerReportingTemplate($parent,$id,$params)
	{
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsAverageLearningpathScore"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsAverageExerciseScore"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsLastAccessToTools"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));

        parent :: __construct($parent,$id,$params);
	}

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'CourseTrackerReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'CourseTrackerReportingTemplateDescription';

        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
        $classname = 'CourseStudentTrackerReportingTemplate';
        $params = Reporting :: get_params($this);
        $manager = new WeblcmsManager();
        $url = $manager->get_reporting_url($classname, $params);

    	//template header
        $html[] = $this->get_header();

        $html[] = '<div class="reporting_center">';
        $html[] = '<a href="'.$url.'" />'.Translation :: get('CourseStudentTrackerReportingTemplateTitle').'</a> | ';
        $html[] = Translation :: get('CourseTrackerReportingTemplateTitle');
        $html[] = '</div><br />';

        //show visible blocks
        $html[] = '<div style="margin-left:auto;margin-right:auto;">';
        $html[] = $this->get_visible_reporting_blocks();
        $html[] = '</div>';

    	//template footer
        $html[] = $this->get_footer();

    	return implode("\n", $html);
    }
}
?>