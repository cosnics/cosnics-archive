<?php
require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
require_once dirname(__FILE__).'/../../repository/lib/condition/equalitycondition.class.php';
/**
 * This class provides a navigation menu to allow a user to browse through his
 * categories of learning objects
 */
class CategoryMenu extends HTML_Menu
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
	 * Creates a new category navigation menu.
	 * @param int $owner The ID of the owner of the categories to provide in
	 * this menu.
	 * @param int $current_category The ID of the current category in the menu.
	 * @param string $url_format The format to use for the URL of a category.
	 *                           Passed to sprintf(). Defaults to the string
	 *                           "?category=%s".
	 */
	public function CategoryMenu($owner, $current_category, $url_format = '?category=%s')
	{
		$this->owner = $owner;
		$this->urlFmt = $url_format;
		$menu = $this->get_menu_items();
		parent :: HTML_Menu($menu);
		$this->forceCurrentUrl($this->get_category_url($current_category));
	}
	/**
	 * Get the menu items.
	 * @return array An array with all menu items. The structure of this array
	 * is the structure needed by PEAR::HTML_Menu on which this class is based.
	 */
	private function get_menu_items()
	{
		$condition = new EqualityCondition('owner', $this->owner);
		$datamanager = RepositoryDataManager :: get_instance();
		$objects = $datamanager->retrieve_learning_objects('category', $condition);
		$categories = array ();
		foreach ($objects as $index => $category)
		{
			$categories[$category->get_category_id()][] = $category;
		}
		return $this->get_sub_menu_items($categories, 0);
	}
	/**
	 * Get the menu items.
	 * @param array $categories An array of all categories to use in this menu
	 * @param int $parent The parent category id
	 * @return array An array with all menu items. The structure of this array
	 * is the structure needed by PEAR::HTML_Menu on which this class is based.
	 */
	private function get_sub_menu_items(& $categories, $parent)
	{
		$sub_tree = array ();
		foreach ($categories[$parent] as $index => $category)
		{
			$menu_item['title'] = $category->get_title();
			$menu_item['url'] = $this->get_category_url($category->get_id());
			$menu_item['id'] = $category->get_id();
			if (count($categories[$category->get_id()]) > 0)
			{
				$menu_item['sub'] = $this->get_sub_menu_items($categories, $category->get_id());
			}
			$sub_tree[$category->get_id()] = $menu_item;
		}
		return $sub_tree;
	}
	private function get_category_url ($category)
	{
		return sprintf($this->urlFmt, $category);
	}
	/**
	 * Get the breadcrumbs which lead to the current category
	 * @return array The array with the breadcrumbs
	 */
	public function get_breadcrumbs()
	{
		$renderer =& new HTML_Menu_ArrayRenderer();
		$this->render($renderer,'urhere');
		$breadcrumbs = $renderer->toArray();
		//$current_location = array_pop($breadcrumbs);
		foreach($breadcrumbs as $index => $breadcrumb)
		{
			$interbredcrump[] = array ("url" => $breadcrumb['url'], "name" => $breadcrumb['title']);
		}
		return $interbredcrump;
	}
}