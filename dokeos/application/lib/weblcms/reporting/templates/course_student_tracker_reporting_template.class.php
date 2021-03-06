<?php
/**
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path(). 'lib/reporting_template.class.php';
class CourseStudentTrackerReportingTemplate extends ReportingTemplate
{
	function CourseStudentTrackerReportingTemplate($parent,$id,$params)
	{
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserTracking"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));

        parent :: __construct($parent,$id,$params);
	}

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'CourseStudentTrackerReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'CourseStudentTrackerReportingTemplateDescription';

        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
        $classname = 'CourseTrackerReportingTemplate';
        $params = Reporting :: get_params($this);
        $manager = new WeblcmsManager();
        $url = $manager->get_reporting_url($classname, $params);

    	//template header
        $html[] = $this->get_header();

        $html[] = '<div class="reporting_center">';
        $html[] = Translation :: get('CourseStudentTrackerReportingTemplateTitle').' | ';
        $html[] = '<a href="'.$url.'" />'.Translation :: get('CourseTrackerReportingTemplateTitle').'</a>';
        $html[] = '</div><br />';
        
        //show visible blocks
        $html[] = $this->get_visible_reporting_blocks();

    	//template footer
        $html[] = $this->get_footer();

    	return implode("\n", $html);
    }
}
?>