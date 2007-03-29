<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../weblcms.class.php';
require_once dirname(__FILE__).'/../weblcmscomponent.class.php';
require_once dirname(__FILE__).'/../../course/coursecategorymenu.class.php';
require_once dirname(__FILE__).'/coursebrowser/coursebrowsertable.class.php';
/**
 * Weblcms component which allows the user to manage his or her course subscriptions
 */
class WeblcmsSubscribeComponent extends WeblcmsComponent
{
	private $category;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$this->category = $_GET[Weblcms :: PARAM_COURSE_CATEGORY_ID];
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('CourseSubscribe'));
		
		$menu = $this->get_menu_html();
		$output = $this->get_course_html();
		
		$this->display_header($breadcrumbs);
		echo $menu;
		echo $output;
		$this->display_footer();
	}
	
	function get_course_html()
	{
		$condition = null;
		if (isset($this->category))
		{
			$condition = new EqualityCondition(Course :: PROPERTY_CATEGORY_CODE, $this->category);
		}
		
		$table = new CourseBrowserTable($this, null, null, $condition);
		
		$html = array();
		$html[] = '<div style="float: right; width: 80%;">';
		$html[] = $table->as_html();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}
	
	function get_menu_html()
	{
		$temp_replacement = '__CATEGORY_ID__';
		$url_format = $this->get_url(array (Weblcms :: PARAM_ACTION => Weblcms :: ACTION_MANAGER_SUBSCRIBE, Weblcms :: PARAM_COURSE_CATEGORY_ID => $temp_replacement));
		$url_format = str_replace($temp_replacement, '%s', $url_format);
		$category_menu = new CourseCategoryMenu($this->category, $url_format);
		
		$html = array();
		$html[] = '<div style="float: left; width: 20%;">';
		$html[] = $category_menu->render_as_tree();
		$html[] = '</div>';
		
		return implode($html, "\n");
	}
}
?>