<?php
/**
 * @package repository.usertable
 */
/**
 * todo: add comment
 */
interface ClassgroupTableDataProvider
{
    function get_classgroups($group = null, $category = null, $offset, $count, $order_property, $order_direction);

    function get_classgroup_count();
}
?>