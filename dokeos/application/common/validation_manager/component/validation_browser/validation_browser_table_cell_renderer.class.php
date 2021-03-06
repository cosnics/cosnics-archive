<?php

require_once dirname(__FILE__) . '/validation_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../validation_table/default_validation_table_cell_renderer.class.php';
//require_once dirname(__FILE__) . '/../../../../lib/profiler/profiler_manager/profiler_manager.class.php';
require_once Path::get_admin_path().'lib/validation.class.php';
/**
 * Cell renderer for the learning object browser table
 */
class ValidationBrowserTableCellRend extends DefaultValidationTableCellRend
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param ValidationManagerBrowserComponent $browser
     */
    function ValidationBrowserTableCellRend($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $validation)
    {
        $user = $validation->get_validation_publisher();

        if ($column === ValidationBrowserTableColumnMod :: get_modification_column())
        {
            return $this->get_modification_links($validation);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
           /* case ProfilePublication :: PROPERTY_PUBLISHED :
                return Text :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $profile->get_published());
                break;*/
            case User :: PROPERTY_USERNAME :
                return '<a href ="'.$this->browser->get_url(array('user_id' => $user->get_id())).'">'.$user->get_username().'</a>';
            case Validation :: PROPERTY_VALIDATED :
              /*  $title = parent :: render_cell($column, $validation);
                $title_short = $title;
                //				if(strlen($title_short) > 53)
                //				{
                //					$title_short = mb_substr($title_short,0,50).'&hellip;';
                //				}
                $title_short = DokeosUtilities :: truncate_string($title_short, 53, false);
              */
                $date_format = '%B %d, %Y at %I:%M %p';//Translation :: get('dateTimeFormatLong');
                $val = Text :: format_locale_date($date_format,$validation->get_validated());
                return $val;
                break;
        }
        return parent :: render_cell($column, $validation);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $profile The profile object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($validation)
    {
        $toolbar_data = array();
        if ($this->browser->get_user()->is_platform_admin() || $validation->get_validation_publisher()->get_id() == $this->browser->get_user()->get_id())
        {
           // $edit_url = "#";//$this->browser->get_publication_editing_url($profile);
           // $toolbar_data[] = array('href' => $edit_url, 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
            
            $delete_url = $this->browser->get_publication_deleting_url($validation);
            $toolbar_data[] = array('href' => $delete_url, 'label' => Translation :: get('Delete'), 'confirm' => true, 'img' => Theme :: get_common_image_path() . 'action_delete.png');
        }
        
        return DokeosUtilities :: build_toolbar($toolbar_data);
    }
}
?>