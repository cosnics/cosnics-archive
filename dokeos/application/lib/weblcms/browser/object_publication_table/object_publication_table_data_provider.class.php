<?php
/**
 * @package application.weblcms.tool.assessment.component.assessment_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
/**
 * This class represents a data provider for a publication candidate table
 */
class ObjectPublicationTableDataProvider extends ObjectTableDataProvider
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
	private $condition;
	
	private $parent;
	/**
	 * Constructor.
	 * @param int $owner The user id of the current active user.
	 * @param array $types The possible types of learning objects which can be
	 * selected.
	 * @param string $query The search query.
	 */
    function ObjectPublicationTableDataProvider($parent, $owner, $types, $condition = null)
    {
    	$this->types = $types;
    	$this->owner = $owner;
    	$this->condition = $condition;
    	$this->parent = $parent;
    }
	/*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null, $order_direction = null)
    {
    	$order_property = $this->get_order_property($order_property);
    	$order_direction = $this->get_order_direction($order_direction);
    	return $this->get_publications($offset, $count, $order_property, $order_direction);
    }
    
    function get_publications($from, $count, $column, $direction)
    {
    	$datamanager = WeblcmsDataManager :: get_instance();
		$publications = $datamanager->retrieve_learning_object_publications_new($this->get_conditions());
		return $publications;
    }
    
	/*
	 * Inherited
	 */
    function get_object_count()
    {
    	$datamanager = WeblcmsDataManager :: get_instance();
		$publications = $datamanager->count_learning_object_publications_new($this->get_conditions());
		return $publications;
    }
    
    function get_conditions()
    {
    	$datamanager = WeblcmsDataManager :: get_instance();
		if($this->parent->is_allowed(EDIT_RIGHT))
		{
			$user_id = array();
			$course_groups = array();
		}
		else
		{
			$user_id = $this->parent->get_user_id();
			$course_groups = $this->parent->get_course_groups();
		}
		$course = $this->parent->get_course_id();
		
		if ($this->parent->get_parameter(WeblcmsManager :: PARAM_CATEGORY))
    		$category = $this->parent->get_parameter(WeblcmsManager :: PARAM_CATEGORY);
    	else
    		$category = 0;
		
		$conditions = array();
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_COURSE_ID, $course);
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, $this->parent->get_tool_id());
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_CATEGORY_ID, $category);
		
		$access = array();
		if (!empty($user_id))
		{
			$access[] = new InCondition('user', $user_id, $datamanager->get_database()->get_alias('learning_object_publication_user'));
		}
		
		if(!empty($course_groups))
		{
			$access[] = new InCondition('course_group_id', $course_groups, $datamanager->get_database()->get_alias('learning_object_publication_course_group'));
		}

		$conditions[] = new OrCondition($access);
		
		$subselect_conditions = array();
		$subselect_conditions[] = $this->get_subselect_condition();
		$subselect_condition = new AndCondition($subselect_conditions);
		$conditions[] = new SubselectCondition(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID, LearningObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(LearningObject :: get_table_name()), $subselect_condition);
		
		if($this->condition)
			$conditions[] = $this->condition;
			
		$condition = new AndCondition($conditions);
		
		return $condition;
    }
    
	/**
	 * Gets the condition by which the learning objects should be selected.
	 * @return Condition The condition.
	 */
    function get_subselect_condition()
    {
    	$type_cond = array();
    	$types = $this->types;
    	foreach ($types as $type)
    	{
    		$type_cond[] = new EqualityCondition(LearningObject :: PROPERTY_TYPE, $type);
    	}
    	$condition = new OrCondition($type_cond);
    	
    	return $condition;
    }
}
?>