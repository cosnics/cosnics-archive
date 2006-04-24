<?php
require_once dirname(__FILE__).'/repositorydatamanager.class.php';

/**
==============================================================================
 *	This class represents a learning object in the repository. Every object
 *	that can be associated with a module is in fact a learning object.
 *
 *	Learning objects have a number of default properties:
 *	- id: the numeric ID of the learning object;
 *	- owner: the ID of the user who owns the learning object;
 *	- title: the title of the learning object;
 *	- description: a brief description of the learning object; may also be
 *	  used to store its content in select cases;
 *	- parent: the numeric ID of the parent object of this learning object;
 *    this is a learning object by itself, usually a category;
 *  - display_order: a number giving the learning object a position among its
 *    siblings; only applies if the learning object is ordered;
 *	- created: the date when the learning object was created, as returned by
 *	  PHP's time() function (UNIX time, seconds since the epoch);
 *	- modified: the date when the learning object was last modified, as
 *	  returned by PHP's time() function.
 *
 *	Actual learning objects must be instances of extensions of this class.
 *	They may define additional properties which are specific to that
 *	particular type of learning object, e.g. the path to a document. This
 *	class provides a framework for that purpose.
 *
 * To access the values of the properties, this class and its subclasses
 * should provide accessor methods. The names of the properties should be
 * defined as class constants, for standardization purposes. It is recommended
 * that the names of these constants start with the string "PROPERTY_".
 *
 *	To create your own type of learning object, you should follow these steps:
 *	- Decide on a name for the type, e.g. "MyType".
 *	- Create a new subdirectory in /repository/lib/learning_object. For
 *	  "MyType", it would be called "my_type".
 *	- Create two files in that subdirectory:
 *	  - The properties file (e.g. "my_type.properties") is a plain text list
 *	    of the names of all the properties of your type, one name per line.
 *	    This file may be omitted if your type does not require additional
 *	    properties.
 *	  - The class file (e.g. "my_type.class.php") is a PHP class that may
 *	    provide specific methods for the type. Even if the type does not
 *	    require additional methods, you must still define the class. Take
 *	    a look at the types that are already defined for examples.
 *	- The data manager will now automagically be aware of the type. All that's
 *	  left for you to do is create the physical storage for the type. This
 *	  will heavily depend on the type of data manager you are using. As MySQL
 *	  is the default, you will probably have to create a table named after the
 *	  type you are defining. This table should contain a numeric "id" column,
 *	  as well as columns for all the properties in the properties file. You do
 *	  not need columns for the default properties! These are stored elsewhere.
 *	When you've completed these steps, you should be able to instantiate the
 *	class and manipulate the objects at will.
 *
 *	@author Tim De Pauw
 * @package repository.learningobject
==============================================================================
 */

class LearningObject
{
	const PROPERTY_ID = 'id';
	const PROPERTY_TYPE = 'type';
	const PROPERTY_OWNER_ID = 'owner';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_PARENT_ID = 'parent';
	const PROPERTY_DISPLAY_ORDER_INDEX = 'display_order';
	const PROPERTY_CREATION_DATE = 'created';
	const PROPERTY_MODIFICATION_DATE = 'modified';

	/**
	 * Numeric identifier of the learning object.
	 */
	private $id;

	/**
	 * Default properties of the learning object, stored in an associative
	 * array.
	 */
	private $defaultProperties;

	/**
	 * Additional properties specific to this type of learning object, stored
	 * in an associative array.
	 */
	private $additionalProperties;
	
	/**
	 * Learning objects attached to this learning object.
	 */
	private $attachments;

	/**
	 * Creates a new learning object.
	 * @param int $id The numeric ID of the learning object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the learning
	 *                                 object. Associative array.
	 * @param array $additionalProperties The properties specific for this
	 *                                    type of learning object.
	 *                                    Associative array.
	 */
	function LearningObject($id = 0, $defaultProperties = array (), $additionalProperties = array ())
	{
		$this->id = $id;
		$this->defaultProperties = $defaultProperties;
		$this->additionalProperties = $additionalProperties;
	}

	/**
	 * Returns the ID of this learning object.
	 * @return int The ID.
	 */
	function get_id()
	{
		return $this->id;
	}

	/**
	 * Returns a string representation of the type of this learning object.
	 * @return string The type.
	 */
	function get_type()
	{
		return RepositoryDataManager :: class_to_type(get_class($this));
	}

	/**
	 * Returns the ID of this learning object's owner.
	 * @return int The ID.
	 */
	function get_owner_id()
	{
		return $this->get_default_property(self :: PROPERTY_OWNER_ID);
	}

	/**
	 * Returns the title of this learning object.
	 * @return string The title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Returns the description of this learning object.
	 * @return string The description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Returns the numeric identifier of the learning object's parent learning
	 * object.
	 * @return int The identifier.
	 */
	function get_parent_id()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT_ID);
	}

	/**
	 * Returns the display order index of the learning object among its
	 * siblings.
	 * @return int The display order index.
	 */
	function get_display_order_index()
	{
		return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER_INDEX);
	}

	/**
	 * Returns the date when this learning object was created, as returned
	 * by PHP's time() function.
	 * @return int The creation date.
	 */
	function get_creation_date()
	{
		return $this->get_default_property(self :: PROPERTY_CREATION_DATE);
	}

	/**
	 * Returns the date when this learning object was last modified, as
	 * returned by PHP's time() function.
	 * @return int The modification time.
	 */
	function get_modification_date()
	{
		return $this->get_default_property(self :: PROPERTY_MODIFICATION_DATE);
	}
	
	/**
	 * Returns the learning objects attached to this learning object.
	 * @return array The learning objects.
	 */
	function get_attached_learning_objects ()
	{
		if (!is_array($this->attachments))
		{
			$dm = RepositoryDataManager :: get_instance();
			$this->attachments = $dm->retrieve_attached_learning_objects($this->get_id());
		}
		return $this->attachments;
	}

	/**
	 * Sets the ID of this learning object.
	 * @param int $id The ID.
	 */
	function set_id($id)
	{
		$this->id = $id;
	}

	/**
	 * Sets the ID of this learning object's owner.
	 * @param int $id The ID.
	 */
	function set_owner_id($owner)
	{
		$this->set_default_property(self :: PROPERTY_OWNER_ID, $owner);
	}

	/**
	 * Sets the title of this learning object.
	 * @param string $title The title.
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}

	/**
	 * Sets the description of this learning object.
	 * @param string $description The description.
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}

	/**
	 * Sets the ID of this learning object's parent learning object.
	 * @param int $parent The ID.
	 */
	function set_parent_id($parent)
	{
		$this->set_default_property(self :: PROPERTY_PARENT_ID, $parent);
	}

	/**
	 * Sets the display order index of the learning object among its siblings.
	 * @param int $index The index.
	 */
	function set_display_order_index($index)
	{
		$this->set_default_property(self :: PROPERTY_DISPLAY_ORDER_INDEX, $index);
	}

	/**
	 * Sets the date when this learning object was created.
	 * @param int $created The creation date, as returned by time().
	 */
	function set_creation_date($created)
	{
		$this->set_default_property(self :: PROPERTY_CREATION_DATE, $created);
	}

	/**
	 * Sets the date when this learning object was modified.
	 * @param int $modified The modification date, as returned by time().
	 */
	function set_modification_date($modified)
	{
		$this->set_default_property(self :: PROPERTY_MODIFICATION_DATE, $modified);
	}

	/**
	 * Returns whether or not this learning object is extended, i.e. whether
	 * its type defines additional properties.
	 * @return boolean True if the learning object is extended, false
	 *                 otherwise.
	 */
	function is_extended()
	{
		return RepositoryDataManager :: get_instance()->is_extended_type($this->get_type());
	}

	/**
	 * Determines whether this learning object is ordered, i.e. whether its
	 * order within its parent learning object is fixed. The order is stored
	 * in the display order index property, which is automatically maintained
	 * by the learning object class.
	 * @return boolean True if the object is ordered, false otherwise.
	 */
	function is_ordered()
	{
		return false;
	}
	
	/**
	 * Attaches the learning object with the given ID to this learning object.
	 * @param int $id The ID of the learning object to attach.
	 */
	function attach_learning_object ($id)
	{
		$dm = RepositoryDataManager :: get_instance();
		return $dm->attach_learning_object($this->get_id(), $id);
	}

	/**
	 * Removes the learning object with the given ID from this learning
	 * object's attachment list.
	 * @param int $id The ID of the learning object to remove from the
	 *                attachment list.
	 * @return boolean True if the attachment was removed, false if it did not
	 *                 exist.
	 */
	function detach_learning_object ($id)
	{
		$dm = RepositoryDataManager :: get_instance();
		return $dm->detach_learning_object($this->get_id(), $id);
	}

	/**
	 * Gets a default property of this learning object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Sets a default property of this learning object by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Gets an additional (type-specific) property of this learning object by
	 * name.
	 * @param string $name The name of the property.
	 */
	function get_additional_property($name)
	{
		return $this->additionalProperties[$name];
	}

	/**
	 * Sets an additional (type-specific) property of this learning object by
	 * name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_additional_property($name, $value)
	{
		$this->additionalProperties[$name] = $value;
	}

	/**
	 * Gets the default properties of this learning object.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Gets the additional (type-specific) properties of this learning
	 * object.
	 * @return array An associative array containing the properties.
	 */
	function get_additional_properties()
	{
		return $this->additionalProperties;
	}

	/**
	 * Instructs the data manager to create the learning object, making it
	 * persistent. Also assigns a unique ID to the learning object and sets
	 * the learning object's creation date to the current time.
	 * @return boolean True if creation succeeded, false otherwise.
	 */
	function create()
	{
		$now = time();
		$this->set_creation_date($now);
		$this->set_modification_date($now);
		$dm = RepositoryDataManager :: get_instance();
		$id = $dm->get_next_learning_object_id();
		$this->set_id($id);
		return $dm->create_learning_object($this);
	}

	/**
	 * Instructs the data manager to update the learning object, making any
	 * modifications permanent. Also sets the learning object's modification
	 * date to the current time if the update is a true update. A true update
	 * is an update that implicates a change to a property that affects the
	 * learning object itself; changing the learning object's category, for
	 * instance, should not change the last modification date.
	 * @param boolean $trueUpdate True if the update is a true update
	 *                            (default), false otherwise.
	 * @return boolean True if the update succeeded, false otherwise.
	 */
	function update($trueUpdate = true)
	{
		if ($trueUpdate)
		{
			$this->set_modification_date(time());
		}
		return RepositoryDataManager :: get_instance()->update_learning_object($this);
	}

	/**
	 * Instructs the data manager to delete the learning object.
	 * @return boolean True if deletion succeeded, false otherwise.
	 */
	function delete()
	{
		return RepositoryDataManager :: get_instance()->delete_learning_object($this);
	}

	/**
	 * Retrieves this learning object's ancestors.
	 * @return array The ancestors, all learning objects.
	 */
	function get_ancestors()
	{
		$ancestors = array ();
		$aid = $this->get_parent_id();
		while ($aid > 0)
		{
			$ancestor = RepositoryDataManager :: get_instance()->retrieve_learning_object($aid);
			$ancestors[] = $ancestor;
			$aid = $ancestor->get_parent_id();
		}
		return $ancestors;
	}

	/**
	 * Checks if the given ID is the ID of one of this learning object's
	 * ancestors.
	 * @return boolean True if the ID belongs to an ancestor, false otherwise.
	 */
	function has_ancestor($ancestor)
	{
		$aid = $this->get_parent_id();
		while ($aid > 0)
		{
			if ($aid == $ancestor)
			{
				return true;
			}
			$ancestor = RepositoryDataManager :: get_instance()->retrieve_learning_object($aid);
			$aid = $ancestor->get_parent_id();
		}
		return false;
	}

	/**
	 * Determines whether this learning object may be moved to the learning
	 * object with the given ID.
	 * @param int $target The ID of the target learning object.
	 * @return boolean True if the move is allowed, false otherwise.
	 */
	function move_allowed($target)
	{
		return false;
	}

	// XXX: Keep this around? Override? Make useful?
	function __tostring()
	{
		return get_class($this).'#'.$this->get_id().' ('.$this->get_title().')';
	}

	/**
	 * Determines whether this learning object type supports attachments, i.e.
	 * whether other learning objects may be attached to it.
	 * @return boolean True if attachments are supported, false otherwise.
	 */
	static function supports_attachments()
	{
		return false;
	}
	
	/**
	 * Get the default properties of all learning objects.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_OWNER_ID, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_PARENT_ID, self :: PROPERTY_CREATION_DATE, self :: PROPERTY_MODIFICATION_DATE);
	}

	/**
	 * Get all properties of this type of learning object that should be taken
	 * into account to calculate the used disk space.
	 * @return mixed The property names. Either a string, an array of strings,
	 *               or null if no properties affect disk quota.
	 */
	static function get_disk_space_properties()
	{
		return null;
	}
}
?>