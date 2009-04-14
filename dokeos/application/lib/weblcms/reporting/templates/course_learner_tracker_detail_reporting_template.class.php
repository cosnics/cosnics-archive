<?php
/**
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path(). 'lib/reporting_template.class.php';
class CourseLearnerTrackerDetailReportingTemplate extends ReportingTemplate
{
	function CourseLearnerTrackerDetailReportingTemplate($parent=null)
	{
        $this->parent = $parent;
        //UserUserInformation
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserUserInformation"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("UserCourseStatistics"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_BLOCK_DIMENSIONS));
        $this->add_reporting_block(ReportingDataManager :: get_instance()->retrieve_reporting_block_by_name("WeblcmsLatestAccess"),
            array(ReportingTemplate :: PARAM_VISIBLE => ReportingTemplate :: REPORTING_BLOCK_VISIBLE, ReportingTemplate :: PARAM_DIMENSIONS => ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS));
	}

    /**
     * @see ReportingTemplate -> get_properties()
     */
    public static function get_properties()
    {
        $properties[ReportingTemplateRegistration :: PROPERTY_TITLE] = 'CourseLearnerTrackerDetailReportingTemplateTitle';
        $properties[ReportingTemplateRegistration :: PROPERTY_PLATFORM] = 0;
        $properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = 'CourseLearnerTrackerDetailReportingTemplateDescription';

        return $properties;
    }

    /**
     * @see ReportingTemplate -> to_html()
     */
    function to_html()
    {
    	//template header
        $html[] = $this->get_header();

        $html[] = '<div class="reporting_template_container">';
        $html[] = '<div class="reporting_template_con_left">';
        $html[] = $this->get_reporting_block_html('UserUserInformation');
        $html[] = '</div>';
        $html[] = '<div class="reporting_template_con_right">';
        $html[] = $this->get_reporting_block_html('UserCourseStatistics');
        $html[] = '</div><div class="clear">&nbsp;</div>';
        $html[] = '</div>';

        $html[] = '<div class="reporting_template_container">';
        $html[] = $this->get_reporting_block_html('WeblcmsLatestAccess');
        $html[] = '</div>';
        //template menu
        //$html[] = $this->get_menu();

        //show visible blocks
        //$html[] = $this->get_visible_reporting_blocks();

    	//template footer
        $html[] = $this->get_footer();

    	return implode("\n", $html);
    }
}
?>