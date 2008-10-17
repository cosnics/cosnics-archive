<?php
require_once dirname(__FILE__) . '/action_bar_search_form.class.php';
require_once Path :: get_library_path().'html/toolbar/toolbar.class.php';
require_once Path :: get_library_path().'html/toolbar/toolbar_item.class.php';
/**
 * Class that renders an action bar divided in 3 parts, a left menu for actions, a middle menu for actions
 * and a right menu for a search bar.
 */
class ActionBarRenderer
{
	const ACTION_BAR_COMMON = 'common';
	const ACTION_BAR_TOOL = 'tool';
	const ACTION_BAR_SEARCH = 'search';
	
	const TYPE_HORIZONTAL = 'hoirzontal';
	const TYPE_VERTICAL = 'vertical';
	
	private $actions = array();
	private $search_form;
	private $type;
	
	function ActionBarRenderer($type)
	{
		$this->type = $type;
	}
	
	function set_type($type)
	{
		$this->type = $type;
	}
	
	function get_type()
	{
		return $this->type;
	}
	
	function add_action($type = self :: ACTION_BAR_COMMON, $action)
	{
		$this->actions[$type][] = $action;
	}
	
	function add_common_action($action)
	{
		$this->actions[self :: ACTION_BAR_COMMON][] = $action;
	}
	
	function add_tool_action($action)
	{
		$this->actions[self :: ACTION_BAR_TOOL][] = $action;
	}
	
	function get_tool_actions()
	{
		return $this->actions[self :: ACTION_BAR_TOOL];
	}
	
	function get_common_actions()
	{
		return $this->actions[self :: ACTION_BAR_COMMON];
	}
	
	function get_search_url()
	{
		return $this->actions[self :: ACTION_BAR_SEARCH];
	}
	
	function set_tool_actions($actions)
	{
		$this->actions[self :: ACTION_BAR_TOOL] = $actions;
	}
	
	function set_common_actions($actions)
	{
		$this->actions[self :: ACTION_BAR_COMMON] = $actions;
	}
	
	function set_search_url($search_url)
	{
		$this->actions[self :: ACTION_BAR_SEARCH] = $search_url;
		$this->search_form = new ActionBarSearchForm($search_url);
	}
	
	function as_html()
	{
		$type = $this->type;
		
		switch($type)
		{
			case self :: TYPE_HORIZONTAL :
				return $this->render_horizontal();
				break;
			case self :: TYPE_VERTICAL :
				return $this->render_vertical();
				break;
			default :
				return $this->render_horizontal();
				break;
		}
	}
	
	function render_horizontal()
	{
		$html = array();
		
		$html[] = '<div id="action_bar_text" style="float:left; display: none; margin-bottom: 10px;"><a href="#"><img src="'. Theme :: get_common_img_path() .'action_bar.png" style="vertical-align: middle;" />&nbsp;'. Translation :: get('ShowActionBar') .'</a></div>';
		$html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
		$html[] = '<div id="action_bar" class="action_bar">';
		
		$common_actions = $this->get_common_actions();
		$tool_actions = $this->get_tool_actions();
		
		if (count($common_actions) > 0)
		{
			$html[] = '<div class="common_menu">';
			$toolbar = new Toolbar();
			$toolbar->set_items($common_actions);
			$toolbar->set_type(Toolbar :: TYPE_HORIZONTAL);
			$html[] = $toolbar->as_html();
			$html[] = '</div>';
		}
		
		if (count($tool_actions) > 0)
		{
			$html[] = '<div class="tool_menu">';
			$toolbar = new Toolbar();
			$toolbar->set_items($tool_actions);
			$toolbar->set_type(Toolbar :: TYPE_HORIZONTAL);
			$html[] = $toolbar->as_html();
			$html[] = '</div>';
		}
		
		if (!is_null($this->search_form))
		{
			$search_form = $this->search_form;
			
			$html[] = '<div class="search_menu">';
			$html[] = $search_form->as_html();
			$html[] = '</div>';
		}
		
		$html[] = '<div class="clear"></div>';
		$html[] = '<div id="action_bar_hide_container">';
		$html[] = '<a id="action_bar_hide" href="#"><img src="'. Theme :: get_common_img_path() .'action_ajax_hide.png" /></a>';
		$html[] = '</div>';
		$html[] = '</div>';
		
		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/action_bar_horizontal.js' .'"></script>';
		
		return implode("\n", $html);
	}
	
	function render_vertical()
	{
		$html = array();
		
		$html[] = '<div id="action_bar_left" class="action_bar_left">';
//		$html[] = '<div id="action_bar_left_options"';

		$html[] = '<h3>' . Translation :: get('ActionBar') . '</h3>';
		
		$common_actions = $this->get_common_actions();
		$tool_actions = $this->get_tool_actions();
		
		$action_bar_has_search_form = !is_null($this->search_form);
		$action_bar_has_common_actions = (count($common_actions) > 0);
		$action_bar_has_tool_actions = (count($tool_actions) > 0);
		$action_bar_has_common_and_tool_actions = (count($common_actions) > 0) && (count($tool_actions) > 0);
		
		if (!is_null($this->search_form))
		{
			$search_form = $this->search_form;
			$html[] = $search_form->as_html();
		}
		
		if ($action_bar_has_search_form && ($action_bar_has_common_actions || $action_bar_has_tool_actions))
		{
			$html[] = '<div class="divider"></div>';
		}
		
		if ($action_bar_has_common_actions)
		{
			$html[] = '<div class="clear"></div>';
			
			$toolbar = new Toolbar();
			$toolbar->set_items($common_actions);
			$toolbar->set_type(Toolbar :: TYPE_VERTICAL);
			$html[] = $toolbar->as_html();
		}
		
		if ($action_bar_has_common_and_tool_actions)
		{
			$html[] = '<div class="divider"></div>';
		}
		
		if ($action_bar_has_tool_actions)
		{
			$html[] = '<div class="clear"></div>';
			
			$toolbar = new Toolbar();
			$toolbar->set_items($tool_actions);
			$toolbar->set_type(Toolbar :: TYPE_VERTICAL);
			$html[] = $toolbar->as_html();
		}
		
		$html[] = '<div class="clear"></div>';
//		$html[] = '</div>';
		
		$html[] = '<div id="action_bar_left_hide_container" class="hide">';
		$html[] = '<a id="action_bar_left_hide" href="#"><img src="'. Theme :: get_common_img_path() .'action_action_bar_hide.png" /></a>';
		$html[] = '<a id="action_bar_left_show" href="#"><img src="'. Theme :: get_common_img_path() .'action_action_bar_show.png" /></a>';
		$html[] = '</div>';
		$html[] = '</div>';
		
		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/action_bar_vertical.js' .'"></script>';
		
		return implode("\n", $html);
	}
	
	function get_query()
	{
		if($this->search_form)
		{
			return $this->search_form->get_query();
		}
		else
		{
			return null;
		}
	}
}

?>