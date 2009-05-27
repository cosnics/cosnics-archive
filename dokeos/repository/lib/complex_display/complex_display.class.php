<?php
/**
 * @author Michael Kyndt
 */

require_once dirname(__FILE__) . '/complex_display_component.class.php';
require_once dirname(__FILE__) . '/../repository_manager/component/complex_browser/complex_browser_table.class.php';
require_once Path :: get_library_path() . 'redirect.class.php';

abstract class ComplexDisplay
{
	const PARAM_DISPLAY_ACTION = 'display_action';
	const PARAM_ROOT_LO = 'pid';
	const PARAM_CLOI_ID = 'cloi';
	const PARAM_SELECTED_CLOI_ID = 'selected_cloi';
	const PARAM_DELETE_SELECTED_CLOI = 'delete_selected_cloi';
	const PARAM_MOVE_SELECTED_CLOI = 'move_selected_cloi';
	const PARAM_TYPE = 'type';
	const PARAM_DIRECTION = 'direction';
    const PARAM_MOVE = 'move';

	const ACTION_DELETE = 'delete';
	const ACTION_UPDATE = 'update';
	const ACTION_CREATE = 'create';
	const ACTION_MOVE = 'move';
	const ACTION_BROWSE = 'browse';
    const ACTION_VIEW_ATTACHMENT = 'view_attachment';
    const ACTION_MOVE_TO_CATEGORY = 'move_to_category';
    const ACTION_EDIT = 'edit';

    const ACTION_VIEW_CLO = 'view';

	private $menu;
	private $root;
	private $cloi;
    private $parent;
    private static $instance;

	function ComplexDisplay($parent)
	{
		$this->parent = $parent;
		$action = Request :: get(self :: PARAM_DISPLAY_ACTION);

		//if(!$action)
		//	$action = self :: ACTION_VIEW_CLO;

		$this->set_action($action);

		$root_id = Request :: get(self :: PARAM_ROOT_LO);
		$cloi_id = Request :: get(self :: PARAM_CLOI_ID);

        if($root_id)
		$this->root = RepositoryDataManager :: get_instance()->retrieve_learning_object($root_id);
		if($cloi_id)
		{
			$cloi = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_item($cloi_id);
			if($cloi)
				$this->cloi = $cloi;
		}

	}

	static function factory($parent,$app_name)
	{
		if(is_null(self :: $instance))
		{
            if($parent->get_parent() instanceof WeblcmsManager)
            {
                $properties = $parent->get_tool()->get_properties();
                $name = $properties->name;
            }else
            {
                $name = $app_name;
            }
            $file = dirname(__FILE__) . '/' . $name . '/' . $name . '_display.class.php';
            require_once $file;
            $class = ucfirst($name).'Display';
            self :: $instance = new $class($parent);
		}

		return self :: $instance;
	}

	function run()
	{
		$action = $this->get_action();
		switch($action)
		{
            case self :: ACTION_DELETE :
                $component = ComplexDisplayComponent :: factory(null,'Deleter',$this);
                break;
            case self :: ACTION_UPDATE :
                $component = ComplexDisplayComponent :: factory(null,'Updater',$this);
                break;
            case self :: ACTION_CREATE:
                $component = ComplexDisplayComponent :: factory(null,'Creator',$this);
                break;
            case self :: ACTION_MOVE:
                $component = ComplexDisplayComponent :: factory(null,'Mover',$this);
                break;
            case self :: ACTION_BROWSE:
                $component = ComplexDisplayComponent :: factory(null,'Browser',$this);
                break;
            case self :: ACTION_VIEW_ATTACHMENT :
                $component = ComplexDisplayComponent :: factory(null,'AttachmentViewer',$this);
                break;
            case self :: ACTION_VIEW_CLO :
                $component = ComplexDisplayComponent :: factory(null, 'Viewer', $this);
                break;
			default :
				$this->set_action(self :: ACTION_VIEW_CLO);
				$component = ComplexDisplayComponent :: factory(null, 'Viewer', $this);
		}

		$component->run();
	}

//	private function parse_input_from_table()
//	{
//		if (isset ($_POST['action']))
//		{
//			$selected_ids = $_POST[RepositoryBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];
//			if (empty ($selected_ids))
//			{
//				$selected_ids = array ();
//			}
//			elseif (!is_array($selected_ids))
//			{
//				$selected_ids = array ($selected_ids);
//			}
//			switch ($_POST['action'])
//			{
//				case self :: PARAM_DELETE_SELECTED_CLOI :
//					$this->set_action(self :: ACTION_DELETE_CLOI);
//					$_GET[self :: PARAM_SELECTED_CLOI_ID] = $selected_ids;
//
//					break;
//			}
//		}
//	}

	function get_action()
	{
		return $this->get_parameter(self :: PARAM_DISPLAY_ACTION);
	}

	function set_action($action)
	{
		$this->set_parameter(self :: PARAM_DISPLAY_ACTION, $action);
	}

	function get_parent()
	{
		return $this->parent;
	}

	function set_parent($parent)
	{
		$this->parent = $parent;
	}

	function set_parameter($parameter, $value)
	{
		$this->get_parent()->set_parameter($parameter, $value);
	}

	function get_parameter($parameter)
	{
		return $this->get_parent()->get_parameter($parameter);
	}

	function get_parameters()
	{
		return $this->get_parent()->get_parameters();
	}

	function display_header($breadcrumbtrail)
	{
		$this->get_parent()->display_header($breadcrumbtrail, false, false);
	}

	function display_footer()
	{
		$this->get_parent()->display_footer();
	}

	function display_message($message)
	{
		$this->get_parent()->display_message($message);
	}

	function display_error_message($message)
	{
		$this->get_parent()->display_error_message($message);
	}

	function display_warning_message($message)
	{
		$this->get_parent()->display_warning_message($message);
	}

	function display_error_page($message)
	{
		$this->get_parent()->display_error_page($message);
	}

	function display_warning_page($message)
	{
		$this->get_parent()->display_warning_page($message);
	}

	function display_popup_form($form_html)
	{
		$this->get_parent()->display_popup_form($form_html);
	}

	function redirect($message = null, $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false)
	{
        $this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities);
//		if (isset($message))
//		{
//			$parameters[$error_message ? Redirect :: PARAM_ERROR_MESSAGE :  Redirect :: PARAM_MESSAGE] = $message;
//		}
//
//		$parameters = array_merge($this->get_parent()->get_parameters(), $parameters);
//		Redirect :: url($parameters, $filter, $encode_entities);
	}

	function get_url($additional_parameters = array ())
	{
		return $this->get_parent()->get_url($additional_parameters);
	}

	function get_user()
	{
		return $this->get_parent()->get_user();
	}

	function get_user_id()
	{
		return $this->get_parent()->get_user_id();
	}

	function get_root_lo()
	{
		return $this->root;
	}

	function get_cloi()
	{
		return $this->cloi;
	}

	/**
	 * Common functionality
	 */

	function get_clo_table_html($show_subitems_column = true, $model = null, $renderer = null)
	{
		$parameters = array(self :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), self :: PARAM_CLOI_ID => ($this->get_cloi()?$this->get_cloi()->get_id():null));

		if($this->get_cloi())
		{
			$parameters[self :: PARAM_CLOI_ID] = $this->get_cloi()->get_id();
		}

		$table = new ComplexBrowserTable($this, array_merge($this->get_parameters(), $parameters), $this->get_clo_table_condition(), $show_subitems_column, $model, $renderer);
		return $table->as_html();
	}

	function get_clo_table_condition()
	{
		if($this->get_cloi())
		{
			return new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $this->get_cloi()->get_ref());
		}
		return new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $this->get_root_lo()->get_id());
	}

	function get_clo_menu()
	{
		if(is_null($this->menu))
		{
			$this->build_menu();
		}
		return $this->menu->render_as_tree();
	}

	function get_clo_breadcrumbs()
	{
		if(is_null($this->menu))
		{
			$this->build_menu();
		}
		return $this->menu->get_breadcrumbs();
	}

	private function build_menu()
	{
		$this->menu = new ComplexMenu($this->get_root_lo(), $this->get_cloi());
	}

	function get_root()
	{
		return $this->get_root_lo()->get_id();
	}

	//url building

	function get_complex_learning_object_item_edit_url($cloi, $root_id)
	{
		return $this->get_url(array(self :: PARAM_DISPLAY_ACTION => self :: ACTION_UPDATE_CLOI,
									self :: PARAM_ROOT_LO => $root_id,
									self :: PARAM_SELECTED_CLOI_ID => $cloi->get_id(),
									self :: PARAM_CLOI_ID => ($this->get_cloi()?$this->get_cloi()->get_id():null),
									'publish' => Request :: get('publish')));
	}

	function get_complex_learning_object_item_delete_url($cloi, $root_id)
	{
		return $this->get_url(array(self :: PARAM_DISPLAY_ACTION => self :: ACTION_DELETE_CLOI,
									self :: PARAM_ROOT_LO => $root_id,
									self :: PARAM_SELECTED_CLOI_ID => $cloi->get_id(),
									self :: PARAM_CLOI_ID => ($this->get_cloi()?$this->get_cloi()->get_id():null),
									'publish' => Request :: get('publish')));
	}

	function get_complex_learning_object_item_move_url($cloi, $root_id, $direction)
	{
		return $this->get_url(array(self :: PARAM_DISPLAY_ACTION => self :: ACTION_MOVE_CLOI,
									self :: PARAM_ROOT_LO => $root_id,
									self :: PARAM_SELECTED_CLOI_ID => $cloi->get_id(),
									self :: PARAM_DIRECTION => $direction,
									self :: PARAM_CLOI_ID => ($this->get_cloi()?$this->get_cloi()->get_id():null),
									'publish' => Request :: get('publish')));
	}

	function get_action_bar($lo)
	{
		$pub = Request :: get('publish');

		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		if($pub && $pub != '')
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $_SESSION['redirect_url']));
			return $action_bar;
		}


	}

	function get_creation_links($lo, $types = array())
	{
		$html[] = '<div class="category_form"><div id="learning_object_selection">';

		if(count($types) == 0)
			$types = $lo->get_allowed_types();

		foreach($types as $type)
		{
			$url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_CREATE_CLOI, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), ComplexBuilder :: PARAM_CLOI_ID => ($this->get_cloi()?$this->get_cloi()->get_id():null), 'publish' => Request :: get('publish')));
			$html[] = '<a href="'. $url .'"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'learning_object/big/' . $type . '.png);">';
			$html[] = Translation :: get(LearningObject :: type_to_class($type).'TypeName');
			$html[] = '<div class="clear">&nbsp;</div>';
			$html[] = '</div></a>';
		}

		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/repository.js');
		$html[] = '</div>';
		$html[] = '<div class="clear">&nbsp;</div>';
		$html[] = '</div>';

		return implode("\n", $html);
	}
}

?>