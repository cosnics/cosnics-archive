<?php
/**
 * @package application.assessment.assessment.component
 */
require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';

/**
 * Component to delete assessment_publications objects
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerDeleterComponent extends AssessmentManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION);
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$assessment_publication = $this->retrieve_assessment_publication($id);
				
				if (!$assessment_publication->is_visible_for_target_user($this->get_user()))
				{
					$failures++;
				}
				else
				{
					if (!$assessment_publication->delete())
					{
						$failures++;
					}
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedAssessmentPublicationDeleted';
				}
				else
				{
					$message = 'SelectedAssessmentPublicationDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedAssessmentPublicationsDeleted';
				}
				else
				{
					$message = 'SelectedAssessmentPublicationsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoAssessmentPublicationsSelected')));
		}
	}
}
?>