<?php
/**
 * @package application.profiler.profiler_manager.component
 */
require_once dirname(__FILE__) . '/../profiler_manager.class.php';
require_once dirname(__FILE__) . '/../profiler_manager_component.class.php';
require_once dirname(__FILE__) . '/../../category_manager/profiler_category_manager.class.php';

/**
 * Profiler component allows the user to manage course categories
 */
class ProfilerManagerCategoryManagerComponent extends ProfilerManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new BreadCrumb($this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES)), Translation :: get('MyProfiler')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ManageCategories')));
        
        $category_manager = new ProfilerCategoryManager($this, $trail);
        $category_manager->run();
    }
}
?>