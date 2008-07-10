<?php

class Block {
	
	const PARAM_ACTION = 'block_action';
	const BLOCK_LIST_SIMPLE = 'simple';
	const BLOCK_LIST_ADVANCED = 'advanced';
	
	private $parent;
	private $type;
	private $block_info;
	private $configuration;

    function Block($parent, $block_info)
	{
		$this->parent = $parent;
		$this->block_info = $block_info;
		$this->configuration = $block_info->get_configuration();
	}
	
	/**
	 * Returns the tool which created this publisher.
	 * @return RepositoryTool The tool.
	 */
	function get_parent()
	{
		return $this->parent;
	}
	
	function get_configuration()
	{
		return $this->configuration;
	}

	/**
	 * @see RepositoryTool::get_user_id()
	 */
	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}
	
	function get_user()
	{
		return $this->get_parent()->get_user();
	}

	/**
	 * Returns the types of learning object that this object may publish.
	 * @return array The types.
	 */
	function get_type()
	{
		return $this->type;
	}
	
	function get_block_info()
	{
		return $this->block_info;
	}
	
    function as_html()
    {
    	$html = array();
    	
    	$html[] = $this->display_header();
    	$html[] = $this->display_footer();
    	
    	return implode ("\n", $html);
	}
	
	function display_header()
	{
		$html = array();
		
		$html[] = '<div class="block" id="block_'. $this->get_block_info()->get_id() .'" style="background-image: url('.Theme :: get_img_path().'block_'.$this->get_block_info()->get_application().'.png);">';
		$html[] = $this->display_title();
		$html[] = '<div class="description"'. ($this->get_block_info()->is_visible() ? '' : ' style="display: none"') .'>';
		
		return implode ("\n", $html);
	}
	
	function display_title()
	{
		$html = array();
		
		$html[] = '<div class="title">'. $this->get_block_info()->get_title();
		$html[] = '<a href="'. $this->get_block_visibility_link($this->get_block_info()) .'" class="closeEl"><img class="visible"'. ($this->get_block_info()->is_visible() ? '' : ' style="display: none;"') .' src="'.Theme :: get_common_img_path().'action_visible.png" /><img class="invisible"'. ($this->get_block_info()->is_visible() ? ' style="display: none;"' : '') .' src="'.Theme :: get_common_img_path().'action_invisible.png" /></a>';
		$html[] = '<a href="'. $this->get_block_editing_link($this->get_block_info()) .'" class="editEl"><img src="'.Theme :: get_common_img_path().'action_edit.png" /></a>';
		$html[] = '<a href="'. $this->get_block_deleting_link($this->get_block_info()) .'" class="deleteEl"><img src="'.Theme :: get_common_img_path().'action_delete.png" /></a>';
		$html[] = '</div>';
		
		return implode ("\n", $html);
	}
	
	function get_block_visibility_link($home_block)
	{
		return $this->get_link(array (HomeManager :: PARAM_ACTION => HomeManager :: ACTION_EDIT_HOME, HomeManager :: PARAM_HOME_TYPE => HomeManager :: TYPE_BLOCK, HomeManager :: PARAM_HOME_ID => $home_block->get_id()));
	}
	
	function get_block_deleting_link($home_block)
	{
		return '#';
	}
	
	function get_block_editing_link($home_block)
	{
		return $this->get_link(array (HomeManager :: PARAM_ACTION => HomeManager :: ACTION_EDIT_HOME, HomeManager :: PARAM_HOME_TYPE => HomeManager :: TYPE_BLOCK, HomeManager :: PARAM_HOME_ID => $home_block->get_id()));
	}
	
	public function get_link($parameters = array (), $encode = false)
	{
		$link = 'index_'. HomeManager :: APPLICATION_NAME .'.php';
		if (count($parameters))
		{
			$link .= '?'.http_build_query($parameters);	
		}
		if ($encode)
		{
			$link = htmlentities($link);
		}
		return $link;
	}
	
	function display_footer()
	{
		$html = array();
		
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		$html[] = '</div>';
		
		return implode ("\n", $html);
	}
	
	function get_platform_blocks($type = self :: BLOCK_LIST_SIMPLE)
	{
		$result = array();
		$applications_options = array();
		$components_options = array();
		
		$applications = Application :: load_all(false);
		
		$path = Path :: get_application_path() .'/lib/';
		
		foreach ($applications as $application)
		{
			$application_path = $path . $application . '/block';
			if ($handle = opendir($application_path))
			{
				while (false !== ($file = readdir($handle)))
				{
					if (!is_dir($file) && stripos($file, '.class.php') !== false)
					{
						
						$component = str_replace('.class.php', '', $file);
						$component = str_replace($application . '_', '', $component);
						
						$applications_options[$application] = Translation :: get(Application :: application_to_class($application));
						$components_options[$application][$component] = DokeosUtilities :: underscores_to_camelcase($component);
					}
				}
				closedir($handle);
			}
		}
		
		$core_applications = array('admin', 'tracking', 'repository', 'users', 'class_group', 'rights', 'home', 'menu');
		
		$path = Path :: get(SYS_PATH);
		
		foreach ($core_applications as $core_application)
		{
			$application_path = $path . $core_application . '/block';
			if ($handle = opendir($application_path))
			{
				while (false !== ($file = readdir($handle)))
				{
					if (!is_dir($file) && stripos($file, '.class.php') !== false)
					{
						
						$component = str_replace('.class.php', '', $file);
						$component = str_replace($core_application . '_', '', $component);
						
						$applications_options[$core_application] = Translation :: get(Application :: application_to_class($core_application));
						$components_options[$core_application][$component] = DokeosUtilities :: underscores_to_camelcase($component);
					}
				}
				closedir($handle);
			}
		}
		
		asort($applications_options);
		
		$result['applications'] = $applications_options;
		$result['components'] = $components_options;
		
		return $result;
	}
	
	/*
	 * We keep this since Quickform's hierselect element
	 * only works if javascript is enabled
	 */
	function get_platform_blocks_deprecated()
	{
		$application_components = array();
		$applications = Application :: load_all(false);
		
		$path = Path :: get_application_path() .'/lib/';
		
		foreach ($applications as $application)
		{
			$application_path = $path . $application . '/block';
			if ($handle = opendir($application_path))
			{
				while (false !== ($file = readdir($handle)))
				{
					if (!is_dir($file) && stripos($file, '.class.php') !== false)
					{
						$component = str_replace('.class.php', '', $file);
						$component = str_replace($application . '_', '', $component);
						$value = $application . '.' . $component;
						$display = Translation :: get(Application :: application_to_class($application)) . '&nbsp;>&nbsp;' . DokeosUtilities :: underscores_to_camelcase($component);
						$application_components[$value] = $display;
					}
				}
				closedir($handle);
			}
		}
		
		$core_applications = array('admin', 'tracking', 'repository', 'users', 'class_group', 'rights', 'home', 'menu');
		
		$path = Path :: get(SYS_PATH);
		
		foreach ($core_applications as $core_application)
		{
			$application_path = $path . $core_application . '/block';
			if ($handle = opendir($application_path))
			{
				while (false !== ($file = readdir($handle)))
				{
					if (!is_dir($file) && stripos($file, '.class.php') !== false)
					{
						$component = str_replace('.class.php', '', $file);
						$component = str_replace($core_application . '_', '', $component);
						$value = $core_application . '.' . $component;
						$display = Translation :: get(Application :: application_to_class($core_application)) . '&nbsp;>&nbsp;' . DokeosUtilities :: underscores_to_camelcase($component);
						$application_components[$value] = $display;
					}
				}
				closedir($handle);
			}
		}
		
		asort($application_components);
		
		return $application_components;
	}
}
?>