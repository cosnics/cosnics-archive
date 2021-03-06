<?php
/**
 * @package application.assessment.assessment.component
 */
require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';
require_once dirname(__FILE__).'/../../category_manager/assessment_publication_category_manager.class.php';

/**
 * Component to manage assessment publication categories
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerCategoryManagerComponent extends AssessmentManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ManageCategories')));
        $category_manager = new AssessmentPublicationCategoryManager($this, $trail);
        $category_manager->run();
	}
}
?>