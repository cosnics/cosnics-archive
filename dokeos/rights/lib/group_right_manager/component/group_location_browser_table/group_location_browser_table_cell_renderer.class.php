<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/group_location_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/location_table/default_location_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../location.class.php';
require_once dirname(__FILE__).'/../../group_right_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class GroupLocationBrowserTableCellRenderer extends DefaultLocationTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function GroupLocationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $location)
	{
		if ($column === GroupLocationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($location);
		}

		if (GroupLocationBrowserTableColumnModel :: is_rights_column($column))
		{
		    return $this->get_rights_column_value($column, $location);
		}

	    switch ($column->get_name())
        {
            case Location :: PROPERTY_LOCATION :
                if ($location->has_children())
                {
                    return '<a href="' . htmlentities($this->browser->get_url(array(GroupRightManager :: PARAM_GROUP => $this->browser->get_current_group()->get_id(), GroupRightManager :: PARAM_SOURCE => $location->get_application(), GroupRightManager :: PARAM_LOCATION => $location->get_id()))) . '">' . parent :: render_cell($column, $location) . '</a>';
                }
                else
                {
                    return parent :: render_cell($column, $location);
                }
                break;
            case Location :: PROPERTY_LOCKED :
        		if ($location->is_locked())
				{
					return '<img src="' . htmlentities(Theme :: get_common_image_path() . 'action_lock.png') . '" alt="' . Translation :: get('Locked') . '" title="' . Translation :: get('Locked') . '" />';
				}
				else
				{
					return '<img src="' . htmlentities(Theme :: get_common_image_path() . 'action_unlock.png') . '" alt="' . Translation :: get('Unlocked') . '" title="' . Translation :: get('Unlocked') . '" />';
				}
            	break;
            case Location :: PROPERTY_INHERIT :
                if ($location->inherits())
				{
					return '<img src="' . htmlentities(Theme :: get_common_image_path() . 'action_setting_true_inherit.png') . '" alt="' . Translation :: get('Inherits') . '" title="' . Translation :: get('Inherits') . '" />';
				}
				else
				{
					return '<img src="' . htmlentities(Theme :: get_common_image_path() . 'action_setting_false_inherit.png') . '" alt="' . Translation :: get('DoesNotInherit') . '" title="' . Translation :: get('DoesNotInherit') . '" />';
				}
            	break;
        }

		return parent :: render_cell($column, $location);
	}
	/**
	 * Gets the action links to display
	 * @param ContentObject $content_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($location)
	{
		$toolbar_data = array();

//		$reset_url = $this->browser->get_group_right_reset_url($location);
		$toolbar_data[] = array(
//			'href' => $reset_url,
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_reset.png',
		);

		return DokeosUtilities :: build_toolbar($toolbar_data);
	}

	private function get_rights_column_value($column, $location)
	{
	    $browser = $this->browser;
	    $locked_parent = $location->get_locked_parent();
	    $rights = RightsUtilities :: get_available_rights($this->browser->get_source());
	    $group_id = $browser->get_current_group()->get_id();

	    $location_url = $browser->get_url(array('application' => $this->application, 'location' => ($locked_parent ? $locked_parent->get_id() : $location->get_id())));

	    foreach($rights as $right_name => $right_id)
	    {
            $column_name = Translation :: get(DokeosUtilities :: underscores_to_camelcase(strtolower($right_name)));
            if ($column->get_name() == $column_name)
            {
                $rights_url = $browser->get_url(array(GroupRightManager :: PARAM_GROUP_RIGHT_ACTION => GroupRightManager:: ACTION_SET_GROUP_RIGHTS, 'group_id' => $group_id, 'right_id' => $right_id, GroupRightManager :: PARAM_LOCATION => $location->get_id()));
                return RightsUtilities :: get_rights_icon($location_url, $rights_url, $locked_parent, $right_id, $browser->get_current_group(), $location);
            }
	    }
	    return '&nbsp;';
	}
}
?>