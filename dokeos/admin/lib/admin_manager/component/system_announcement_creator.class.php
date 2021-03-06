<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__) . '/../admin_manager.class.php';
require_once dirname(__FILE__) . '/../admin_manager_component.class.php';
require_once Path :: get_application_library_path() . 'repo_viewer/repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../announcer/system_announcement_multipublisher.class.php';

class AdminManagerSystemAnnouncementCreatorComponent extends AdminManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_BROWSE_SYSTEM_ANNOUNCEMENTS)), Translation :: get('SystemAnnouncements')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishSystemAnnouncement')));
        $trail->add_help('administration system announcements');
        
        $publisher = $this->get_publisher_html();
        
        $this->display_header($trail);
        echo $publisher;
        echo '<div style="clear: both;"></div>';
        $this->display_footer();
    }

    private function get_publisher_html()
    {
        $object = Request :: get('object');
        $pub = new RepoViewer($this, 'system_announcement', true);
        
        if (! isset($object))
        {
            $html[] = '<p><a href="' . $this->get_url() . '"><img src="' . Theme :: get_common_image_path() . 'action_browser.png" alt="' . Translation :: get('BrowserTitle') . '" style="vertical-align:middle;"/> ' . Translation :: get('BrowserTitle') . '</a></p>';
            $html[] = $pub->as_html();
        }
        else
        {
            //$html[] = 'ContentObject: ';
            $publisher = new SystemAnnouncerMultipublisher($pub);
            $html[] = $publisher->get_publications_form($object);
        }
        
        return implode($html, "\n");
    }
}
?>