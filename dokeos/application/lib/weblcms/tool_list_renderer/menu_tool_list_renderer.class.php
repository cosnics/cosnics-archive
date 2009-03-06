<?php
/**
 * $Id$
 * @package application.weblcms
 */
require_once(dirname(__FILE__).'/../tool_list_renderer.class.php');
require_once('HTML/Table.php');
/**
 * Tool list renderer to display a navigation menu.
 */
class MenuToolListRenderer extends ToolListRenderer
{
	/**
	 *
	 */
	private $is_course_admin;
	
	private $menu_properties;
	/**
	 * Constructor
	 * @param  WebLcms $parent The parent application
	 */
	function MenuToolListRenderer($parent)
	{
		parent::ToolListRenderer($parent);
		$this->is_course_admin = $this->get_parent()->get_course()->is_course_admin($this->get_parent()->get_user());
		$this->menu_properties = $this->retrieve_menu_properties();
	}
	/**
	 * Sets the type of this navigation menu renderer
	 * @param int $type
	 */
	function set_type($type)
	{
		$this->type = $type;
	}
	// Inherited
	function display()
	{
		$parent = $this->get_parent();
		$tools = $parent->get_registered_tools();
		$this->show_tools($tools);
	}
	/**
	 * Show the tools of a given section
	 * @param array $tools
	 */
	private function show_tools($tools)
	{
		$parent = $this->get_parent();
		$course = $parent->get_course();
		
		$menu_style = $this->get_menu_style();
		
		$html[] = '<div id="tool_bar" class="tool_bar tool_bar_'. ($this->display_menu_icons() && !$this->display_menu_text() ? 'icon_' : '') . $menu_style .'">';
		
		if ($this->get_menu_style() == 'right')
		{
			$html[] = '<div id="tool_bar_hide_container" class="hide">';
			$html[] = '<a id="tool_bar_hide" href="#"><img src="'. Theme :: get_common_image_path() .'action_action_bar_'. $menu_style .'_hide.png" /></a>';
			$html[] = '<a id="tool_bar_show" href="#"><img src="'. Theme :: get_common_image_path() .'action_action_bar_'. $menu_style .'_show.png" /></a>';
			$html[] = '</div>';
		}
		
		$html[] = '<div class="tool_menu">';
		$html[] = '<ul>';
		
		foreach ($tools as $index => $tool)
		{
			$sections = WeblcmsDataManager :: get_instance()->retrieve_course_sections(new EqualityCondition('id', $tool->section));
			$section = $sections->next_result();
			
			//dump($tool->name);
			if(!PlatformSetting :: get($tool->name . '_active', 'weblcms') && $section->get_type() != CourseSection :: TYPE_ADMIN)
				continue;
				
			$section = WeblcmsDataManager :: get_instance()->retrieve_course_sections(new EqualityCondition('id',$tool->section))->next_result();

			if($section->get_type() == CourseSection :: TYPE_ADMIN)
			{
				$admin_tools[] = $tool;
				continue;
			}
			
			if($tool->visible || $this->is_course_admin)
			{
				$html[] = $this->display_tool($tool);
			}
		}
		
		if(count($admin_tools) && $this->is_course_admin)
		{ 
			$html[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px;"></div>';
			foreach($admin_tools as $tool)
			{
				$html[] = $this->display_tool($tool);
			}
		}
		
		$html[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px;"></div>';
		$html[] = '</ul>';
		
		$form = new FormValidator('search_simple', 'post', $parent->get_url(array('tool' => 'search')), '', null, false);
		$form->addElement('text', 'query', '', 'size="18" class="search_query_no_icon" style="margin-left: -30px; background-color: white; border: 1px solid grey; height: 18px; "');
		$form->addElement('style_submit_button', 'submit', Translation :: get('Search'), array('class' => 'normal search'));
		$html[] = $form->toHtml();

		
		$html[] = '</div>';
		$html[] = '<div class="clear"></div>';
	
		if ($this->get_menu_style() == 'left')
		{
			$html[] = '<div id="tool_bar_hide_container" class="hide">';
			$html[] = '<a id="tool_bar_hide" href="#"><img src="'. Theme :: get_common_image_path() .'action_action_bar_'. $menu_style .'_hide.png" /></a>';
			$html[] = '<a id="tool_bar_show" href="#"><img src="'. Theme :: get_common_image_path() .'action_action_bar_'. $menu_style .'_show.png" /></a>';
			$html[] = '</div>';
		}
		
		$html[] = '</div>';
		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/tool_bar.js' .'"></script>';
		if($_SESSION['toolbar_state'] == 'hide')
				$html[] = '<script type="text/javascript">var hide = "true";</script>';
		$html[] = '<div class="clear"></div>';
		
		echo implode("\n",$html);
	}
	
	function display_tool($tool)
	{
		$parent = $this->get_parent();
		$course = $parent->get_course();
		
		$new = '';
		if($parent->tool_has_new_publications($tool->name))
		{
			$new = '_new';
		}
		$tool_image = 'tool_mini_' . $tool->name . $new . '.png';
		$title = htmlspecialchars(Translation :: get(Tool :: type_to_class($tool->name).'Title'));
		$html[] = '<li class="tool_list_menu">';
		$html[] = '<a href="'.$parent->get_url(array (WebLcms :: PARAM_ACTION => Weblcms :: ACTION_VIEW_COURSE,WebLcms :: PARAM_TOOL => $tool->name), true).'" title="'.$title.'">';
						
		if ($this->display_menu_icons())
		{
			$html[] = '<img src="'.Theme :: get_image_path().$tool_image.'" style="vertical-align: middle;" alt="'.$title.'"/> ';
		}
		
		if ($this->display_menu_text())
		{
			$html[] = $title;
		}
		
		$html[] = '</a>';
		$html[] = '</li>';
		
		return implode("\n", $html);
	}
	
	function retrieve_menu_properties()
	{
		$menu_style = $this->get_parent()->get_course()->get_menu();
		
		$properties = array();
		
		switch($menu_style)
		{
			case Course :: MENU_LEFT_ICON :
				$properties['style'] = 'left';
				$properties['icons'] = true;
				$properties['text'] = false;
				break;
			case Course :: MENU_LEFT_ICON_TEXT :
				$properties['style'] = 'left';
				$properties['icons'] = true;
				$properties['text'] = true;
				break;
			case Course :: MENU_LEFT_TEXT :
				$properties['style'] = 'left';
				$properties['icons'] = false;
				$properties['text'] = true;
				break;
				
			case Course :: MENU_RIGHT_ICON :
				$properties['style'] = 'right';
				$properties['icons'] = true;
				$properties['text'] = false;
				break;
			case Course :: MENU_RIGHT_ICON_TEXT :
				$properties['style'] = 'right';
				$properties['icons'] = true;
				$properties['text'] = true;
				break;
			case Course :: MENU_RIGHT_TEXT :
				$properties['style'] = 'right';
				$properties['icons'] = false;
				$properties['text'] = true;
				break;
				
			default :
				$properties['style'] = 'left';
				$properties['icons'] = true;
				$properties['text'] = true;
				break;
		}
		
		return $properties;
	}
	
	function get_menu_properties()
	{
		return $this->menu_properties;
	}	
	
	function get_menu_style()
	{
		$properties = $this->get_menu_properties();
		return $properties['style'];
	}
	
	function display_menu_icons()
	{
		$properties = $this->get_menu_properties();
		return $properties['icons'];
	}
	
	function display_menu_text()
	{
		$properties = $this->get_menu_properties();
		return $properties['text'];
	}
}
?>