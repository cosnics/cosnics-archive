<?php
/**
 * @package application.lib.profiler
 */
require_once Path :: get_application_library_path() . 'category_manager/category_manager.class.php';
require_once dirname(__FILE__) . '/../profiler_data_manager.class.php';
require_once dirname(__FILE__) . '/profiler_category.class.php';

class ProfilerCategoryManager extends CategoryManager
{
    private $trail;

    function ProfilerCategoryManager($parent, $trail)
    {
        parent :: __construct($parent, $trail);
        $this->trail = $trail;
    }

    function get_category()
    {
        return new ProfilerCategory();
    }

    function count_categories($condition)
    {
        $wdm = ProfilerDataManager :: get_instance();
        return $wdm->count_categories($condition);
    }

    function retrieve_categories($condition, $offset, $count, $order_property)
    {
        $wdm = ProfilerDataManager :: get_instance();
        return $wdm->retrieve_categories($condition, $offset, $count, $order_property);
    }

    function get_next_category_display_order($parent_id)
    {
        $wdm = ProfilerDataManager :: get_instance();
        return $wdm->select_next_category_display_order($parent_id);
    }

    function get_breadcrumb_trail()
    {
        return $this->trail;
    }
}
?>