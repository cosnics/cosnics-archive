<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of feeback_manager
 *
 * @author pieter
 */

require_once dirname(__FILE__).'/feedback_manager_component.class.php';

 class FeedbackManager {

    const PARAM_ACTION = 'feedback_action';
	const PARAM_FEEDBACK_ID = 'feedback_id';
	//const PARAM_DIRECTION = 'direction';
	const PARAM_REMOVE_FEEDBACK = 'remove_feedback';
	//const PARAM_MOVE_SELECTED_CATEGORIES = 'move_selected_categories';

	//const ACTION_BROWSE_CATEGORIES = 'browse_categories';
	const ACTION_CREATE_FEEDBACK = 'create_feedback';
	const ACTION_UPDATE_FEEDBACK = 'update_feedback';
	const ACTION_DELETE_FEEDBACK = 'delete_feedback';
	//const ACTION_MOVE_CATEGORY = 'move_category';
	//const ACTION_CHANGE_CATEGORY_PARENT = 'change_category_parent';
	//const ACTION_COPY_GENERAL_CATEGORIES = 'copy_general_categories';
	//const ACTION_AJAX_MOVE_CATEGORIES = 'ajax_move_categories';
	//const ACTION_AJAX_DELETE_CATEGORIES = 'ajax_delete_categories';

	private $parent;

	private $publisher_actions;

	private $parameters;

    private $trail;

	/**
	 * Constructor.
	 * @param array $types The learning object types that may be published.
	 * @param  boolean $email_option If true the publisher has the option to
	 * send the published learning object by email to the selecter target users.
	 */
	function FeedbackManager($parent,$trail)
	{
		$this->parent = $parent;
        $this->trail = $trail;
		//$parent->set_parameter(self :: PARAM_ACTION, $this->get_action());
		//$this->parse_input_from_table();
	}

	function run($action)
	{
        
		//$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			/*case self :: ACTION_BROWSE_CATEGORIES :
				$component = CategoryManagerComponent :: factory('Browser', $this);
				break;*/
			case self :: ACTION_CREATE_FEEDBACK :
            
				$component = FeedbackManagerComponent :: factory('Creator', $this);
				break;
			case self :: ACTION_UPDATE_FEEDBACK :
				$component = FeedbackManagerComponent :: factory('Updater', $this);
				break;
			case self :: ACTION_DELETE_FEEDBACK :
				$component = FeedbackManagerComponent :: factory('Deleter', $this);
				break;
			/*case self :: ACTION_MOVE_CATEGORY :
				$component = CategoryManagerComponent :: factory('Mover', $this);
				break;
			case self :: ACTION_CHANGE_CATEGORY_PARENT :
				$component = CategoryManagerComponent :: factory('ParentChanger', $this);
				break;
			case self :: ACTION_COPY_GENERAL_CATEGORIES :
				$component = CategoryManagerComponent :: factory('GeneralCategoriesCopier', $this);
				break;
			case self :: ACTION_AJAX_MOVE_CATEGORIES :
				$component = CategoryManagerComponent :: factory('AjaxCategoryMover', $this);
				break;
			case self :: ACTION_AJAX_DELETE_CATEGORIES :
				$component = CategoryManagerComponent :: factory('AjaxCategoryDeleter', $this);
				break;*/
			default :
				$component = CategoryManagerComponent :: factory('Creator', $this);
		}
		$component->run();

	}

	/**
	 * Returns the tool which created this publisher.
	 * @return Tool The tool.
	 */
	function get_parent()
	{
		return $this->parent;
	}

	function display_header($breadcrumbtrail)
	{
		return $this->parent->display_header($breadcrumbtrail, false, false);
	}

	function display_footer()
	{
		return $this->parent->display_footer();
	}

	/**
	 * @see Tool::get_user_id()
	 */
	function get_user_id()
	{
		return $this->parent->get_user_id();
	}

	function get_user()
	{
		return $this->parent->get_user();
	}

	/**
	 * Returns the action that the user selected, or "publicationcreator" if none.
	 * @return string The action.
	 */
	function get_action()
	{
		return $_GET[self :: PARAM_ACTION];
	}

	function get_url($parameters = array(), $encode = false)
	{
		return $this->parent->get_url($parameters, $encode);
	}

	function get_parameters()
	{
		return $this->parent->get_parameters();
	}

	function set_parameter($name, $value)
	{
		$this->parent->set_parameter($name, $value);
	}

	/**
	 * Sets a default learning object. When the creator component of this
	 * publisher is displayed, the properties of the given learning object will
	 * be used as the default form values.
	 * @param string $type The learning object type.
	 * @param LearningObject $learning_object The learning object to use as the
	 *                                        default for the given type.
	 */
	function set_default_learning_object($type, $learning_object)
	{
		$this->default_learning_objects[$type] = $learning_object;
	}

	function get_default_learning_object($type)
	{
		if(isset($this->default_learning_objects[$type]))
		{
			return $this->default_learning_objects[$type];
		}
		return new AbstractLearningObject($type, $this->get_user_id());
	}

	function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
	{
		return $this->parent->redirect($action, $message, $error_message, $extra_params);
	}

	function repository_redirect($action = null, $message = null, $cat_id = 0, $error_message = false, $extra_params = array())
	{
		return $this->parent->redirect($action, $message, $cat_id, $error_message, $extra_params);
	}

	function get_extra_parameters()
	{
		return $this->parameters;
	}

	function set_extra_parameters($parameters)
	{
		$this->parameters = $parameters;
	}

	function get_category()
	{
		return new FeedBack();
	}

	/*function get_category_form()
	{
		return new CategoryForm();
	}*/

	/*function allowed_to_delete_category($category_id)
	{
		return true;
	}

	function allowed_to_edit_category($category_id)
	{
		return true;
	}

	function get_browse_categories_url($category_id = 0)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CATEGORIES,
								    self :: PARAM_CATEGORY_ID => $category_id));
	}

	function get_create_category_url($category_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_CATEGORY,
									self :: PARAM_CATEGORY_ID => $category_id));
	}

	function get_update_category_url($category_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_UPDATE_CATEGORY,
								    self :: PARAM_CATEGORY_ID => $category_id));
	}

	function get_delete_category_url($category_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CATEGORY,
								    self :: PARAM_CATEGORY_ID => $category_id));
	}

	function get_move_category_url($category_id, $direction = 1)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_CATEGORY,
								    self :: PARAM_CATEGORY_ID => $category_id,
								    self :: PARAM_DIRECTION => $direction));
	}

	function get_change_category_parent_url($category_id)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CHANGE_CATEGORY_PARENT,
								    self :: PARAM_CATEGORY_ID => $category_id));
	}

	function get_copy_general_categories_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_COPY_GENERAL_CATEGORIES));
	}*/

	/*private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			$selected_ids = $_POST['category_table'.ObjectTable :: CHECKBOX_NAME_SUFFIX];

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
				case self :: PARAM_REMOVE_SELECTED_CATEGORIES :
					$this->set_parameter(self :: PARAM_ACTION, self :: ACTION_DELETE_CATEGORY);
					$_GET[self :: PARAM_ACTION] = self :: ACTION_DELETE_CATEGORY;
					$_GET[self :: PARAM_CATEGORY_ID] = $selected_ids;
					break;
				case self :: PARAM_MOVE_SELECTED_CATEGORIES :
					$this->set_parameter(self :: PARAM_ACTION, self :: ACTION_CHANGE_CATEGORY_PARENT);
					$_GET[self :: PARAM_ACTION] = self :: ACTION_CHANGE_CATEGORY_PARENT;
					$_GET[self :: PARAM_CATEGORY_ID] = $selected_ids;
					break;
			}
		}
	}*/

	function get_breadcrumb_trail()
	{
		return $this->trail;
	}

	//abstract function count_categories($condition);
	//abstract function retrieve_feedbacks($condition, $offset, $count, $order_property, $order_direction);
	//abstract function get_next_category_display_order($parent_id);

}
?>
