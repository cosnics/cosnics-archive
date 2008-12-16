<?php
require_once dirname(__FILE__).'/rights_utilities.class.php';
/**
 * @package users
 */
/**
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */

class Location
{
	const PROPERTY_ID = 'id';
	const PROPERTY_LOCATION = 'location';
	const PROPERTY_LEFT_VALUE = 'left_value';
	const PROPERTY_RIGHT_VALUE = 'right_value';
	const PROPERTY_PARENT = 'parent';
	const PROPERTY_APPLICATION  = 'application';
	const PROPERTY_TYPE  = 'type';
	const PROPERTY_IDENTIFIER  = 'identifier';
	const PROPERTY_INHERIT  = 'inherit';
	const PROPERTY_LOCKED  = 'locked';
	
	/**#@-*/

	/**
	 * Default properties of the user object, stored in an associative
	 * array.
	 */
	private $defaultProperties;

	function update() 
	{
		$rdm = RightsDataManager :: get_instance();
		$success = $rdm->update_location($this);
		if (!$success)
		{
			return false;
		}

		return true;	
	}

	/**
	 * Creates a new user object.
	 * @param int $id The numeric ID of the user object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the user
	 *                                 object. Associative array.
	 */
	function Location($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this user object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this user.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all users.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_LOCATION, self :: PROPERTY_LEFT_VALUE, self :: PROPERTY_RIGHT_VALUE, self :: PROPERTY_PARENT, self :: PROPERTY_APPLICATION, self :: PROPERTY_TYPE, self :: PROPERTY_IDENTIFIER, self :: PROPERTY_INHERIT, self :: PROPERTY_LOCKED);
	}
		
	/**
	 * Sets a default property of this user by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default user
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
	
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
		
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	function get_location()
	{
		return $this->get_default_property(self :: PROPERTY_LOCATION);
	}
		
	function set_location($location)
	{
		$this->set_default_property(self :: PROPERTY_LOCATION, $location);
	}
	
	function get_left_value()
	{
		return $this->get_default_property(self :: PROPERTY_LEFT_VALUE);
	}
		
	function set_left_value($left_value)
	{
		$this->set_default_property(self :: PROPERTY_LEFT_VALUE, $left_value);
	}
	
	function get_right_value()
	{
		return $this->get_default_property(self :: PROPERTY_RIGHT_VALUE);
	}
		
	function set_right_value($right_value)
	{
		$this->set_default_property(self :: PROPERTY_RIGHT_VALUE, $right_value);
	}
	
	function get_parent()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT);
	}
		
	function set_parent($parent)
	{
		$this->set_default_property(self :: PROPERTY_PARENT, $parent);
	}
	
	function get_application()
	{
		return $this->get_default_property(self :: PROPERTY_APPLICATION);
	}
		
	function set_application($application)
	{
		$this->set_default_property(self :: PROPERTY_APPLICATION, $application);
	}
	
	function get_type()
	{
		return $this->get_default_property(self :: PROPERTY_TYPE);
	}
		
	function set_type($type)
	{
		$this->set_default_property(self :: PROPERTY_TYPE, $type);
	}
	
	function get_identifier()
	{
		return $this->get_default_property(self :: PROPERTY_IDENTIFIER);
	}
		
	function set_identifier($identifier)
	{
		$this->set_default_property(self :: PROPERTY_IDENTIFIER, $identifier);
	}
	
	function get_inherit()
	{
		return $this->get_default_property(self :: PROPERTY_INHERIT);
	}
		
	function set_inherit($inherit)
	{
		$this->set_default_property(self :: PROPERTY_INHERIT, $inherit);
	}
	
	function inherits()
	{
		return $this->get_inherit();
	}
	
	function get_locked()
	{
		return $this->get_default_property(self :: PROPERTY_LOCKED);
	}
		
	function set_locked($locked)
	{
		$this->set_default_property(self :: PROPERTY_LOCKED, $locked);
	}
	
	function is_locked()
	{
		return $this->get_locked();
	}
	
	function lock()
	{
		$this->set_locked(true);
	}
	
	function unlock()
	{
		$this->set_locked(false);
	}
	
	function is_root()
	{
		$parent = $this->get_parent();
		return ($parent == 0);
	}
	
	/**
	 * Instructs the Datamanager to delete this user.
	 * @return boolean True if success, false otherwise.
	 */
	function delete()
	{
		return RightsDataManager :: get_instance()->delete_location($this);
	}
	
	function create()
	{
		$rdm = RightsDataManager :: get_instance();
		$this->set_id($rdm->get_next_location_id());
		return $rdm->create_location($this);
	}
	
	function is_child_of($parent_id)
	{
		$rdm = RightsDataManager :: get_instance();
		
		$parent = $rdm->retrieve_location($parent_id);
		// TODO: What if $parent is invalid ? Return error

        // Check if the left and right value of the child are within the
        // left and right value of the parent, if so it is a child
        if ($parent->get_left_value() < $this->get_left_value() && $parent->get_right_value() > $this->get_right_value())
        {
            return true;
        }

        return false;
	}
	
	/**
	 * Get the locations on the same level with the same parent
	 */
	function get_siblings($include_self = true)
	{
		$rdm = RightsDataManager :: get_instance();
		
		$siblings_conditions = array();
		$siblings_conditions[] = new EqualityCondition(Location :: PROPERTY_PARENT, $this->get_parent());
		$siblings_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->get_application());
		
		if (!$include_self)
		{
			$siblings_conditions[] = new NotCondition(new EqualityCondition(Location :: PROPERTY_ID, $this->get_id()));
		}
		
		$siblings_condition = new AndCondition($siblings_conditions);
			
		return $rdm->retrieve_locations($siblings_condition);
	}
	
	/**
	 * Get the location's first level children
	 */
	function get_children()
	{
		$rdm = RightsDataManager :: get_instance();
		
		$children_conditions = array();
		$children_conditions[] = new EqualityCondition(Location :: PROPERTY_PARENT, $this->get_id());
		$children_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->get_application());
		
		$children_condition = new AndCondition($children_conditions);
			
		return $rdm->retrieve_locations($children_condition);
	}
	
	/**
	 * Get all of the location's parents 
	 */
	function get_parents($include_self = true)
	{
		$rdm = RightsDataManager :: get_instance();
		
		$parent_conditions = array();
		if ($include_self)
		{
			$parent_conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN_OR_EQUAL, $this->get_left_value());
			$parent_conditions[] = new InequalityCondition(Location :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $this->get_right_value());
		}
		else
		{
			$parent_conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $this->get_left_value());
			$parent_conditions[] = new InequalityCondition(Location :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $this->get_right_value());
		}
		$parent_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->get_application());
		
		$parent_condition = new AndCondition($parent_conditions);
		$order = array(Location :: PROPERTY_LEFT_VALUE);
		$order_direction = array(SORT_DESC);
			
		return $rdm->retrieve_locations($parent_condition, null, null, $order, $order_direction);
	}
	
	function get_parent_location($include_self = true)
	{
		$rdm = RightsDataManager :: get_instance();
			
		return $rdm->retrieve_location($this->get_parent());
	}
	
	function get_locked_parent()
	{
		$rdm = RightsDataManager :: get_instance();
		
		$locked_parent_conditions = array();
		$locked_parent_conditions[] = new InequalityCondition(Location :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $this->get_left_value());
		$locked_parent_conditions[] = new InequalityCondition(Location :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $this->get_right_value());
		$locked_parent_conditions[] = new EqualityCondition(Location :: PROPERTY_APPLICATION, $this->get_application());
		$locked_parent_conditions[] = new EqualityCondition(Location :: PROPERTY_LOCKED, true);
		
		$locked_parent_condition = new AndCondition($locked_parent_conditions);
		$order = array(Location :: PROPERTY_LEFT_VALUE);
		$order_direction = array(SORT_ASC);
		
		$locked_parents = $rdm->retrieve_locations($locked_parent_condition, null, 1, $order, $order_direction);
		
		if ($locked_parents->size() > 0)
		{
			return $locked_parents->next_result();
		}
		else
		{
			return null;
		}
	}
	
	function move($new_parent_id, $new_previous_id = 0)
	{
		$rdm = RightsDataManager :: get_instance();
		
		if (!$rdm->move_location_nodes($this, $new_parent_id, $new_previous_id))
		{
			return false;
		}
		
		return true;
	}
	
	function remove()
	{
		$rdm = RightsDataManager :: get_instance();
		
		// Delete the actual location
		if (!$rdm->delete_location_nodes($this))
		{
			return false;
		}
		
		// Update left and right values
		if (!$rdm->delete_nested_values($this))
		{
        	// TODO: Some kind of general error handling framework would be nice: PEAR-ERROR maybe ?
        	return false;
		}
		
        return true;
	}
	
	function add($previous_id = 0)
	{
		$rdm = RightsDataManager :: get_instance();
		$parent_id = $this->get_parent();
		
        $previous_visited = 0;

        if ($parent_id || $previous_id)
        {
            if ($previous_id)
            {
            	$node = $rdm->retrieve_location($previous_id);
            	$parent_id = $node->get_parent();
            	
            	// TODO: If $node is invalid, what then ?
            }
            else
            {
            	$node = $rdm->retrieve_location($parent_id);
            }
            
            // Set the new location's parent id
            $this->set_parent($parent_id);

			// TODO: If $node is invalid, what then ?

            // get the "visited"-value where to add the new element behind
            // if $previous_id is given, we need to use the right-value
            // if only the $parent_id is given we need to use the left-value
            $previous_visited = $previous_id ? $node->get_right_value() : $node->get_left_value();
            
            // Correct the left and right values wherever necessary.
            if (!$rdm->add_nested_values($this, $previous_visited, 1))
            {
            	// TODO: Some kind of general error handling framework would be nice: PEAR-ERROR maybe ?
            	return false;
            }
        }
        
        // Left and right values have been shifted so now we
        // want to really add the location itself, but first
        // we have to set it's left and right value.
        $this->set_left_value($previous_visited + 1);
        $this->set_right_value($previous_visited + 2);
        if (!$location->create())
        {
        	return false;
        }
        
        return true;
	}
}
?>