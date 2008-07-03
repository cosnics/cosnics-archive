<?php
/**
 * @package repository
 */
 $this_section = 'home';
 
require_once dirname(__FILE__).'/../../common/global.inc.php';
require_once Path :: get_home_path() . 'lib/home_manager/home_manager.class.php';
require_once Path :: get_home_path() . 'lib/home_data_manager.class.php';

Translation :: set_application('home');
Theme :: set_application($this_section);

function unserialize_jquery($jquery)
{
	$block_data = explode('&', $jquery);
	$blocks = array();
	
	foreach($block_data as $block)
	{
		$block_split = explode('=', $block);
		$blocks[] = $block_split[1];
	}
	
	return $blocks;
}

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
	$user_id	= Session :: get_user_id();
	$column_data	= explode('_', $_POST['column']);
	$blocks			= unserialize_jquery($_POST['order']);
	
	/*
	 * TODO: Make this accept input from the jQuery script, should automatically add the correct block to the homepage
	 */	
	
	$block = new HomeBlock();
	$block->set_column($column_data[1]);
	$block->set_title('AjaxTest');
	$block->set_sort('1');
	$block->set_application('search_portal');
	$block->set_component('extra');
	$block->set_visibility('1');
	$block->set_user($user_id);
	
	$block->create();
	
	$application_class = Application :: application_to_class($block->get_application());
	
	if(!Application :: is_application($application))
	{
		$application_class .= 'Manager';
	}
	
	$usermgr = new UserManager($user_id);
	$user = $usermgr->get_user();
	
	$app = new $application_class($user);
	echo $app->render_block($block);
}
?>