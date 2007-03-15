<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
/**
==============================================================================
*	This is the group library for Dokeos.
*	Include/require it in your code to use its functionality.
*
*	@author various authors
*	@author Roan Embrechts (Vrije Universiteit Brussel), virtual courses support + some cleaning
*   @author Bart Mollet (HoGent), all functions in class GroupManager
*	@package dokeos.library
==============================================================================
*/
require_once ('database.lib.php');
require_once ('course.lib.php');
require_once ('tablesort.lib.php');
require_once ('fileManage.lib.php');
require_once ('fileUpload.lib.php');
/**
 * infinite
 */
define("INFINITE", "99999");
/**
 * No limit on the number of users in a group
 */
define("MEMBER_PER_GROUP_NO_LIMIT", "0");
/**
 * No limit on the number of groups per user
 */
define("GROUP_PER_MEMBER_NO_LIMIT", "0");
/**
 * The tools of a group can have 3 states
 * - not available
 * - public
 * - private
 */
define("TOOL_NOT_AVAILABLE", "0");
define("TOOL_PUBLIC", "1");
define("TOOL_PRIVATE", "2");
/**
 * Constants for the available group tools
 */
define("GROUP_TOOL_FORUM", "0");
define("GROUP_TOOL_DOCUMENTS", "1");
/**
 * Fixed id's for group categories
 * - VIRTUAL_COURSE_CATEGORY: in this category groups are created based on the
 *   virtual  course of a course
 * - DEFAULT_GROUP_CATEGORY: When group categories aren't available (platform-
 *   setting),  all groups are created in this 'dummy'-category
 */
define("VIRTUAL_COURSE_CATEGORY", 1);
define("DEFAULT_GROUP_CATEGORY", 2);
/**
 * This library contains some functions for group-management.
 * @author Bart Mollet
 * @package dokeos.library
 * @todo Add $course_code parameter to all functions. So this GroupManager can
 * be used outside a session.
 */
class GroupManager
{
	/**
	 * Get list of groups for current course.
	 * @param int $category The id of the category from which the groups are
	 * requested
	 * @param string $course_code Default is current course
	 * @return array An array with all information about the groups.
	 */
	function get_group_list($category = null, $course_code = null)
	{
		global $_uid;
		$course_db = '';
		if ($course_code != null)
		{
			$course_info = Database :: get_course_info_from_code($course_code);
			$course_db = $course_info['database'];
		}
		$table_group = Database :: get_course_table(GROUP_TABLE, $course_db);
		$table_user = Database :: get_main_table(MAIN_USER_TABLE);
		$table_course = Database :: get_main_table(MAIN_COURSE_TABLE);
		$table_group_user = Database :: get_course_table(GROUP_USER_TABLE, $course_db);
		$sql = "SELECT  g.id ,
														g.name ,
														g.description ,
														g.category_id,
														g.max_student maximum_number_of_members,
														g.forum_id ,
														g.forum_state,
														g.secret_directory,
														g.tutor_id id_tutor,
														g.self_registration_allowed,
														g.self_unregistration_allowed,
														tutor.user_id,
														tutor.lastname,
														tutor.firstname,
														tutor.username,
														tutor.email,
														ug.user_id is_member,
														COUNT(ug2.id) number_of_members,
														tutor.user_id user_id
														FROM ".$table_group." `g`
														LEFT JOIN  ".$table_user." `tutor`
														ON `tutor`.`user_id` = `g`.`tutor_id`
														LEFT JOIN ".$table_group_user." `ug`
														ON `ug`.`group_id` = `g`.`id` AND `ug`.`user_id` = '".$_uid."'
														LEFT JOIN ".$table_group_user." `ug2`
														ON `ug2`.`group_id` = `g`.`id`";
		if ($category != null)
			$sql .= " WHERE `g`.`category_id` = '".$category."' ";
		$sql .= " GROUP BY `g`.`id` ORDER BY UPPER(g.name)";
		$groupList = api_sql_query($sql,__FILE__,__LINE__);
		$groups = array ();
		while ($thisGroup = mysql_fetch_array($groupList))
		{
			if ($thisGroup['category_id'] == VIRTUAL_COURSE_CATEGORY)
			{
				$sql = "SELECT title FROM $table_course WHERE code = '".$thisGroup['name']."'";
				$obj = mysql_fetch_object(api_sql_query($sql,__FILE__,__LINE__));
				$thisGroup['name'] = $obj->title;
			}
			$groups[] = $thisGroup;
		}
		return $groups;
	}
	/**
	 * Get all categories
	 * @param string $course_code The cours (default = current course)
	 */
	function get_categories($course_code = null)
	{
		$course_db = '';
		if ($course_code != null)
		{
			$course_info = Database :: get_course_info_from_code($course_code);
			$course_db = $course_info['database'];
		}
		$table_group_cat = Database :: get_course_table(GROUP_CATEGORY_TABLE, $course_db);
		$sql = "SELECT * FROM $table_group_cat ORDER BY display_order";
		$res = api_sql_query($sql,__FILE__,__LINE__);
		$cats = array ();
		while ($cat = mysql_fetch_array($res))
		{
			$cats[] = $cat;
		}
		return $cats;
	}
	/**
	 * Get a group category
	 * @param int $id The category id
	 * @param string $course_code The course (default = current course)
	 */
	function get_category($id, $course_code = null)
	{
		$course_db = '';
		if ($course_code != null)
		{
			$course_info = Database :: get_course_info_from_code($course_code);
			$course_db = $course_info['database'];
		}
		$table_group_cat = Database :: get_course_table(GROUP_CATEGORY_TABLE, $course_db);
		$sql = "SELECT * FROM $table_group_cat WHERE id = $id";
		$res = api_sql_query($sql,__FILE__,__LINE__);
		return mysql_fetch_array($res);
	}
	/**
	 * Get the category of a given group
	 * @param int $group_id The id of the group
	 * @param string $course_code The course in which the group is (default =
	 * current course)
	 * @return array The category
	 */
	function get_category_from_group($group_id, $course_code = null)
	{
		$course_db = '';
		if ($course_code != null)
		{
			$course_info = Database :: get_course_info_from_code($course_code);
			$course_db = $course_info['database'];
		}
		$table_group = Database :: get_course_table(GROUP_TABLE, $course_db);
		$table_group_cat = Database :: get_course_table(GROUP_CATEGORY_TABLE, $course_db);
		$sql = "SELECT gc.* FROM $table_group_cat gc, $table_group g WHERE gc.id = g.category_id AND g.id=$group_id";
		$res = api_sql_query($sql,__FILE__,__LINE__);
		$cat = mysql_fetch_array($res);
		return $cat;
	}
	/**
	 * Delete a group category
	 * @param int $cat_id The id of the category to delete
	 * @param string $course_code The code in which the category should be
	 * deleted (default = current course)
	 */
	function delete_category($cat_id, $course_code = null)
	{
		$course_db = '';
		if ($course_code != null)
		{
			$course_info = Database :: get_course_info_from_code($course_code);
			$course_db = $course_info['database'];
		}
		$table_group = Database :: get_course_table(GROUP_TABLE, $course_db);
		$table_group_cat = Database :: get_course_table(GROUP_CATEGORY_TABLE, $course_db);
		$sql = "SELECT id FROM $table_group WHERE category_id='".$cat_id."'";
		$res = api_sql_query($sql,__FILE__,__LINE__);
		if (mysql_num_rows($res) > 0)
		{
			$groups_to_delete = array ();
			while ($group = mysql_fetch_object($res))
			{
				$groups_to_delete[] = $group->id;
			}
			GroupManager :: delete_groups($groups_to_delete);
		}
		$sql = "DELETE FROM $table_group_cat WHERE id='".$cat_id."'";
		api_sql_query($sql,__FILE__,__LINE__);
	}
	/**
	 * Create group category
	 * @param string $title The title of the new category
	 * @param string $description The description of the new category
	 * @param int $forum_state The state of the forum when new groups are
	 * created inside this category (TOOL_NOT_AVAILABLE, TOOL_PUBLIC,
	 * TOOL_PRIVATE)
	 * @param bool $self_registration_allowed
	 * @param bool $self_unregistration_allowed
	 * @param int $max_number_of_students
	 * @param int $groups_per_user
	 */
	function create_category($title, $description, $forum_state, $doc_state, $self_registration_allowed, $self_unregistration_allowed, $maximum_number_of_students, $groups_per_user)
	{
		$table_group_category = Database :: get_course_table(GROUP_CATEGORY_TABLE);
		$sql = "SELECT MAX(display_order)+1 as new_order FROM $table_group_category ";
		$res = api_sql_query($sql,__FILE__,__LINE__);
		$obj = mysql_fetch_object($res);
		if (!isset ($obj->new_order))
		{
			$obj->new_order = 1;
		}
		$sql = "INSERT INTO ".$table_group_category." SET title='".mysql_real_escape_string($title)."', display_order = $obj->new_order,description='".mysql_real_escape_string($description)."', forum_state = '".$forum_state."', doc_state = '".$doc_state."', groups_per_user   = ".$groups_per_user.",  self_reg_allowed = '".$self_registration_allowed."', self_unreg_allowed = '".$self_unregistration_allowed."', max_student = ".$maximum_number_of_students." ";
		api_sql_query($sql,__FILE__,__LINE__);
		$id = mysql_insert_id();
		if ($id == VIRTUAL_COURSE_CATEGORY)
		{
			$sql = "UPDATE  ".$table_group_category." SET id = ". ($id +1)." WHERE id = $id";
			api_sql_query($sql,__FILE__,__LINE__);
			return $id +1;
		}
		return $id;
	}
	/**
	 * Update group category
	 * @param int $id The id of the category
	 * @param string $title The title of the new category
	 * @param string $description The description of the new category
	 * @param int $forum_state The state of the forum when new groups are
	 * created inside this category (TOOL_NOT_AVAILABLE, TOOL_PUBLIC,
	 * TOOL_PRIVATE)
	 * @param bool $self_registration_allowed
	 * @param bool $self_unregistration_allowed
	 * @param int $max_number_of_students
	 * @param int $groups_per_user
	 */
	function update_category($id, $title, $description, $forum_state, $doc_state, $self_registration_allowed, $self_unregistration_allowed, $maximum_number_of_students, $groups_per_user)
	{
		$table_group_category = Database :: get_course_table(GROUP_CATEGORY_TABLE);
		$sql = "UPDATE ".$table_group_category." SET title='".mysql_real_escape_string($title)."', description='".mysql_real_escape_string($description)."', forum_state = '".$forum_state."', doc_state = '".$doc_state."', groups_per_user   = ".$groups_per_user.",  self_reg_allowed = '".$self_registration_allowed."', self_unreg_allowed = '".$self_unregistration_allowed."', max_student = ".$maximum_number_of_students." WHERE id=$id";
		api_sql_query($sql,__FILE__,__LINE__);
	}
	/**
	 * Returns the number of groups of the user with the greatest number of
	 * subscribtions in the given category
	 */
	function get_current_max_groups_per_user($category_id = null, $course_code = null)
	{
		$course_db = '';
		if ($course_code != null)
		{
			$course_info = Database :: get_course_info_from_code($course_code);
			$course_db = $course_info['database'];
		}
		$group_table = Database :: get_course_table(GROUP_TABLE, $course_db);
		$group_user_table = Database :: get_course_table(GROUP_USER_TABLE, $course_db);
		$sql = 'SELECT COUNT(gu.group_id) AS current_max FROM '.$group_user_table.' gu, '.$group_table.' g WHERE gu.group_id = g.id ';
		if ($category_id != null)
			$sql .= ' AND g.category_id = '.$category_id;
		$sql .= ' GROUP BY gu.user_id ORDER BY current_max DESC LIMIT 1';
		$res = api_sql_query($sql,__FILE__,__LINE__);
		$obj = mysql_fetch_object($res);
		return $obj->current_max;
	}
	/**
	 * Swaps the display-order of two categories
	 * @param int $id1 The id of the first category
	 * @param int $id2 The id of the second category
	 */
	function swap_category_order($id1, $id2)
	{
		$table_group_cat = Database :: get_course_table(GROUP_CATEGORY_TABLE);
		$sql = "SELECT id,display_order FROM $table_group_cat WHERE id IN ($id1,$id2)";
		$res = api_sql_query($sql,__FILE__,__LINE__);
		$cat1 = mysql_fetch_object($res);
		$cat2 = mysql_fetch_object($res);
		$sql = "UPDATE $table_group_cat SET display_order=$cat2->display_order WHERE id=$cat1->id";
		api_sql_query($sql,__FILE__,__LINE__);
		$sql = "UPDATE $table_group_cat SET display_order=$cat1->display_order WHERE id=$cat2->id";
		api_sql_query($sql,__FILE__,__LINE__);
	}
	/**
	 * Create a group
	 * @param string $name The name for this group
	 * @param int $tutor The user-id of the group's tutor
	 * @param int $places How many people can subscribe to the new group
	 */
	function create_group($name, $category_id, $tutor, $places)
	{
		global $_course,$_uid;
		$currentCourseRepository = $_course['path'];
		$coursesRepositorySys = api_get_path(SYS_COURSE_PATH);
		$table_group = Database :: get_course_table(GROUP_TABLE);
		$table_forum = Database :: get_course_forum_table();
		$category = GroupManager :: get_category($category_id);
		if( strlen($places) == 0)
		{
			$places = $category['max_student'];
		}
		$sql = "INSERT INTO ".$table_group." SET category_id='".$category_id."', max_student = '".$places."', forum_state = '".$category['forum_state']."', doc_state = '".$category['doc_state']."',  self_registration_allowed = '".$category['self_reg_allowed']."',  self_unregistration_allowed = '".$category['self_unreg_allowed']."'";
		api_sql_query($sql,__FILE__,__LINE__);
		$lastId = mysql_insert_id();
		$sql = "INSERT INTO ".$table_forum." (forum_name, forum_desc, forum_access, forum_moderator, forum_topics, forum_posts, forum_last_post_id, cat_id, forum_type)
																														VALUES ('".$name."','', 2, 1, 0, 0, 0, 1, 0)";
		api_sql_query($sql, __FILE__, __LINE__);
		$forumInsertId = mysql_insert_id();
		/*$secret_directory = uniqid("")."_team_".$lastId;
		while (is_dir($coursesRepositorySys.$currentCourseRepository."/group/$secret_directory"))
		{
			$secret_directory = uniqid("")."_team_".$lastId;
		}
		FileManager :: mkdirs($coursesRepositorySys.$currentCourseRepository."/group/".$secret_directory, 0777);
		*/
		$desired_dir_name= '/'.replace_dangerous_char($name,'strict').'_groupdocs';
		$dir_name = create_unexisting_directory($_course,$_uid,$lastId,NULL,$coursesRepositorySys.$currentCourseRepository.'/document',$desired_dir_name);
		/* Stores the directory path into the group table */
		$sql = "UPDATE ".$table_group." SET   name = '".mysql_real_escape_string($name)."', tutor_id = '".$tutor."',forum_id = '".$forumInsertId."', secret_directory = '".$dir_name."' WHERE id ='".$lastId."'";
		api_sql_query($sql,__FILE__,__LINE__);
		return $lastId;
	}
	/**
	 * Create subgroups.
	 * This function creates new groups based on an existing group. It will
	 * create the specified number of groups and fill those groups with users
	 * from the base group
	 * @param int $group_id The group from which subgroups have to be created.
	 * @param int $number_of_groups The number of groups that have to be created
	 */
	function create_subgroups($group_id, $number_of_groups)
	{
		$table_group = Database :: get_course_table(GROUP_TABLE);
		$category_id = GroupManager :: create_category('Subgroups', '', TOOL_PRIVATE, TOOL_PRIVATE, 0, 0, 1, 1);
		$users = GroupManager :: get_users($group_id);
		$group_ids = array ();
		for ($group_nr = 1; $group_nr <= $number_of_groups; $group_nr ++)
		{
			$group_ids[] = GroupManager :: create_group('SUBGROUP '.$group_nr, $category_id, 0, 0);
		}
		$members = array ();
		foreach ($users as $index => $user_id)
		{
			GroupManager :: subscribe_users($user_id, $group_ids[$index % $number_of_groups]);
			$members[$group_ids[$index % $number_of_groups]]++;
		}
		foreach ($members as $group_id => $places)
		{
			$sql = "UPDATE $table_group SET max_student = $places WHERE id = $group_id";
			api_sql_query($sql,__FILE__,__LINE__);
		}
	}
	/**
	 * Create groups from all virtual courses in the given course.
	 */
	function create_groups_from_virtual_courses()
	{
		GroupManager :: delete_category(VIRTUAL_COURSE_CATEGORY);
		$id = GroupManager :: create_category(get_lang('GroupsFromVirtualCourses'), '', TOOL_NOT_AVAILABLE, TOOL_NOT_AVAILABLE, 0, 0, 1, 1);
		$table_group_cat = Database :: get_course_table(GROUP_CATEGORY_TABLE);
		$sql = "UPDATE ".$table_group_cat." SET id=".VIRTUAL_COURSE_CATEGORY." WHERE id=$id";
		api_sql_query($sql,__FILE__,__LINE__);
		$course = api_get_course_info();
		$course['code'] = $course['sysCode'];
		$course['title'] = $course['name'];
		$virtual_courses = CourseManager :: get_virtual_courses_linked_to_real_course($course['sysCode']);
		$group_courses = $virtual_courses;
		$group_courses[] = $course;
		$ids = array ();
		foreach ($group_courses as $index => $group_course)
		{
			$users = CourseManager :: get_user_list_from_course_code($group_course['code']);
			$members = array ();
			foreach ($users as $index => $user)
			{
				if ($user['status'] == 5 && $user['tutor_id'] == 0)
				{
					$members[] = $user['user_id'];
				}
			}
			$id = GroupManager :: create_group($group_course['code'], VIRTUAL_COURSE_CATEGORY, 0, count($members));
			GroupManager :: subscribe_users($members, $id);
			$ids[] = $id;
		}
		return $ids;
	}
	/**
	 * Create a group for every class subscribed to the current course
	 * @param int $category_id The category in which the groups should be
	 * created
	 */
	function create_class_groups($category_id)
	{
		global $_course;
		$classes = ClassManager::get_classes_in_course($_course['sysCode']);
		$group_ids = array();
		foreach($classes as $index => $class)
		{
			$users = ClassManager::get_users($class['id']);
			$group_id = GroupManager::create_group($class['name'],$category_id,0,count($users));
			$user_ids = array();
			foreach($users as $index_user => $user)
			{
				$user_ids[] = $user['user_id'];
			}
			GroupManager::subscribe_users($user_ids,$group_id);
			$group_ids[] = $group_id;
		}
		return $group_ids;
	}
	/**
	 * Get all users from a given group
	 * @param int $group_id The group
	 */
	function get_users($group_id)
	{
		$group_user_table = Database :: get_course_table(GROUP_USER_TABLE, $course_db);
		$sql = "SELECT user_id FROM $group_user_table WHERE group_id = $group_id";
		$res = api_sql_query($sql,__FILE__,__LINE__);
		$users = array ();
		while ($obj = mysql_fetch_object($res))
		{
			$users[] = $obj->user_id;
		}
		return $users;
	}
	/**
	 * deletes groups and their datas.
	 * @author Christophe Gesche <christophe.gesche@claroline.net>
	 * @author Hugues Peeters <hugues.peeters@claroline.net>
	 * @author Bart Mollet
	 * @param  mixed   $groupIdList - group(s) to delete. It can be a single id
	 *                                (int) or a list of id (array).
	 * @param string $course_code Default is current course
	 * @return integer              - number of groups deleted.
	 */
	function delete_groups($group_ids, $course_code = null)
	{
		$course_db = '';
		if ($course_code != null)
		{
			$course = Database :: get_course_info_from_code($course_code);
			$course['path'] = $course['directory'];
			$course_db = $course['database'];
		}
		else
		{
			$course = api_get_course_info();
		}
		$group_table = Database :: get_course_table(GROUP_TABLE, $course_db);
		$group_user_table = Database :: get_course_table(GROUP_USER_TABLE, $course_db);
		$forum_table = Database :: get_course_table(FORUM_TABLE, $course_db);
		$forum_post_table = Database :: get_course_table(FORUM_POST_TABLE, $course_db);
		$forum_post_text_table = Database :: get_course_table(FORUM_POST_TEXT_TABLE, $course_db);
		$forum_topic_table = Database :: get_course_table(FORUM_TOPIC_TABLE, $course_db);
		$group_ids = is_array($group_ids) ? $group_ids : array ($group_ids);
		// define repository for deleted element
		$group_garbage = api_get_path(GARBAGE_PATH).$course['path']."/group/";
		if (!file_exists($group_garbage))
			FileManager :: mkdirs($group_garbage, '0777');
		// Unsubscribe all users
		GroupManager :: unsubscribe_all_users($group_ids);
		$sql = 'SELECT id, secret_directory, forum_id FROM '.$group_table.' WHERE id IN ('.implode(' , ', $group_ids).')';
		$db_result = api_sql_query($sql,__FILE__,__LINE__);
		$forum_ids = array ();
		while ($group = mysql_fetch_object($db_result))
		{
			// move group-documents to garbage
			$source_directory = api_get_path(SYS_COURSE_PATH).$course['path']."/group/".$group->secret_directory;
			$destination_directory = $group_garbage.$group->secret_directory;
			if (file_exists($source_directory))
			{
				rename($source_directory, $destination_directory);
			}
			$forum_ids[] = $group->forum_id;
		}
		// delete the forum (with topics, posts, ...)
		if(count($forum_ids) > 0)
		{
			$post_ids = array ();
			$sql = 'SELECT post_id FROM '.$forum_post_table.' WHERE forum_id IN ('.implode(',', $forum_ids).')';
			$db_result = api_sql_query($sql,__FILE__,__LINE__);
			$post_ids = array();
			while ($post = mysql_fetch_object($db_result))
			{
				$post_ids[] = $post->post_id;
			}
			if( count($post_ids) > 0)
			{
				$sql = 'DELETE FROM '.$forum_post_text_table.' WHERE post_id IN ('.implode(',', $post_ids).')';
				api_sql_query($sql,__FILE__,__LINE__);
			}
			$sql = 'DELETE FROM '.$forum_post_table.' WHERE forum_id IN ('.implode(',', $forum_ids).')';
			api_sql_query($sql,__FILE__,__LINE__);
			$sql = 'DELETE FROM '.$forum_topic_table.' WHERE forum_id IN ('.implode(',', $forum_ids).')';
			api_sql_query($sql,__FILE__,__LINE__);
			$sql = 'DELETE FROM '.$forum_table.' WHERE forum_id IN ('.implode(',', $forum_ids).')';
			api_sql_query($sql,__FILE__,__LINE__);
		}
		// delete the groups
		$sql = "DELETE FROM ".$group_table." WHERE id IN ('".implode("' , '", $group_ids)."')";
		api_sql_query($sql,__FILE__,__LINE__);
		return mysql_affected_rows();
	}
	/**
	 * Fill the groups with students.
	 * The algorithm takes care to first fill the groups with the least # of users.
	 *	Analysis
	 *	There was a problem with the "ALL" setting.
	 *	When max # of groups is set to all, the value is sometimes NULL and sometimes ALL
	 *	and in both cased the query does not work as expected.
	 *	Stupid solution (currently implemented: set ALL to a big number (INFINITE) and things are solved :)
	 *	Better solution: that's up to you.
	 *
	 *	Note
	 *	Throughout Dokeos there is some confusion about "course id" and "course code"
	 *	The code is e.g. TEST101, but sometimes a variable that is called courseID also contains a course code string.
	 *	However, there is also a integer course_id that uniquely identifies the course.
	 *	ywarnier:> Now the course_id has been removed (25/1/2005)
	 *	The databases are als very inconsistent in this.
	 *
	 * @author Chrisptophe Gesche <christophe.geshe@claroline.net>,
	 *         Hugues Peeters     <hugues.peeters@claroline.net> - original version
	 * @author Roan Embrechts - virtual course support, code cleaning
	 * @author Bart Mollet - code cleaning, use other GroupManager-functions
	 * @return void
	 */
	function fill_groups($group_ids)
	{
		$group_ids = is_array($group_ids) ? $group_ids : array ($group_ids);
		global $_course;
		$category = GroupManager :: get_category_from_group($group_ids[0]);
		$groups_per_user = $category['groups_per_user'];
		$course_user_table = Database :: get_main_table(MAIN_COURSE_USER_TABLE);
		$group_table = Database :: get_course_table(GROUP_TABLE);
		$group_user_table = Database :: get_course_table(GROUP_USER_TABLE);
		$real_course_info = Database :: get_course_info_from_code($_course['sysCode']);
		$complete_user_list = CourseManager :: get_real_and_linked_user_list($real_course_info);
		$number_groups_per_user = ($groups_per_user == GROUP_PER_MEMBER_NO_LIMIT ? INFINITE : $groups_per_user);
		/*
		 * Retrieve all the groups where enrollment is still allowed
		 * (reverse) ordered by the number of place available
		 */
		$sql = "SELECT g.id gid, g.max_student-count(ug.user_id) nbPlaces, g.max_student
				FROM ".$group_table." g
				LEFT JOIN  ".$group_user_table." ug
				ON    `g`.`id` = `ug`.`group_id`
				WHERE g.id IN (".implode(',', $group_ids).")
				GROUP BY (`g`.`id`)
				HAVING (nbPlaces > 0 OR g.max_student = ".MEMBER_PER_GROUP_NO_LIMIT.")
				ORDER BY nbPlaces DESC";
		$sql_result = api_sql_query($sql,__FILE__,__LINE__);
		$group_available_place = array ();
		while ($group = mysql_fetch_array($sql_result, MYSQL_ASSOC))
		{
			$group_available_place[$group['gid']] = $group['nbPlaces'];
		}
		/*
		 * Retrieve course users (reverse) ordered by the number
		 * of group they are already enrolled
		 */
		for ($i = 0; $i < count($complete_user_list); $i ++)
		{
			//find # of groups the user is enrolled in
			$number_of_groups = GroupManager :: user_in_number_of_groups($complete_user_list[$i]["user_id"],$category['id']);
			//add # of groups to user list
			$complete_user_list[$i]["number_groups_left"] = $number_groups_per_user - $number_of_groups;
		}
		//first sort by user_id to filter out duplicates
		$complete_user_list = TableSort :: sort_table($complete_user_list, 'user_id');
		$complete_user_list = GroupManager :: filter_duplicates($complete_user_list, "user_id");
		$complete_user_list = GroupManager :: filter_only_students($complete_user_list);
		//now sort by # of group left
		$complete_user_list = TableSort :: sort_table($complete_user_list, 'number_groups_left', SORT_DESC);
		$userToken = array ();
		foreach ($complete_user_list as $this_user)
		{
			if ($this_user["number_groups_left"] > 0)
			{
				$userToken[$this_user["user_id"]] = $this_user["number_groups_left"];
			}
		}
		/*
		 * Retrieve the present state of the users repartion in groups
		 */
		$sql = "SELECT user_id uid, group_id gid FROM ".$group_user_table."";
		$result = api_sql_query($sql,__FILE__,__LINE__);
		while ($member = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$groupUser[$member[gid]][] = $member[uid];
		}
		$changed = true;
		while ($changed)
		{
			$changed = false;
			reset($group_available_place);
			arsort($group_available_place);
			reset($userToken);
			arsort($userToken);
			foreach ($group_available_place as $group_id => $place)
			{
				foreach ($userToken as $user_id => $places)
				{
					if (GroupManager :: can_user_subscribe($user_id, $group_id))
					{
						GroupManager :: subscribe_users($user_id, $group_id);
						$group_available_place[$group_id]--;
						$userToken[$user_id]--;
						$changed = true;
						break;
					}
				}
				if ($changed)
				{
					break;
				}
			}
		}
	}
	/**
	 * Get group properties
	 * @param int $group_id The group from which properties are requested.
	 * @return array All properties. Array-keys are name, tutor_id, description, forum_id, maximum_number_of_students, directory
	 */
	function get_group_properties($group_id)
	{
		if (empty($group_id) or !is_integer(intval($group_id)) ) {
			return null;
		}
		$table_group = Database :: get_course_table(GROUP_TABLE);
		$sql = 'SELECT   *  FROM '.$table_group.' WHERE id = '.$group_id;
		$db_result = api_sql_query($sql,__FILE__,__LINE__);
		$db_object = mysql_fetch_object($db_result);
		$result['id'] = $db_object->id;
		$result['name'] = $db_object->name;
		$result['tutor_id'] = $db_object->tutor_id;
		$result['description'] = $db_object->description;
		$result['forum_state'] = $db_object->forum_state;
		$result['forum_id'] = $db_object->forum_id;
		$result['maximum_number_of_students'] = $db_object->max_student;
		$result['doc_state'] = $db_object->doc_state;
		$result['directory'] = $db_object->secret_directory;
		$result['self_registration_allowed'] = $db_object->self_registration_allowed;
		$result['self_unregistration_allowed'] = $db_object->self_unregistration_allowed;
		return $result;
	}
	/**
	 * Set group properties
	 * Changes the group's properties.
	 * @param int $group_id
	 * @param string $name
	 * @param string $description
	 * @param int $tutor_id
	 * @param int $maximum_number_of_students
	 * @param int $forum_id
	 * @param int $forum_state
	 * @param bool $self_registration_allowed
	 * @param bool $self_unregistration_allowed
	 * @return bool TRUE if properties are successfully changed.
	 */
	function set_group_properties($group_id, $name, $description, $tutor_id, $maximum_number_of_students, $forum_id, $forum_state, $doc_state, $self_registration_allowed, $self_unregistration_allowed)
	{
		$table_group = Database :: get_course_table(GROUP_TABLE);
		$table_forum = Database :: get_course_table(FORUM_TABLE);
		$sql = "UPDATE ".$table_group." SET name='".trim($name)."', forum_state='".$forum_state."', doc_state = '".$doc_state."', description='".trim($description)."', max_student=".$maximum_number_of_students.", tutor_id=".$tutor_id.", self_registration_allowed='".$self_registration_allowed."', self_unregistration_allowed='".$self_unregistration_allowed."' WHERE id=".$group_id;
		$result = api_sql_query($sql,__FILE__,__LINE__);
		$sql = "UPDATE ".$table_forum." SET forum_name='".trim($name)."' WHERE forum_id=".$forum_id;
		$result &= api_sql_query($sql,__FILE__,__LINE__);
		return $result;
	}
	/**
	 * Get the total number of groups for the current course.
	 * @return int The number of groups for the current course.
	 */
	function get_number_of_groups()
	{
		$table_group = Database :: get_course_table(GROUP_TABLE);
		$res = api_sql_query('SELECT COUNT(id) AS number_of_groups FROM '.$table_group);
		$obj = mysql_fetch_object($res);
		return $obj->number_of_groups;
	}
	/**
	 * Get the number of students in a group.
	 * @param int $group_id
	 * @return int Number of students in the given group.
	 */
	function number_of_students($group_id)
	{
		$table_group_user = Database :: get_course_table(GROUP_USER_TABLE);
		$db_result = api_sql_query('SELECT  COUNT(*) AS number_of_students FROM '.$table_group_user.' WHERE group_id = '.$group_id);
		$db_object = mysql_fetch_object($db_result);
		return $db_object->number_of_students;
	}
	/**
	 * Maximum number of students in a group
	 * @param int $group_id
	 * @return int Maximum number of students in the given group.
	 */
	function maximum_number_of_students($group_id)
	{
		$table_group = Database :: get_course_table(GROUP_TABLE);
		$db_result = api_sql_query('SELECT   max_student  FROM '.$table_group.' WHERE id = '.$group_id);
		$db_object = mysql_fetch_object($db_result);
		if ($db_object->max_student == 0)
		{
			return INFINITE;
		}
		return $db_object->max_student;
	}
	/**
	 * Number of groups of a user
	 * @param int $user_id
	 * @return int The number of groups the user is subscribed in.
	 */
	function user_in_number_of_groups($user_id, $cat_id)
	{
		$table_group_user = Database :: get_course_table(GROUP_USER_TABLE);
		$table_group = Database :: get_course_table(GROUP_TABLE);
		$sql = 'SELECT  COUNT(*) AS number_of_groups FROM '.$table_group_user.' gu, '.$table_group.' g WHERE gu.user_id = \''.$user_id.'\' AND g.id = gu.group_id AND g.category_id=  \''.$cat_id.'\'';
		$db_result = api_sql_query($sql,__FILE__,__LINE__);
		$db_object = mysql_fetch_object($db_result);
		return $db_object->number_of_groups;
	}
	/**
	 * Is sef-registration allowed?
	 * @param int $user_id
	 * @param int $group_id
	 * @return bool TRUE if self-registration is allowed in the given group.
	 */
	function is_self_registration_allowed($user_id, $group_id)
	{
		if (!$user_id > 0)
			return false;
		$table_group = Database :: get_course_table(GROUP_TABLE);
		$sql = 'SELECT  self_registration_allowed FROM '.$table_group.' WHERE id = '.$group_id;
		$db_result = api_sql_query($sql,__FILE__,__LINE__);
		$db_object = mysql_fetch_object($db_result);
		return $db_object->self_registration_allowed == 1 && GroupManager :: can_user_subscribe($user_id, $group_id);
	}
	/**
	 * Is sef-unregistration allowed?
	 * @param int $user_id
	 * @param int $group_id
	 * @return bool TRUE if self-unregistration is allowed in the given group.
	 */
	function is_self_unregistration_allowed($user_id, $group_id)
	{
		if (!$user_id > 0)
			return false;
		$table_group = Database :: get_course_table(GROUP_TABLE);
		$db_result = api_sql_query('SELECT  self_unregistration_allowed FROM '.$table_group.' WHERE id = '.$group_id);
		$db_object = mysql_fetch_object($db_result);
		return $db_object->self_unregistration_allowed == 1 && GroupManager :: can_user_unsubscribe($user_id, $group_id);
	}
	/**
	 * Is user subscribed in group?
	 * @param int $user_id
	 * @param int $group_id
	 * @return bool TRUE if given user is subscribed in given group
	 */
	function is_subscribed($user_id, $group_id)
	{
		$table_group_user = Database :: get_course_table(GROUP_USER_TABLE);
		$db_result = api_sql_query('SELECT 1 FROM '.$table_group_user.' WHERE group_id = '.$group_id.' AND user_id = '.$user_id);
		return mysql_num_rows($db_result) > 0;
	}
	/**
	 * Can a user subscribe to a specified group in a course
	 * @param int $user_id
	 * @param int $group_id
	 * @return bool TRUE if given user  can be subscribed in given group
	 */
	function can_user_subscribe($user_id, $group_id)
	{
		global $_course;
		$course_code = $_course['sysCode'];
		$category = GroupManager :: get_category_from_group($group_id);
		$result = CourseManager :: is_user_subscribed_in_real_or_linked_course($_uid, $course_code);
		$result = !GroupManager :: is_subscribed($user_id, $group_id);
		$result &= (GroupManager :: number_of_students($group_id) < GroupManager :: maximum_number_of_students($group_id));
		if ($category['groups_per_user'] == GROUP_PER_MEMBER_NO_LIMIT)
		{
			$category['groups_per_user'] = INFINITE;
		}
		$result &= (GroupManager :: user_in_number_of_groups($user_id, $category['id']) < $category['groups_per_user']);
		$result &= !GroupManager :: is_tutor($user_id);
		return $result;
	}
	/**
	 * Can a user unsubscribe to a specified group in a course
	 * @param int $user_id
	 * @param int $group_id
	 * @return bool TRUE if given user  can be unsubscribed from given group
	 * @internal for now, same as GroupManager::is_subscribed($user_id,$group_id)
	 */
	function can_user_unsubscribe($user_id, $group_id)
	{
		$result = GroupManager :: is_subscribed($user_id, $group_id);
		return $result;
	}
	/**
	 * Get all subscribed users from a group
	 * @param int $group_id
	 * @return array An array with information of all users from the given group.
	 *               (user_id, firstname, lastname, email)
	 */
	function get_subscribed_users($group_id)
	{
		$table_user = Database :: get_main_table(MAIN_USER_TABLE);
		$table_group_user = Database :: get_course_table(GROUP_USER_TABLE);
		$sql = "SELECT `ug`.`id`, `u`.`user_id`, `u`.`lastname`, `u`.`firstname`, `u`.`email`
																																								FROM ".$table_user." u, ".$table_group_user." ug
																																								WHERE `ug`.`group_id`='".$group_id."'
																																								AND `ug`.`user_id`=`u`.`user_id`
																																								ORDER BY UPPER(`u`.`lastname`), UPPER(`u`.`firstname`)";
		$db_result = api_sql_query($sql,__FILE__,__LINE__);
		$users = array ();
		while ($user = mysql_fetch_object($db_result))
		{
			$member['user_id'] = $user->user_id;
			$member['firstname'] = $user->firstname;
			$member['lastname'] = $user->lastname;
			$member['email'] = $user->email;
			$users[] = $member;
		}
		return $users;
	}
	/**
	 * Subscribe user(s) to a specified group in current course
	 * @param mixed $user_ids Can be an array with user-id's or a single user-id
	 * @param int $group_id
	 * @return bool TRUE if successfull
	 */
	function subscribe_users($user_ids, $group_id)
	{
		$user_ids = is_array($user_ids) ? $user_ids : array ($user_ids);
		$result = true;
		foreach ($user_ids as $index => $user_id)
		{
			$table_group_user = Database :: get_course_table(GROUP_USER_TABLE);
			$sql = "INSERT INTO ".$table_group_user." (user_id, group_id) VALUES ('".$user_id."', '".$group_id."')";
			$result &= api_sql_query($sql,__FILE__,__LINE__);
		}
		return $result;
	}
	/**
	 * Unsubscribe user(s) from a specified group in current course
	 * @param mixed $user_ids Can be an array with user-id's or a single user-id
	 * @param int $group_id
	 * @return bool TRUE if successfull
	 */
	function unsubscribe_users($user_ids, $group_id)
	{
		$user_ids = is_array($user_ids) ? $user_ids : array ($user_ids);
		$table_group_user = Database :: get_course_table(GROUP_USER_TABLE);
		$result &= api_sql_query('DELETE FROM '.$table_group_user.' WHERE group_id = '.$group_id.' AND user_id IN ('.implode(',', $user_ids).')');
	}
	/**
	 * Unsubscribe all users from one or more groups
	 * @param mixed $group_id Can be an array with group-id's or a single group-id
	 * @return bool TRUE if successfull
	 */
	function unsubscribe_all_users($group_ids)
	{
		$group_ids = is_array($group_ids) ? $group_ids : array ($group_ids);
		if( count($group_ids) > 0)
		{
			$table_group_user = Database :: get_course_table(GROUP_USER_TABLE);
			$sql = 'DELETE FROM '.$table_group_user.' WHERE group_id IN ('.implode(',', $group_ids).')';
			$result = api_sql_query($sql,__FILE__,__LINE__);
			return $result;
		}
		return true;
	}
	/**
	 * Get all tutors for the current course.
	 * @return array An array with firstname, lastname and user_id for all
	 *               tutors in the current course.
	 */
	function get_all_tutors()
	{
		global $_course;
		$course_user_table = Database :: get_main_table(MAIN_COURSE_USER_TABLE);
		$user_table = Database :: get_main_table(MAIN_USER_TABLE);
		$sql = "SELECT user.user_id AS user_id, user.lastname AS lastname, user.firstname AS firstname
				FROM ".$user_table." user, ".$course_user_table." cu
				WHERE cu.user_id=user.user_id
				AND cu.tutor_id='1'
				AND cu.course_code='".$_course['sysCode']."'";
		$resultTutor = api_sql_query($sql,__FILE__,__LINE__);
		$tutors = array ();
		while ($tutor = mysql_fetch_array($resultTutor))
		{
			$tutors[] = $tutor;
		}
		return $tutors;
	}
	/**
	 * Is user a tutor in current course
	 * @param int $user_id
	 * @return bool TRUE if given user is a tutor in the current course.
	 */
	function is_tutor($user_id)
	{
		global $_course;
		$course_user_table = Database :: get_main_table(MAIN_COURSE_USER_TABLE);
		$sql = "SELECT tutor_id FROM ".$course_user_table."
																																             WHERE `user_id`='".$user_id."'
																																             AND `course_code`='".$_course['sysCode']."'"."AND tutor_id=1";
		$db_result = api_sql_query($sql,__FILE__,__LINE__);
		$result = (mysql_num_rows($db_result) > 0);
		return $result;
	}
	/**
	 * Get all group's from a given course in which a given user is ubscribed
	 * @author  Patrick Cool
	 * @param	 string $course_db: the database of the course you want to
	 * retrieve the groups for
	 * @param integer $user_id: the ID of the user you want to know all its
	 * group memberships
	 */
	function get_group_ids($course_db = '',$user_id)
	{
	$tbl_group = Database::get_course_table(GROUP_USER_TABLE,$course_db);

	$sql = "SELECT group_id FROM $tbl_group WHERE user_id = '$user_id'";
	$groupres = api_sql_query($sql);

	// uncommenting causes a bug in Agenda AND announcements because there we check if the return value of this function is an array or not
	//$groups=array();

	if($groupres)
	{
		while ($myrow= mysql_fetch_array($groupres))
			$groups[]=$myrow['group_id'];
	}

	return $groups;
	}
	/*
	-----------------------------------------------------------
	Group functions
	these take virtual/linked courses into account when necessary
	-----------------------------------------------------------
	*/
	/**
	*	Get a combined list of all users of the real course $course_code
	*		and all users in virtual courses linked to this course $course_code
	*	Filter user list: remove duplicate users; plus
	*		remove users that
	*		- are already in the current group $group_id;
	*		- do not have student status in these courses;
	*		- are not appointed as tutor (group assistent) for this group;
	*		- have already reached their maximum # of groups in this course.
	*
	*	Originally to get the correct list of users a big SQL statement was used,
	*	but this has become more complicated now there is not just one real course but many virtual courses.
	*	Still, that could have worked as well.
	*
	*	@version 1.1.3
	*	@author Roan Embrechts
	*/
	function get_complete_list_of_users_that_can_be_added_to_group($course_code, $group_id)
	{
		global $_course, $_uid;
		$category = GroupManager :: get_category_from_group($group_id, $course_code);
		$number_of_groups_limit = $category['groups_per_user'] == GROUP_PER_MEMBER_NO_LIMIT ? INFINITE : $category['groups_per_user'];
		$real_course_code = $_course['sysCode'];
		$real_course_info = Database :: get_course_info_from_code($real_course_code);
		$real_course_user_list = CourseManager :: get_user_list_from_course_code($virtual_course_code);
		//get list of all virtual courses
		$user_subscribed_course_list = CourseManager :: get_list_of_virtual_courses_for_specific_user_and_real_course($_uid, $real_course_code);
		//add real course to the list
		$user_subscribed_course_list[] = $real_course_info;
		if (!is_array($user_subscribed_course_list))
			return;
		//for all courses...
		foreach ($user_subscribed_course_list as $this_course)
		{
			$this_course_code = $this_course["code"];
			$course_user_list = CourseManager :: get_user_list_from_course_code($this_course_code);
			//for all users in the course
			foreach ($course_user_list as $this_user)
			{
				$user_id = $this_user["user_id"];
				$loginname = $this_user["username"];
				$lastname = $this_user["lastname"];
				$firstname = $this_user["firstname"];
				$status = $this_user["status"];
				//$role =  $this_user["role"];
				$tutor_id = $this_user["tutor_id"];
				$full_name = $lastname.", ".$firstname;
				if ($lastname == "" || $firstname == '')
					$full_name = $loginname;
				$complete_user["user_id"] = $user_id;
				$complete_user["full_name"] = $full_name;
				$complete_user['firstname'] = $firstname;
				$complete_user['lastname'] = $lastname;
				$complete_user["status"] = $status;
				$complete_user["tutor_id"] = $tutor_id;
				$student_number_of_groups = GroupManager :: user_in_number_of_groups($user_id, $category['id']);
				//filter: only add users that have not exceeded their maximum amount of groups
				if ($student_number_of_groups < $number_of_groups_limit)
				{
					$complete_user_list[] = $complete_user;
				}
			}
		}
		if (is_array($complete_user_list))
		{
			//sort once, on array field "full_name"
			$complete_user_list = TableSort :: sort_table($complete_user_list, "full_name");
			//filter out duplicates, based on field "user_id"
			$complete_user_list = GroupManager :: filter_duplicates($complete_user_list, "user_id");
			$complete_user_list = GroupManager :: filter_users_already_in_group($complete_user_list, $group_id);
			//$complete_user_list = GroupManager :: filter_only_students($complete_user_list);
		}
		return $complete_user_list;
	}
	/**
	*	Filter out duplicates in a multidimensional array
	*	by comparing field $compare_field.
	*
	*	@param $user_array_in list of users (must be sorted).
	*	@param string $compare_field, the field to be compared
	*/
	function filter_duplicates($user_array_in, $compare_field)
	{
		$total_number = count($user_array_in);
		$user_array_out[0] = $user_array_in[0];
		$count_out = 0;
		for ($count_in = 1; $count_in < $total_number; $count_in ++)
		{
			if ($user_array_in[$count_in][$compare_field] != $user_array_out[$count_out][$compare_field])
			{
				$count_out ++;
				$user_array_out[$count_out] = $user_array_in[$count_in];
			}
		}
		return $user_array_out;
	}
	/**
	*	Filters from the array $user_array_in the users already in the group $group_id.
	*/
	function filter_users_already_in_group($user_array_in, $group_id)
	{
		foreach ($user_array_in as $this_user)
		{
			if (!GroupManager :: is_subscribed($this_user['user_id'], $group_id))
			{
				$user_array_out[] = $this_user;
			}
		}
		return $user_array_out;
	}
	/**
	* Remove all users that are not students and all users who have tutor status
	* from  the list.
	*/
	function filter_only_students($user_array_in)
	{
		$user_array_out = array ();
		foreach ($user_array_in as $this_user)
		{
			if ($this_user['status'] == STUDENT && $this_user['tutor_id'] == 0)
			{
				$user_array_out[] = $this_user;
			}
		}
		return $user_array_out;
	}
	/**
	 * Check if a user has access to a certain group tool
	 * @param int $user_id The user id
	 * @param int $group_id The group id
	 * @param constant $tool The tool to check the access rights. This should be
	 * one of constants: GROUP_TOOL_FORUM, GROUP_TOOL_DOCUMENTS
	 * @return bool True if the given user has access to the given tool in the
	 * given course.
	 */
	function user_has_access($user_id, $group_id, $tool)
	{
		switch ($tool)
		{
			case GROUP_TOOL_FORUM :
				$state_key = 'forum_state';
				break;
			case GROUP_TOOL_DOCUMENTS :
				$state_key = 'doc_state';
				break;
			default:
				return false;
		}
		$group = GroupManager :: get_group_properties($group_id);
		if ($group[$state_key] == TOOL_NOT_AVAILABLE)
		{
			return false;
		}
		elseif ($group[$state_key] == TOOL_PUBLIC)
		{
			return true;
		}
		elseif (api_is_allowed_to_edit())
		{
			return true;
		}
		elseif($group['tutor_id'] == $user_id)
		{
			return true;
		}
		else
		{
			return GroupManager :: is_subscribed($user_id, $group_id);
		}
	}
}
?>