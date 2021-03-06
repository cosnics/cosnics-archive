<?php
/**
 * $Id$
 * Repository tool
 * @package application.weblcms.tool
 */

/**
==============================================================================
 *	This is the base class component for all tool components used in applications.
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

abstract class ToolComponent
{
	private static $component_count = 0;
	private $id;
	/** The parent tool **/
	private $tool;

	/**
	 * Constructor
	 * @param Tool $tool the parent tool
	 */
    function ToolComponent($tool)
    {
		$this->tool = $tool;
		$this->id =  ++self :: $component_count;
    }

    /**
     * Returns the tool to which this component belongs to
     * @return Tool
     */
    function get_tool()
	{
		return $this->tool;
	}

	function get_parent()
	{
		return $this->tool->get_parent();
	}

	/** Delegation functions **/

	function display_header($breadcrumbtrail, $display_title)
	{
		$this->tool->display_header($breadcrumbtrail, $display_title);
	}

	function display_footer()
	{
		$this->tool->display_footer();
	}

	function display_error_message($message)
	{
		$this->tool->display_error_message($message);
	}

	function get_action()
	{
		return $this->tool->get_action();
	}

	function disallow()
	{
		$this->tool->disallow();
	}

	function get_tool_id()
	{
		return $this->tool->get_tool_id();
	}

	/**
	 * @see WebApplication :: get_user()
	 */
	function get_user()
	{
		return $this->tool->get_user();
	}

	/**
	 * @see WebApplication :: get_user_id()
	 */
	function get_user_id()
	{
		return $this->tool->get_user_id();
	}

	function get_user_info($user_id)
	{
		return $this->tool->get_user_info($user_id);
	}

	/**
	 * @see WebApplication :: get_course_id()
	 */
	function get_course()
	{
		return $this->tool->get_course();
	}

	/**
	 * @see WebApplication :: get_course_id()
	 */
	function get_course_id()
	{
		return $this->tool->get_course_id();
	}

	/**
	 * @see WebApplication :: get_course_groups()
	 */
	function get_course_groups()
	{
		return $this->tool->get_course_groups();
	}

	function get_course_group()
	{
		return $this->tool->get_course_group();
	}

	/**
	 * @see WebApplication :: get_parameters()
	 */
	function get_parameters()
	{
		return $this->tool->get_parameters();
	}

	/**
	 * @see WebApplication :: get_parameter()
	 */
	function get_parameter($name)
	{
		return $this->tool->get_parameter($name);
	}

	/**
	 * @see WebApplication :: set_parameter()
	 */
	function set_parameter($name, $value)
	{
		$this->tool->set_parameter($name, $value);
	}

	/**
	 * @see WebApplication :: get_url()
	 */

	function get_url($parameters = array (), $filter = array(), $encode_entities = false)
	{
		return $this->tool->get_url($parameters, $filter, $encode_entities);
	}

	/**
	 * @see WebApplication :: redirect()
	 */
	function redirect($message = '', $error_message = false, $parameters = array (), $filter = array(), $encode_entities = false, $type = Redirect :: TYPE_URL)
	{
		return $this->tool->redirect($message, $error_message, $parameters, $filter, $encode_entities, $type);
	}

	/**
	 * Check if the current user has a given right in this tool
	 * @param int $right
	 * @return boolean True if the current user has the right
	 */
	function is_allowed($right)
	{
		return $this->tool->is_allowed($right);
	}

	/**
	 * @see WeblcmsManager :: get_last_visit_date()
	 */
	function get_last_visit_date()
	{
		return $this->tool->get_last_visit_date();
	}

	function get_path($path_type)
	{
		return $this->tool->get_path($path_type);
	}

	function perform_requested_actions()
	{
		return $this->tool->perform_requested_actions();
	}

	function get_categories($list = false)
	{
		return $this->tool->get_categories($list);
	}

	function get_category($id)
	{
		return $this->tool->get_category($id);
	}

	static function factory($tool_name, $component_name, $tool)
	{
		$filename = dirname(__FILE__).'/'.
			DokeosUtilities :: camelcase_to_underscores($tool_name).'/component/' .
			DokeosUtilities :: camelcase_to_underscores($tool_name). ($tool_name?'_':'') .
			DokeosUtilities :: camelcase_to_underscores($component_name). '.class.php';
		if (!file_exists($filename) || !is_file($filename))
		{
			die('Failed to load "'.$component_name.'" component');
		}
		$class = $tool_name . 'Tool'.$component_name.'Component';
		require_once $filename;
		return new $class($tool);
	}

	function display_introduction_text($introduction_text)
	{
		return $this->tool->display_introduction_text($introduction_text);
	}

    function get_access_details_toolbar_item($parent)
    {
        return $this->tool->get_access_details_toolbar_item($parent);
    }
    
 	function get_allowed_types()
 	{
 		return $this->tool->get_allowed_types();
 	}
 	
 	function get_complex_builder_url($pid)
 	{
 		return $this->tool->get_complex_builder_url($pid);
 	}

}