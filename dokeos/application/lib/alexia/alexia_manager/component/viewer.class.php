<?php
/**
 * @package alexia
 * @subpackage alexia_manager
 * @subpackage component
 *
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__) . '/../alexia_manager.class.php';
require_once dirname(__FILE__) . '/../alexia_manager_component.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class AlexiaManagerViewerComponent extends AlexiaManagerComponent
{
    private $folder;
    private $publication;
    private $actionbar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(AlexiaManager :: PARAM_ALEXIA_ID);

        if (isset($id))
        {
            $this->publication = $this->retrieve_alexia_publication($id);
            $publication = $this->publication;

            $trail = new BreadcrumbTrail();
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_BROWSE_PUBLICATIONS)), Translation :: get('Library')));
            $trail->add(new Breadcrumb($this->get_url(), $publication->get_publication_object()->get_title()));
            $trail->add_help('alexia general');

            $this->action_bar = $this->get_action_bar($publication);

            $this->display_header($trail);
            echo $this->action_bar->as_html();
            echo '<div class="clear"></div><br />';
            echo $this->get_publication_as_html();

            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPublicationSelected')));
        }
    }

    function get_action_bar($publication)
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_publication_editing_url($publication), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $this->get_publication_deleting_url($publication), ToolbarItem :: DISPLAY_ICON_AND_LABEL, true));

        return $action_bar;
    }

    function get_publication_as_html()
    {
        $publication = $this->publication;
        $link = $publication->get_publication_object();
        $html = array();
        
        $display = ContentObjectDisplay :: factory($link);
        $html[] = $display->get_full_html();
        
        return implode("\n",$html);
    }
}
?>