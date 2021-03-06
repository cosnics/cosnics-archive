<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/registration_browser_table_column_model.class.php';
require_once Path :: get_admin_path() . 'lib/tables/registration_table/default_registration_table_cell_renderer.class.php';
require_once Path :: get_admin_path() . 'lib/registration.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class RegistrationBrowserTableCellRenderer extends DefaultRegistrationTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function RegistrationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $registration)
    {
        if ($column === RegistrationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($registration);
        }

        return parent :: render_cell($column, $registration);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($registration)
    {
        $toolbar_data = array();

        if ($registration->is_active())
        {
            $toolbar_data[] = array('href' => $this->browser->get_registration_deactivation_url($registration), 'label' => Translation :: get('Deactivate'), 'img' => Theme :: get_image_path() . 'action_deactivate.png');
        }
        else
        {
            $toolbar_data[] = array('href' => $this->browser->get_registration_activation_url($registration), 'label' => Translation :: get('Activate'), 'img' => Theme :: get_image_path() . 'action_activate.png');
        }

        $toolbar_data[] = array('href' => $this->browser->get_registration_removal_url($registration), 'label' => Translation :: get('Deinstall'), 'img' => Theme :: get_common_image_path() . 'action_recycle_bin.png', 'confirm' => true);

        return DokeosUtilities :: build_toolbar($toolbar_data);
    }
}
?>