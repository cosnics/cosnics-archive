<?php
require_once dirname(__FILE__).'/../../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublicationbrowser.class.php';
require_once dirname(__FILE__).'/descriptionpublicationlistrenderer.class.php';

class DescriptionBrowser extends LearningObjectPublicationBrowser
{
	function DescriptionBrowser($parent, $types)
	{
		parent :: __construct($parent, 'description');
		$renderer = new DescriptionPublicationListRenderer($this);
		$this->set_publication_list_renderer($renderer);
	}

	function get_publications($from, $count, $column, $direction)
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'description');
		$condition = $tool_condition;
		$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), null, $this->get_user_id(), $this->get_groups(), $condition, false, array (Description :: PROPERTY_DISPLAY_ORDER_INDEX), array (SORT_DESC));
		$visible_publications = array ();
		while ($publication = $publications->next_result())
		{
			// If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
			if (!$publication->is_visible_for_target_users() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
			{
				continue;
			}
			$visible_publications[] = $publication;
		}
		return $visible_publications;
	}

	function get_publication_count()
	{
		return count($this->get_publications());
	}
}
?>