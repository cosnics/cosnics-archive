<?php
/**
 * @package application.lib.profiler.publisher.publication_candidate_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_data_provider.class.php';
require_once Path :: get_repository_path(). 'lib/content_object.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';
/**
 * This class represents a data provider for a publication candidate table
 */
class ContentObjectTableDataProvider extends ObjectTableDataProvider
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
    function ContentObjectTableDataProvider($owner, $types, $query = null, $parent)
    {
        $this->types = $types;
        $this->owner = $owner;
        $this->query = $query;
        $this->parent = $parent;
    }
	/*
	 * Inherited
	 */
    function get_objects($offset, $count, $order_property = null)
    {
        $order_property = $this->get_order_property($order_property);

        $dm = RepositoryDataManager :: get_instance();

        return $dm->retrieve_content_objects(null, $this->get_condition(), $order_property, $offset, $count);
    }
	/*
	 * Inherited
	 */
    function get_object_count()
    {
        $dm = RepositoryDataManager :: get_instance();
        return $dm->count_content_objects(null, $this->get_condition());
    }
    /**
     * Gets the condition by which the learning objects should be selected.
     * @return Condition The condition.
     */
    function get_condition()
    {
        $owner = $this->owner;

        if(!Request :: get('SharedBrowser') == 1)
        {
            $category = Request :: get('category');
            $category = $category?$category:0;

            $conds = array();
            $conds[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $owner->get_id());
            $conds[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $category);
            $conds[] = new EqualityCondition(ContentObject :: PROPERTY_STATE, 0);
            $type_cond = array();
            $types = $this->types;
            foreach ($types as $type)
            {
                $type_cond[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type);
            }
            $conds[] = new OrCondition($type_cond);
            $c = DokeosUtilities :: query_to_condition($this->query);
            if (!is_null($c))
            {
                $conds[] = $c;
            }

            foreach($this->parent->get_excluded_objects() as $excluded)
            {
                $conds[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_ID, $excluded, ContentObject :: get_table_name()));
            }

            return new AndCondition($conds);
        }else
        {
            $query = $this->query;
			if(isset($query) && $query != '')
		    {
		        $or_conditions[] = new LikeCondition(ContentObject :: PROPERTY_TITLE, $query);
		        $or_conditions[] = new LikeCondition(ContentObject :: PROPERTY_DESCRIPTION, $query);
		        $conditions[] = new OrCondition($or_conditions);
		    }

		    $rdm = RightsDataManager :: get_instance();

		    $user = $this->owner;
		    $groups = $user->get_groups();
		    foreach($groups as $group)
		    {
		    	$group_ids[] = $group->get_id();
		    }

		    //retrieve all the rights
		    $reflect = new ReflectionClass(Application :: application_to_class(RepositoryManager :: APPLICATION_NAME) . 'Rights');
		    $rights_db = $reflect->getConstants();

		    foreach($rights_db as $right_id)
		    {
		        if($right_id != RepositoryRights :: VIEW_RIGHT && $right_id != RepositoryRights :: USE_RIGHT && $right_id != RepositoryRights :: REUSE_RIGHT)
		            continue;
		        $rights[] = $right_id;
		    }

			$location_ids = array();
		    $shared_content_objects = $rdm->retrieve_shared_content_objects_for_user($user->get_id(),$rights);

		    while($user_right_location = $shared_content_objects->next_result())
		    {
		        if(!in_array($user_right_location->get_location_id(), $location_ids))
		            $location_ids[] = $user_right_location->get_location_id();

		        $this->list[] = array('location_id' => $user_right_location->get_location_id(),'user' => $user_right_location->get_user_id(), 'right' => $user_right_location->get_right_id());
		    }

		   	$shared_content_objects = $rdm->retrieve_shared_content_objects_for_groups($group_ids,$rights);

		    while($group_right_location = $shared_content_objects->next_result())
		    {
		        if(!in_array($group_right_location->get_location_id(), $location_ids))
		            $location_ids[] = $group_right_location->get_location_id();

		        $this->list[] = array('location_id' => $group_right_location->get_location_id(),'group' => $group_right_location->get_group_id(), 'right' => $group_right_location->get_right_id());
		    }


		    if(count($location_ids) > 0)
		    {
		    	$location_cond = new InCondition('id',$location_ids);
		    	$locations = $rdm->retrieve_locations($location_cond);

		       	while($location = $locations->next_result())
		       	{
		          	$ids[] = $location->get_identifier();

				    foreach ($this->list as $key => $value)
				    {
				        if($value['location_id'] == $location->get_id())
				        {
				            $value['content_object'] = $location->get_identifier();
				            $this->list[$key] = $value;
				        }
				    }
				}

				if($ids)
				    $conditions[] = new InCondition('id',$ids, ContentObject :: get_table_name());


				if($conditions)
				    $condition = new AndCondition($conditions);
		    }

			if(!$condition)
			{
			    $condition = new EqualityCondition('id', -1, ContentObject :: get_table_name());
			}

			return $condition;
        }
    }
}
?>