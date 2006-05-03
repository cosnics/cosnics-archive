<?php
require_once dirname(__FILE__).'/repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/repositorysearchform.class.php';
require_once dirname(__FILE__).'/../repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../learningobjectcategorymenu.class.php';
require_once dirname(__FILE__).'/../learningobject.class.php';
require_once dirname(__FILE__).'/../condition/orcondition.class.php';
require_once dirname(__FILE__).'/../condition/equalitycondition.class.php';
require_once dirname(__FILE__).'/../learning_object_table/learningobjecttable.class.php';
require_once dirname(__FILE__).'/../../../claroline/inc/lib/formvalidator/FormValidator.class.php';

class RepositoryManager
{
	// SortableTable hogs 'action' so we'll use something else.
	const PARAM_ACTION = 'go';
	const PARAM_MESSAGE = 'message';

	const PARAM_CATEGORY_ID = 'category';
	const PARAM_LEARNING_OBJECT_ID = 'object';
	const PARAM_DESTINATION_LEARNING_OBJECT_ID = 'destination';
	const PARAM_LEARNING_OBJECT_TYPE = 'type';

	const PARAM_DELETE_SELECTED = 'delete_selected';
	const PARAM_MOVE_SELECTED = 'move_selected';

	const ACTION_BROWSE_LEARNING_OBJECTS = 'browse';
	const ACTION_VIEW_LEARNING_OBJECTS = 'view';
	const ACTION_CREATE_LEARNING_OBJECTS = 'create';
	const ACTION_EDIT_LEARNING_OBJECTS = 'edit';
	const ACTION_DELETE_LEARNING_OBJECTS = 'delete';
	const ACTION_MOVE_LEARNING_OBJECTS = 'move';
	const ACTION_EDIT_LEARNING_OBJECT_METADATA = 'metadata';
	const ACTION_EDIT_LEARNING_OBJECT_RIGHTS = 'rights';
	const ACTION_VIEW_QUOTA = 'quota';

	private $parameters;
	
	private $search_parameters;

	private $user_id;

	private $category_id;

	private $search_form;

	private $category_menu;

	function RepositoryManager($user_id)
	{
		$this->user_id = $user_id;
		$this->parameters = array ();
		$this->set_action($_GET[self :: PARAM_ACTION]);
		$this->parse_input_from_table();
		$this->determine_search_settings();
	}

	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_VIEW_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_CREATE_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Creator', $this);
				break;
			case self :: ACTION_EDIT_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Editor', $this);
				break;
			case self :: ACTION_DELETE_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_MOVE_LEARNING_OBJECTS :
				$component = RepositoryManagerComponent :: factory('Mover', $this);
				break;
			case self :: ACTION_EDIT_LEARNING_OBJECT_METADATA :
				$component = RepositoryManagerComponent :: factory('MetadataEditor', $this);
				break;
			case self :: ACTION_EDIT_LEARNING_OBJECT_RIGHTS :
				$component = RepositoryManagerComponent :: factory('RightsEditor', $this);
				break;
			case self :: ACTION_VIEW_QUOTA :
				$component = RepositoryManagerComponent :: factory('QuotaViewer', $this);
				break;
			default :
				$this->set_action(self :: ACTION_BROWSE_LEARNING_OBJECTS);
				$component = RepositoryManagerComponent :: factory('Browser', $this);
		}
		$component->run();
	}

	// TODO: Clean this up. It's all SortableTable's fault. :-(
	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			$selected_ids = $_POST[LearningObjectTable :: DEFAULT_NAME.LearningObjectTable :: CHECKBOX_NAME_SUFFIX];
			if (empty ($selected_ids))
			{
				$selected_ids = array ();
			}
			elseif (!is_array($selected_ids))
			{
				$selected_ids = array ($selected_ids);
			}
			switch ($_POST['action'])
			{
				case self :: PARAM_DELETE_SELECTED :
					$this->set_action(self :: ACTION_DELETE_LEARNING_OBJECTS);
					$_GET[self :: PARAM_LEARNING_OBJECT_ID] = $selected_ids;
					break;
				case self :: PARAM_MOVE_SELECTED :
					$this->set_action(self :: ACTION_MOVE_LEARNING_OBJECTS);
					$_GET[self :: PARAM_LEARNING_OBJECT_ID] = $selected_ids;
					break;
			}
		}
	}

	function get_action()
	{
		return $this->get_parameter(self :: PARAM_ACTION);
	}

	function set_action($action)
	{
		return $this->set_parameter(self :: PARAM_ACTION, $action);
	}

	function display_header($breadcrumbs = array (), $display_search = false)
	{
		// TODO: Breadcrumbs.
		Display :: display_header(api_get_setting('siteName'));
		echo '<div style="float: left; width: 20%;">';
		$this->display_type_selector_for_creation();
		$this->display_learning_object_categories();
		echo '</div>';
		echo '<div style="float: right; width: 80%;">';
		if ($msg = $_GET[self :: PARAM_MESSAGE])
		{
			$this->display_message($msg);
		}
		if ($display_search)
		{
			$this->display_search_form();
		}
	}

	function display_footer()
	{
		echo '</div>';
		echo '<div style="clear: both;"></div>';
		// TODO: Find out why we need to reconnect here.
		global $dbHost, $dbLogin, $dbPass, $mainDbName;
		mysql_connect($dbHost, $dbLogin, $dbPass);
		mysql_select_db($mainDbName);
		Display :: display_footer();
	}

	function display_message($message)
	{
		Display :: display_normal_message($message);
	}

	function display_error_message($message)
	{
		Display :: display_error_message($message);
	}

	function display_error_page($message)
	{
		$this->display_header();
		$this->display_error_message($message);
		$this->display_footer();
	}

	function display_popup_form($form_html)
	{
		Display :: display_normal_message($form_html);
	}

	function get_parameters($include_search = false)
	{
		if ($include_search && isset($this->search_parameters))
		{
			return array_merge($this->search_parameters, $this->parameters);
		}
		return $this->parameters;
	}

	function get_parameter($name)
	{
		return $this->parameters[$name];
	}
	
	function set_parameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}

	function get_search_parameter($name)
	{
		return $this->search_parameters[$name];
	}
	
	function redirect($action = self :: ACTION_BROWSE_LEARNING_OBJECTS, $message = null, $new_category_id = 0)
	{
		$params = array ();
		$params[self :: PARAM_ACTION] = $action;
		if (isset ($message))
		{
			$params[self :: PARAM_MESSAGE] = $message;
		}
		if ($new_category_id)
		{
			$params[self :: PARAM_CATEGORY_ID] = $new_category_id;
		}
		$url = $this->get_url($params);
		header('Location: '.$url);
	}

	function get_url($additional_parameters = array (), $include_search = false)
	{
		$eventual_parameters = array_merge($this->get_parameters($include_search), $additional_parameters);
		$string = http_build_query($eventual_parameters);
		return $_SERVER['PHP_SELF'].'?'.$string;
	}

	function get_user_id()
	{
		return $this->user_id;
	}

	function get_root_category_id()
	{
		if (isset($this->category_menu))
		{
			$keys = array_keys($this->category_menu->_menu);
			return $keys[0];
		}
		else
		{
			$dm = RepositoryDataManager :: get_instance();
			$cat = $dm->retrieve_root_category($this->get_user_id());
			return $cat->get_id();
		}
	}

	function retrieve_learning_object($id, $type = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_learning_object($id, $type);
	}

	function retrieve_learning_objects($type = null, $condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_learning_objects($type, $condition, $orderBy, $orderDir, $offset, $maxObjects);
	}

	function count_learning_objects($type = null, $condition = null)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->count_learning_objects($type, $condition);
	}

	function learning_object_deletion_allowed($learning_object)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->learning_object_deletion_allowed($learning_object);
	}

	function get_learning_object_publication_attributes($id)
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->get_learning_object_publication_attributes($id);
	}

	function get_learning_object_viewing_url($learning_object)
	{
		if ($learning_object->get_type() == 'category')
		{
			return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_BROWSE_LEARNING_OBJECTS, self :: PARAM_CATEGORY_ID => $learning_object->get_id()));
		}
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}

	function get_learning_object_editing_url($learning_object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}

	function get_learning_object_deletion_url($learning_object)
	{
		if (!$this->learning_object_deletion_allowed($learning_object))
		{
			return null;
		}
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_DELETE_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}

	function get_learning_object_moving_url($learning_object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_MOVE_LEARNING_OBJECTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}

	function get_learning_object_metadata_editing_url($learning_object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_LEARNING_OBJECT_METADATA, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}

	function get_learning_object_rights_editing_url($learning_object)
	{
		return $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_EDIT_LEARNING_OBJECT_RIGHTS, self :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	}

	function get_learning_object_types()
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->get_registered_types();
	}

	function get_web_code_path()
	{
		return api_get_path(WEB_CODE_PATH);
	}

	function not_allowed()
	{
		api_not_allowed();
	}

	function get_user_info($id)
	{
		return api_get_user_info($id);
	}

	function get_type_filter_url($type)
	{
		$params = array ();
		$params[self :: PARAM_ACTION] = self :: ACTION_BROWSE_LEARNING_OBJECTS;
		$params[self :: PARAM_LEARNING_OBJECT_TYPE] = array ($type);
		return $this->get_url($params);
	}

	function get_search_condition()
	{
		return $this->get_search_form()->get_condition();
	}
	
	function get_category_condition($category_id)
	{
		$subcat = array();
		$this->get_category_id_list($category_id, & $this->category_menu->_menu, &$subcat);
		$conditions = array();
		foreach ($subcat as $cat)
		{
			$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $cat);
		}
		return (count($conditions) > 1 ? new OrCondition($conditions) : $conditions[0]);
	}
	
	private function get_category_id_list($category_id, & $node, & $subcat)
	{
		// XXX: Make sure we don't mess up things with trash here.
		// TODO: Move this to LearningObjectCategoryMenu or something.
		foreach ($node as $id => $subnode)
		{
			$new_id = ($id == $category_id ? null : $category_id);
			if (is_null($new_id))
			{
				$subcat[] = $id;
			}
			$this->get_category_id_list($new_id, & $subnode['sub'], & $subcat);
		}
	}

	private function determine_search_settings()
	{
		if (isset($_GET[self :: PARAM_CATEGORY_ID]))
		{
			$this->set_parameter(self :: PARAM_CATEGORY_ID, intval($_GET[self :: PARAM_CATEGORY_ID]));
		}
		$form = $this->get_search_form();
		if ($form->is_full_repository_search())
		{
			$this->set_parameter(self :: PARAM_CATEGORY_ID, $this->get_root_category_id());
		}
		$this->search_parameters = $form->get_frozen_values();
	}

	private function get_category_menu()
	{
		if (!isset ($this->category_menu))
		{
			// We need this because the percent sign in '%s' gets escaped.
			$temp_replacement = '__CATEGORY_ID__';
			$url_format = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_BROWSE_LEARNING_OBJECTS, self :: PARAM_CATEGORY_ID => $temp_replacement));
			$url_format = str_replace($temp_replacement, '%s', $url_format);
			$category = $this->get_parameter(self :: PARAM_CATEGORY_ID);
			if (!isset($category))
			{
				$category = $this->get_root_category_id();
			}
			$extra_items = array();
			$create = array();
			$create['title'] = get_lang('Create');
			$create['url'] = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_LEARNING_OBJECTS));
			$create['onclick'] = 'return toggleCreationForm();';
			$create['class'] = 'create';
			$extra_items[] = & $create;
			$quota = array();
			$quota['title'] = get_lang('Quota');
			$quota['url'] = $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_VIEW_QUOTA));
			$quota['class'] = 'quota';
			$extra_items[] = & $quota;
			// TODO: Implement recycle bin.
			$trash = array();
			$trash['title'] = get_lang('RecycleBin');
			$trash['url'] = 'javascript:void(0);';
			$trash['onclick'] = 'alert(&quot;Sorry, not implemented.&quot;);';
			$trash['class'] = 'trash';
			$extra_items[] = & $trash;
			$this->category_menu = new LearningObjectCategoryMenu($this->get_user_id(), $category, $url_format, & $extra_items);
		}
		return $this->category_menu;
	}

	private function get_search_form()
	{
		if (!isset ($this->search_form))
		{
			$this->search_form = new RepositorySearchForm($this, $this->get_url());
		}
		return $this->search_form;
	}

	private function display_learning_object_categories()
	{
		echo $this->get_category_menu()->render_as_tree();
	}

	private function display_search_form()
	{
		echo $this->get_search_form()->display();
	}

	private function display_type_selector_for_creation()
	{
		$form_id = 'lo_creation_form';
		echo '<div id="'.$form_id.'" class="select_type_create" style="position: absolute; left: 5%; top: 20%; text-align: center; margin: 0; padding: 1em; background: #FC6; border: 2px dashed black; width: 90%; display: none;">';
		$form = new FormValidator('create_type', 'post', $this->get_url(array (self :: PARAM_ACTION => self :: ACTION_CREATE_LEARNING_OBJECTS)));
		$type_options = array ();
		$type_options[''] = '';
		foreach ($this->get_learning_object_types() as $type)
		{
			$type_options[$type] = get_lang($type.'TypeName');
		}
		asort($type_options);
		$form->addElement('select', self :: PARAM_LEARNING_OBJECT_TYPE, get_lang('CreateANew'), $type_options);
		$form->addElement('submit', 'submit', get_lang('Go'));
		$form->addElement('static', null, null, '<a href="javascript:void(0);" onclick="toggleCreationForm();">'.get_lang('Hide').'</a>');
		$renderer = clone $form->defaultRenderer();
		$renderer->setElementTemplate('{label} {element} ');
		$form->accept($renderer);
		echo $renderer->toHTML();
		echo '</div>';
		echo <<<END
<script type="text/javascript">
function toggleCreationForm()
{
	var div = document.getElementById('$form_id');
	div.style.display = (div.style.display == 'block' ? 'none' : 'block');
	return false;
}
</script>
END;
	}
}
?>