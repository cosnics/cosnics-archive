<?php
/**
 * @package application.lib.encyclopedia
 */
require_once dirname(__FILE__) . '/platform_category.class.php';
require_once dirname(__FILE__) . '/category_form.class.php';
require_once dirname(__FILE__) . '/category_manager_component.class.php';
/**
==============================================================================
 *	This class provides the means to manage categories.
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

abstract class CategoryManager
{
	const PARAM_ACTION = 'category_action';
	const PARAM_CATEGORY_ID = 'category_id';
	const PARAM_DIRECTION = 'direction';
	const PARAM_REMOVE_SELECTED_CATEGORIES = 'remove_selected_categories';
	
	const ACTION_BROWSE_CATEGORIES = 'browse_categories';
	const ACTION_CREATE_CATEGORY = 'create_category';
	const ACTION_UPDATE_CATEGORY = 'update_category';
	const ACTION_DELETE_CATEGORY = 'delete_category';
	const ACTION_MOVE_CATEGORY = 'move_category';
	
	private $parent;
	
	private $publisher_actions;
	
	private $parameters;
	
	/**
	 * Constructor.
	 * @param array $types The learning object types that may be published.
	 * @param  boolean $email_option If true the publisher has the option to
	 * send the published learning object by email to the selecter target users.
	 */
	function CategoryManager($parent)
	{
		$this->parent = $parent;
		$parent->set_parameter(self :: PARAM_ACTION, $this->get_action());
	}
	
	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{
			case self :: ACTION_BROWSE_CATEGORIES :
				$component = CategoryManagerComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_CREATE_CATEGORY :
				$component = CategoryManagerComponent :: factory('Creator', $this);
				break;
			case self :: ACTION_UPDATE_CATEGORY :
				$component = CategoryManagerComponent :: factory('Updater', $this);
				break;
			case self :: ACTION_DELETE_CATEGORY :
				$component = CategoryManagerComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_MOVE_CATEGORY :
				$component = CategoryManagerComponent :: factory('Mover', $this);
				break;
			default :
				$component = CategoryManagerComponent :: factory('Browser', $this);
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
		return new PlatformCategory();
	}
	
	function get_category_form()
	{
		return new CategoryForm();
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
	
	abstract function count_categories($condition);
	abstract function retrieve_categories($condition, $offset, $count, $order_property, $order_direction);
}
?>