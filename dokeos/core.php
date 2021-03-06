<?php
/**
 * $Id: application.class.php 12019 2007-04-13 12:57:10Z Scara84 $
 * This script will load the requested core application and call its run() function.
 */
require_once dirname(__FILE__).'/common/global.inc.php';
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';
require_once Path :: get_library_path() . 'core_application.class.php';

$application_name = Request :: get('application');
$this_section = $application_name;

// If application path doesn't exist, block the user
if(!CoreApplication :: is_active($application_name))
{
	Display :: not_allowed();
}

//require_once Path ::get_application_path().'lib/weblcms/tool/assessment/assessment_tool.class.php';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

if (!Authentication :: is_valid() && !(Request :: get('application') == 'user' && (Request :: get(Application :: PARAM_ACTION) == UserManager :: ACTION_REGISTER_USER || Request :: get(Application :: PARAM_ACTION) == UserManager :: ACTION_RESET_PASSWORD)) && !(Request :: get('application') == 'admin' && (Request :: get(Application :: PARAM_ACTION) == AdminManager :: ACTION_WHOIS_ONLINE)))
{
	Display :: not_allowed();
}

// Load the current user for every application but the UserManager itself
if (!(Request :: get('application') == 'user'))
{
	$user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
}
else
{
	if(!Session :: get_user_id())
	{
		$user = null;
	}
	else
	{
		$user = Session :: get_user_id();
	}
}

// Load & run the application
try
{
	$application = CoreApplication :: factory($application_name, $user);
	$application->set_parameter('application', $application_name);
	$application->run();
}
catch(Exception $exception)
{
	$application->display_header();
	Display :: error_message($exception->getMessage());
	$application->display_footer();
}
?>