<?php
/**
 * @package application.weblcms.tool.exercise.component.exercise_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';
/**
 * This class represents a data provider for a publication candidate table
 */
class WikiPublicationTableDataProvider extends ObjectTableDataProvider
{
	/**
	 * The user id of the current active user.
	 */
	private $owner;
	/**
	 * The possible types of learning objects which can be selected.
	 */
	private $types;
	/**
	 * The search query, or null if none.
	 */
	private $query;
	
	private $parent;
	/**
	 * Constructor.
	 * @param int $owner The user id of the current active user.
	 * @param array $types The possible types of learning objects which can be
	 * selected.
	 * @param string $query The search query.
	 */
    function WikiPublicationTableDataProvider($parent, $owner, $types, $query = null)
    {
    	$this->types = $types;
    	$this->owner = $owner;
    	$this->query = $query;
    	$this->parent = $parent;
    }
	/*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
    	$order_property = $this->get_order_property($order_property);
    	$order_direction = $this->get_order_direction($order_direction);
    	$dm = RepositoryDataManager :: get_instance();       
    	return $this->get_publications($offset, $count, $order_property, $order_direction);
    }
    
    function get_publications($from, $count, $column, $direction)
    {        
    	$datamanager = WeblcmsDataManager :: get_instance();
		$tool_condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'wiki');
		$condition = $tool_condition;
		$lo_condition = $this->get_condition();
		if($this->parent->is_allowed(EDIT_RIGHT))
		{
            //is allowed resets the user id and course groups
			$user_id = null;
			$course_groups = null;
            /*$user_id = $this->parent->get_user_id();
			$course_groups = $this->parent->get_course_groups();*/
		}
		else
		{
			$user_id = $this->parent->get_user_id();
			$course_groups = $this->parent->get_course_groups();
		}
		$course = $this->parent->get_course_id();		
    	$publications = $datamanager->retrieve_learning_object_publications($course, null, $user_id, $course_groups, $condition, false, array(), array(), 0, -1, null, $lo_condition);
		$visible_publications = array ();
		while ($publication = $publications->next_result())
		{
			// If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
			if (!$publication->is_visible_for_target_users() && !($this->parent->is_allowed(DELETE_RIGHT) || $this->parent->is_allowed(EDIT_RIGHT)))
			{
				continue;
			}
			$visible_publications[] = $publication;
		}        
		$publications = $visible_publications;
		return $publications;
    }
	/*
	 * Inherited
	 */
    function get_object_count()
    {
    	//$dm = RepositoryDataManager :: get_instance();
    	//return $dm->count_learning_objects(null, $this->get_condition());
    	return count($this->get_publications());
    }
	/**
	 * Gets the condition by which the learning objects should be selected.
	 * @return Condition The condition.
	 */
    function get_condition()
    {
    	$owner = $this->owner;
    	
    	$conds = array();
    	//$conds[] = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $owner->get_id());
    	$type_cond = array();
    	$types = $this->types;
    	foreach ($types as $type)
    	{
    		$type_cond[] = new EqualityCondition(LearningObject :: PROPERTY_TYPE, $type);
    	}
    	$conds[] = new OrCondition($type_cond);
		$c = DokeosUtilities :: query_to_condition($this->query);
		if (!is_null($c))
		{
			$conds[] = $c;
		}
    	return new AndCondition($conds);
    }
}
?>