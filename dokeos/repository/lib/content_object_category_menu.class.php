<?php
/**
 * $Id: learning_object_category_menu.class.php 23130 2009-09-25 12:40:53Z vanpouckesven $
 * @package repository
 */
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once Path :: get_repository_path(). 'lib/content_object.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path() . 'html/menu/tree_menu_renderer.class.php';
require_once Path :: get_library_path() . 'html/menu/options_menu_renderer.class.php';
require_once dirname(__FILE__) . '/category_manager/repository_category.class.php';
/**
 * This class provides a navigation menu to allow a user to browse through his
 * categories of learning objects.
 * @author Bart Mollet
 */
class ContentObjectCategoryMenu extends HTML_Menu
{
	/**
	 * The owner of the categories
	 */
	private $owner;
	/**
	 * The string passed to sprintf() to format category URLs
	 */
	private $urlFmt;
	/**
	 * The array renderer used to determine the breadcrumbs.
	 */
	private $array_renderer;
	
	private $data_manager;
	/**
	 * Creates a new category navigation menu.
	 * @param int $owner The ID of the owner of the categories to provide in
	 * this menu.
	 * @param int $current_category The ID of the current category in the menu.
	 * @param string $url_format The format to use for the URL of a category.
	 *                           Passed to sprintf(). Defaults to the string
	 *                           "?category=%s".
	 * @param array $extra_items An array of extra tree items, added to the
	 *                           root.
	 */
	function ContentObjectCategoryMenu($owner, $current_category, $url_format = '?category=%s', $extra_items = array())
	{
		$this->owner = $owner;
		$this->urlFmt = $url_format;
		$this->data_manager = RepositoryDataManager :: get_instance();
		$menu = $this->get_menu_items($extra_items);
		parent :: __construct($menu);
		$this->array_renderer = new HTML_Menu_ArrayRenderer();
		$this->forceCurrentUrl($this->get_category_url($current_category));
	}
	/**
	 * Returns the menu items.
	 * @param array $extra_items An array of extra tree items, added to the
	 *                           root.
	 * @return array An array with all menu items. The structure of this array
	 *               is the structure needed by PEAR::HTML_Menu, on which this
	 *               class is based.
	 */
	private function get_menu_items($extra_items)
	{
		$menu = array();
		$menu_item = array();
		
		$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, 0);
		$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->owner);
		$condition = new AndCondition($conditions);
		$count = $this->data_manager->count_content_objects(null, $condition);
		
		$menu_item['title'] = Translation :: get('MyRepository') . ' (' . $count . ')';
		$menu_item['url'] = $this->get_category_url(0);
		$sub_menu_items = $this->get_sub_menu_items(0);
		if(count($sub_menu_items) > 0)
		{
			$menu_item['sub'] = $sub_menu_items;
		}
		$menu_item['class'] = 'category';
		$menu_item[OptionsMenuRenderer :: KEY_ID] = 0;
		$menu[0] = $menu_item;
		if (count($extra_items))
        {
        	$menu = array_merge($menu, $extra_items);
        }
		

		return $menu;
	}
	/**
	 * Returns the items of the sub menu.
	 * @param array $categories The categories to include in this menu.
	 * @param int $parent The parent category ID.
	 * @return array An array with all menu items. The structure of this array
	 *               is the structure needed by PEAR::HTML_Menu, on which this
	 *               class is based.
	 */
	private function get_sub_menu_items($parent)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_USER_ID, $this->owner);
		$conditions[] = new EqualityCondition(RepositoryCategory :: PROPERTY_PARENT, $parent);
		$condition = new AndCondition($conditions);
		
		$objects = $this->data_manager->retrieve_categories($condition);
		$categories = array ();
		while ($category = $objects->next_result())
		{
			$conditions = array();
			$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $category->get_id());
			$conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->owner);
			$condition = new AndCondition($conditions);
			
			$count = $this->data_manager->count_content_objects(null, $condition);
		
			$menu_item = array();
			$menu_item['title'] = $category->get_name() . ' (' . $count . ')';
			$menu_item['url'] = $this->get_category_url($category->get_id());
			$sub_menu_items = $this->get_sub_menu_items($category->get_id());
			if(count($sub_menu_items) > 0)
			{
				$menu_item['sub'] = $sub_menu_items;
			}
			$menu_item['class'] = 'category';
			$menu_item[OptionsMenuRenderer :: KEY_ID] = $category->get_id();
			$categories[$category->get_id()] = $menu_item;
		}
		return $categories;
	}
	/**
	 * Gets the URL of a given category
	 * @param int $category The id of the category
	 * @return string The requested URL
	 */
	private function get_category_url ($category)
	{
		// TODO: Put another class in charge of the htmlentities() invocation
		return htmlentities(sprintf($this->urlFmt, $category));
	}
	/**
	 * Get the breadcrumbs which lead to the current category.
	 * @return array The breadcrumbs.
	 */
	function get_breadcrumbs()
	{
        $trail = new BreadcrumbTrail(false);
        $this->render($this->array_renderer, 'urhere');
        $breadcrumbs = $this->array_renderer->toArray();
        foreach ($breadcrumbs as $crumb)
        {
            $str = Translation :: get('MyRepository');
        	if(substr($crumb['title'], 0, strlen($str)) == $str) continue;
            $trail->add(new Breadcrumb($crumb['url'], substr($crumb['title'], 0, strpos($crumb['title'], '('))));
           
        }
        return $trail;
//
//		$this->render($this->array_renderer, 'urhere');
//		$breadcrumbs = $this->array_renderer->toArray();
//		foreach ($breadcrumbs as $crumb)
//		{
//			$crumb['name'] = $crumb['title'];
//			unset($crumb['title']);
//		}
//		return $breadcrumbs;
	}
	/**
	 * Renders the menu as a tree
	 * @return string The HTML formatted tree
	 */
	function render_as_tree()
	{
		$renderer = new TreeMenuRenderer();
		$this->render($renderer, 'sitemap');
		return $renderer->toHTML();
	}
}