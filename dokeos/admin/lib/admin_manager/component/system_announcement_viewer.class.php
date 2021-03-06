<?php
/**
 * @package application.lib.profiler.profiler_manager
 */
require_once dirname(__FILE__) . '/../admin_manager.class.php';
require_once dirname(__FILE__) . '/../admin_manager_component.class.php';

class AdminManagerSystemAnnouncementViewerComponent extends AdminManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewSystemAnnouncement')));
        $trail->add_help('administration system announcements');
        
        $user = $this->get_user();
        
        if (! $user->is_platform_admin())
        {
            Display :: not_allowed();
            exit();
        }
        
        $id = Request :: get(AdminManager :: PARAM_SYSTEM_ANNOUNCEMENT_ID);
        
        if ($id)
        {
            $system_announcement_publication = $this->retrieve_system_announcement_publication($id);
            $object = $system_announcement_publication->get_publication_object();
            
            $display = ContentObjectDisplay :: factory($object);
            
            $this->display_header($trail);
            echo $display->get_full_html();
            echo $this->get_toolbar($system_announcement_publication, $object);
            $this->display_footer();
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoSystemAnnouncementSelected')));
        }
    }

    function get_toolbar($system_announcement_publication, $object)
    {
        $user = $this->get_user();
        $toolbar_data = array();
        
        if ($user->is_platform_admin())
        {
            $edit_url = $this->get_system_announcement_publication_editing_url($system_announcement_publication);
            $toolbar_data[] = array('href' => $edit_url, 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png', 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
            
            $delete_url = $this->get_system_announcement_publication_deleting_url($system_announcement_publication);
            $toolbar_data[] = array('href' => $delete_url, 'label' => Translation :: get('Delete'), 'confirm' => true, 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL);
        }
        
        return DokeosUtilities :: build_toolbar($toolbar_data);
    }
}
?>