<?php
/**
 * @author: Michael Kyndt
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once Path :: get_reporting_path().'lib/reporting_template_registration.class.php';
/**
 * TODO: Add comment
 */
class DefaultReportingTemplateRegistrationTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultReportingTemplateRegistrationTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $reporting_template_registration)
	{
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
                case ReportingTemplateRegistration :: PROPERTY_APPLICATION:
                    return Translation :: get($reporting_template_registration->get_application());
				case ReportingTemplateRegistration :: PROPERTY_TITLE :
					return $reporting_template_registration->get_title();
				case ReportingTemplateRegistration :: PROPERTY_DESCRIPTION :
					$description = strip_tags($reporting_template_registration->get_description());
					if(strlen($description) > 203)
					{
						mb_internal_encoding("UTF-8");
						$description = mb_substr(strip_tags($reporting_template_registration->get_description()),0,200).'&hellip;';
					}
					return $description;
			}
		}
		return '&nbsp;';
	}
	
	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>