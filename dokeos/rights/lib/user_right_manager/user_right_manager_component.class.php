<?php
require_once Path :: get_common_path() . 'sub_manager_component.class.php';

class UserRightManagerComponent extends SubManagerComponent
{

    function retrieve_locations($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_locations($condition, $offset, $count, $order_property);
    }

    function count_locations($conditions = null)
    {
        return $this->get_parent()->count_locations($conditions);
    }

    function retrieve_location($location_id)
    {
        return $this->get_parent()->retrieve_location($location_id);
    }

    function retrieve_user_right_location($right_id, $user_id, $location_id)
    {
        return $this->get_parent()->retrieve_user_right_location($right_id, $user_id, $location_id);
    }

    function retrieve_users($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->get_parent()->retrieve_users($condition, $offset, $count, $order_property);
    }

    function count_users($conditions = null)
    {
        return $this->get_parent()->count_users($conditions);
    }
}
?>