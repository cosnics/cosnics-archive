<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms_manager.class.php';
require_once dirname(__FILE__).'/../weblcms_manager_component.class.php';
require_once dirname(__FILE__).'/../../course/course_category_menu.class.php';
require_once dirname(__FILE__).'/admin_course_browser/admin_course_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
/**
 * Weblcms component which allows the the platform admin to browse the courses
 */
class WeblcmsManagerAdminCourseBrowserComponent extends WeblcmsManagerComponent
{
	private $category;
	private $action_bar;

	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		Header :: set_section('admin');

		$this->category = Request :: get(WeblcmsManager :: PARAM_COURSE_CATEGORY_ID);

		$trail = new BreadcrumbTrail();
        //$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_APPLICATION => 'weblcms')), Translation :: get('MyCourses')));
        if($this->get_user()->is_platform_admin())
			$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_ACTION => null)), Translation :: get('Courses')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('CourseList')));
        $trail->add_help('courses general');

        if($this->category)
        {
            $category = WeblcmsDataManager::get_instance()->retrieve_course_category($this->category);
            $trail->add(new Breadcrumb($this->get_url(), $category->get_name()));
        }

		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail, false, true);
			echo '<div class="clear"></div><br />';
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}

		$this->action_bar = $this->get_action_bar();
		$menu = $this->get_menu_html();
		$output = $this->get_course_html();

		$this->display_header($trail, false, true);
		echo '<div class="clear"></div>';
		echo '<br />' . $this->action_bar->as_html() . '<br />';
		echo $menu;
		echo $output;
		$this->display_footer();
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->set_search_url($this->get_url(array('category' => Request :: get('category'))));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array('category' => Request :: get('category'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		return $action_bar;
	}

	function get_course_html()
	{
		$table = new AdminCourseBrowserTable($this, null, $this->get_condition());

		$html = array();
		$html[] = '<div style="float: right; width: 80%;">';
		$html[] = $table->as_html();
		$html[] = '</div>';

		return implode($html, "\n");
	}

	function get_menu_html()
	{
		$extra_items = array ();
		if ($this->get_search_validate())
		{
			// $search_url = $this->get_url();
			$search_url = '#';
			$search = array ();
			$search['title'] = Translation :: get('SearchResults');
			$search['url'] = $search_url;
			$search['class'] = 'search_results';
			$extra_items[] = $search;
		}
		else
		{
			$search_url = null;
		}

		$temp_replacement = '__CATEGORY_ID__';
		$url_format = $this->get_url(array (Application :: PARAM_ACTION => WeblcmsManager :: ACTION_ADMIN_COURSE_BROWSER, WeblcmsManager :: PARAM_COURSE_CATEGORY_ID => $temp_replacement));
		$url_format = str_replace($temp_replacement, '%s', $url_format);
		$category_menu = new CourseCategoryMenu($this->category, $url_format, $extra_items);

		if (isset ($search_url))
		{
			$category_menu->forceCurrentUrl($search_url, true);
		}

		$html = array();
		$html[] = '<div style="float: left; width: 20%;">';
		$html[] = $category_menu->render_as_tree();
		$html[] = '</div>';

		return implode($html, "\n");
	}

	function get_condition()
	{
		$query = $this->action_bar->get_query();

		if (isset($query) && $query != '')
		{
			$conditions = array ();
			$conditions[] = new PatternMatchCondition(Course :: PROPERTY_NAME, '*'.$query.'*');
			$conditions[] = new PatternMatchCondition(Course :: PROPERTY_VISUAL, '*'.$query.'*');
			$conditions[] = new PatternMatchCondition(Course :: PROPERTY_LANGUAGE, '*'.$query.'*');

			$search_conditions = new OrCondition($conditions);
		}

		$condition = null;
		if (isset($this->category))
		{
			$condition = new EqualityCondition(Course :: PROPERTY_CATEGORY, $this->category);

			if (count($search_conditions))
			{
				$condition = new AndCondition($condition, $search_conditions);
			}
		}
		else
		{
			if (count($search_conditions))
			{
				$condition = $search_conditions;
			}
		}

		return $condition;
	}
}
?>