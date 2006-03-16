<?php
require_once('../../claroline/inc/claro_init_global.inc.php');
require_once(api_get_library_path().'/formvalidator/FormValidator.class.php');
require_once('../lib/repositorydatamanager.class.php');
require_once('../lib/learningobject_form.class.php');
require_once('../lib/learningobject_display.class.php');
if( !api_get_user_id())
{
	api_not_allowed();
}
if( isset($_GET['id']))
{
	$datamanager = RepositoryDataManager::get_instance();
	$object = $datamanager->retrieve_learning_object($_GET['id']);
	$display = LearningObjectDisplay::factory($object);
	// Create a navigation menu to browse through the categories
	$current_category_id = $object->get_category_id();
	$menu = new CategoryMenu(api_get_user_id(),$current_category_id,'index.php?category=%s');
	$interbredcrump = $menu->get_breadcrumbs();
	$tool_name = get_lang('Rights').': '.$object->get_title();
	Display::display_header($tool_name);
	api_display_tool_title($tool_name);
	echo $display->get_full_html();
	//TODO: implement roles & rights stuff
	echo '<p><b>TODO: Here you can edit the access rights of the selected object...</b></p>';
	Display::display_footer();
}
?>