<?php
/**
 * @package repository.publicationtable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../learning_object.class.php';
require_once dirname(__FILE__).'/../learning_object_publication_attributes.class.php';
/**
 * TODO: Add comment
 */
class DefaultPublicationTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultPublicationTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $learning_object_publication)
	{
		switch ($column->get_name())
		{
			case LearningObjectPublicationAttributes :: PROPERTY_PUBLICATION_OBJECT :
				return $learning_object_publication->get_publication_object_id();
			case LearningObjectPublicationAttributes :: PROPERTY_APPLICATION :
				return DokeosUtilities :: underscores_to_camelcase_with_spaces($learning_object_publication->get_application());
			case LearningObjectPublicationAttributes :: PROPERTY_LOCATION :
				$application = $learning_object_publication->get_application();

				if($application == 'weblcms')
				{
					$location = $learning_object_publication->get_location();
					$codes = explode("&gt;",$location);
					$course_id = trim($codes[0]);
					$tool = trim($codes[1]);

					$wdm = WeblcmsDataManager :: get_instance();
					$course = $wdm->retrieve_course($course_id);
					return $course->get_name() . ' > ' . $tool;
				}

				return $learning_object_publication->get_location();
			case LearningObject :: PROPERTY_TITLE :

				/*$application = $learning_object_publication->get_application();
				$url = 'run.php?application=' . DokeosUtilities :: camelcase_to_underscores($application);

				if($application == 'weblcms')
				{
					$location = $learning_object_publication->get_location();
					$codes = explode("&gt;",$location);
					$course = trim($codes[0]);
					$tool = trim($codes[1]);

					if(stripos($tool, '_feedback'))
						$tool = substr($tool, 0, stripos($tool, '_feedback'));

					$url .= '&go=courseviewer&course=' . $course . '&tool=' . $tool . '&tool_action=view';
				}
				else
				{
					//$url .= '&go=view';
				}*/
				
				$url = $learning_object_publication->get_url();
				$url = '<a href="' . $url . '">';
				
				return $url . $learning_object_publication->get_publication_object()->get_title() . '</a>';
			case LearningObjectPublicationAttributes :: PROPERTY_PUBLICATION_DATE :
				return date('Y-m-d, H:i', $learning_object_publication->get_publication_date());
			default :
			    return '&nbsp;';
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>