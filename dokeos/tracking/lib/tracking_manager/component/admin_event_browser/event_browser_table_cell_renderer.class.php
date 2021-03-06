<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__) . '/event_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../event_table/default_event_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../event.class.php';
require_once dirname(__FILE__) . '/../../tracking_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class EventBrowserTableCellRenderer extends DefaultEventTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function EventBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    /**
     * Renders the cell
     *
     * @param ObjectTableColumn $column
     * @param Event $event
     * @return unknown
     */
    function render_cell($column, $event)
    {
        if ($column === EventBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($event);
        }
        
        $property = $column->get_property();
        
        if ($property == Event :: PROPERTY_NAME && $event->get_active() == 1)
        {
            return '<a href="' . $this->browser->get_event_viewer_url($event) . '">' . $event->get_default_property($property) . '</a>';
        }
        
        return parent :: render_cell($column, $event);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($event)
    {
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->browser->get_change_active_url('event', $event->get_id()), 'label' => ($event->get_active() == 1) ? Translation :: get('Deactivate') : Translation :: get('Activate'), 'confirm' => false, 'img' => ($event->get_active() == 1) ? Theme :: get_common_image_path() . 'action_visible.png' : Theme :: get_common_image_path() . 'action_invisible.png');
        
        $toolbar_data[] = array('href' => $this->browser->get_empty_tracker_url('event', $event->get_id()), 'label' => Translation :: get('Empty_event'), 'confirm' => true, 'img' => Theme :: get_common_image_path() . 'action_recycle_bin.png');
        
        return DokeosUtilities :: build_toolbar($toolbar_data);
    }
}
?>