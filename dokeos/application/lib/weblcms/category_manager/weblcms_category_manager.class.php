<?php
/**
 * @package application.lib.profiler
 */
require_once Path :: get_application_library_path(). 'category_manager/category_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_data_manager.class.php';
require_once dirname(__FILE__) . '/course_category.class.php';

class WeblcmsCategoryManager extends CategoryManager
{
	function WeblcmsCategoryManager($parent)
	{
		$trail = new BreadcrumbTrail();
        if($parent->get_user()->is_platform_admin())
			$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb($parent->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('Courses')));
		$trail->add(new Breadcrumb($parent->get_url(), Translation :: get('ManageCategories')));
		parent :: __construct($parent, $trail);
	}

	function get_category()
	{
		return new CourseCategory();
	}

	function get_category_form()
	{
		return new WeblcmsCategoryForm();
	}

	function count_categories($condition)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->count_categories($condition);
	}

	function retrieve_categories($condition, $offset, $count, $order_property)
	{
		$wdm = WeblcmsDataManager :: get_instance();
		return $wdm->retrieve_categories($condition, $offset, $count, $order_property);
	}

	function get_next_category_display_order($parent_id)
	{
		$wdm = WeblcmsDataManager :: get_instance();

        $condition = new EqualityCondition(CourseCategory :: PROPERTY_PARENT, $parent_id);
        $sort = $wdm->retrieve_max_sort_value(CourseCategory :: get_table_name(), CourseCategory :: PROPERTY_DISPLAY_ORDER, $condition);

		return $sort + 1;
	}
}
?>