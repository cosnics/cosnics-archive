<?php
/**
 * @package application.lib.personal_messenger
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../../../common/global.inc.php';
require_once dirname(__FILE__).'/personal_messenger_data_manager.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_user_path(). 'lib/user.class.php';
require_once Path :: get_library_path().'condition/equality_condition.class.php';
require_once Path :: get_library_path().'condition/not_condition.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';

if (Authentication :: is_valid())
{
	$conditions = array ();

	if (Request :: get('query'))
	{
		$q = '*'.Request :: get('query').'*';
		$query_condition = new PatternMatchCondition(User :: PROPERTY_USERNAME, $q);

		if (isset ($query_condition))
		{
			$conditions[] = $query_condition;
		}
	}

	if (is_array(Request :: get('exclude')))
	{
		$c = array ();
		foreach (Request :: get('exclude') as $id)
		{
			$c[] = new EqualityCondition(User :: PROPERTY_USER_ID, $id);
		}
		$conditions[] = new NotCondition(new OrCondition($c));
	}

	if (Request :: get('query') || is_array(Request :: get('exclude')))
	{
		$condition = new AndCondition($conditions);
	}
	else
	{
		$condition = null;
	}

	$dm = UserDataManager :: get_instance();
	$objects = $dm->retrieve_users($condition);

	while ($lo = $objects->next_result())
	{
		$users[] =$lo;
	}

	$dm = GroupDataManager :: get_instance();
	$grs = $dm->retrieve_groups();
	while($group = $grs->next_result())
	{
		$groups[] = $group;
	}
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="iso-8859-1"?>', "\n", '<tree>', "\n";

dump_tree($users, $groups);

echo '</tree>';

function dump_tree($users, $groups)
{
	if (contains_results($users) || contains_results($groups))
	{
		echo '<node id="user" class="type_category unlinked" title="Users">', "\n";
		foreach ($users as $lo)
		{
			echo '<leaf id="user_'. $lo->get_id(). '" class="'. 'type type_user'. '" title="'. htmlentities($lo->get_username()). '" description="'. htmlentities($lo->get_firstname()) . ' ' . htmlentities($lo->get_lastname()) . '"/>'. "\n";
		}
		echo '</node>', "\n";

		echo '<node id="group" class="type_category unlinked" title="Groups">', "\n";
		foreach ($groups as $group)
		{
			echo '<leaf id="group_'. $group->get_id(). '" class="'. 'type type_group'. '" title="'. htmlentities($group->get_name()). '" description="'. htmlentities($group->get_name()) . '"/>'. "\n";
		}
		echo '</node>', "\n";
	}
}

function contains_results($objects)
{
	if (count($objects))
	{
		return true;
	}
	return false;
}
?>