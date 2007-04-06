<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) various contributors

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
*	This is the index file displayed when a user is logged in on Dokeos.
*
*	It displays:
*	- personal course list
*	- menu bar
*
*	Part of the what's new ideas were based on a rene haentjens hack
*
*	Search for
*	CONFIGURATION parameters
*	to modify settings
*
*	@todo rewrite code to separate display, logic, database code
*	@package dokeos.main
==============================================================================
*/
/*
==============================================================================
	   INIT SECTION
==============================================================================
*/
// Don't change these settings
define('SCRIPTVAL_No', 0);
define('SCRIPTVAL_InCourseList', 1);
define('SCRIPTVAL_UnderCourseList', 2);
define('SCRIPTVAL_Both', 3);
define('SCRIPTVAL_NewEntriesOfTheDay', 4);
define('SCRIPTVAL_NewEntriesOfTheDayOfLastLogin', 5);
define('SCRIPTVAL_NoTimeLimit', 6);
// End 'don't change' section

$langFile = array ('courses', 'index');

$cidReset = true; /* Flag forcing the 'current course' reset,
                   as we're not inside a course anymore  */
/*
-----------------------------------------------------------
	Included libraries
-----------------------------------------------------------
*/
//this includes main_api too:
include_once ('./main/inc/claro_init_global.inc.php');
$this_section = SECTION_COURSES;

api_block_anonymous_users(); // only users who are logged in can proceed

include_once (api_get_library_path()."/course.lib.php");
include_once (api_get_library_path()."/debug.lib.inc.php");
include_once (api_get_library_path()."/system_announcements.lib.php");
include_once (api_get_library_path()."/text.lib.php");
include_once (api_get_library_path()."/groupmanager.lib.php");

/*
-----------------------------------------------------------
	Table definitions
-----------------------------------------------------------
*/
//new table definitions, using database library
//these already have backticks around them!
$main_user_table = Database :: get_main_table(MAIN_USER_TABLE);
$main_admin_table = Database :: get_main_table(MAIN_ADMIN_TABLE);
$main_course_table = Database :: get_main_table(MAIN_COURSE_TABLE);
$main_course_user_table = Database :: get_main_table(MAIN_COURSE_USER_TABLE);
$main_category_table = Database :: get_main_table(MAIN_CATEGORY_TABLE);

/*
-----------------------------------------------------------
	Constants and CONFIGURATION parameters
-----------------------------------------------------------
*/
// ---- Course list options ----
// Preview of course content
// to disable all: set CONFVAL_maxTotalByCourse = 0
// to enable all: set e.g. CONFVAL_maxTotalByCourse = 5
// by default disabled since what's new icons are better (see function display_digest() )
define('CONFVAL_maxValvasByCourse', 2); // Maximum number of entries
define('CONFVAL_maxAgendaByCourse', 2); //  collected from each course
define('CONFVAL_maxTotalByCourse', 0); //  and displayed in summary.
define('CONFVAL_NB_CHAR_FROM_CONTENT', 80);
// Order to sort data
$orderKey = array('keyTools', 'keyTime', 'keyCourse'); // default "best" Choice
//$orderKey = array('keyTools', 'keyCourse', 'keyTime');
//$orderKey = array('keyCourse', 'keyTime', 'keyTools');
//$orderKey = array('keyCourse', 'keyTools', 'keyTime');
define('CONFVAL_showExtractInfo', SCRIPTVAL_UnderCourseList);
// SCRIPTVAL_InCourseList    // best choice if $orderKey[0] == 'keyCourse'
// SCRIPTVAL_UnderCourseList // best choice
// SCRIPTVAL_Both // probably only for debug
//define('CONFVAL_dateFormatForInfosFromCourses', get_lang('dateFormatShort'));
define('CONFVAL_dateFormatForInfosFromCourses', get_lang('dateFormatLong'));
//define("CONFVAL_limitPreviewTo",SCRIPTVAL_NewEntriesOfTheDay);
//define("CONFVAL_limitPreviewTo",SCRIPTVAL_NoTimeLimit);
define("CONFVAL_limitPreviewTo", SCRIPTVAL_NewEntriesOfTheDayOfLastLogin);

$nameTools = get_lang('MyCourses');

/*
-----------------------------------------------------------
	Check configuration parameters integrity
-----------------------------------------------------------
*/
if (CONFVAL_showExtractInfo != SCRIPTVAL_UnderCourseList and $orderKey[0] != "keyCourse")
{
	// CONFVAL_showExtractInfo must be SCRIPTVAL_UnderCourseList to accept $orderKey[0] !="keyCourse"
	if (DEBUG || api_is_platform_admin()) // Show bug if admin. Else force a new order
		die('
					<strong>config error:'.__FILE__.'</strong><br />
					set
					<ul>
						<li>
							CONFVAL_showExtractInfo = SCRIPTVAL_UnderCourseList
							(actually : '.CONFVAL_showExtractInfo.')
						</li>
					</ul>
					or
					<ul>
						<li>
							$orderKey[0] != "keyCourse"
							(actually : '.$orderKey[0].')
						</li>
					</ul>');
	else
	{
		$orderKey = array ('keyCourse', 'keyTools', 'keyTime');
	}
}

/*
-----------------------------------------------------------
	Header
	include the HTTP, HTML headers plus the top banner
-----------------------------------------------------------
*/
Display :: display_header($nameTools, 'Mycourses');

/*
==============================================================================
		FUNCTIONS

		display_admin_links()
		display_create_course_link()
		display_edit_course_list_links()
		display_digest($toolsList, $digest, $orderKey, $courses)
		show_notification($mycours)

		get_personal_course_list($user_id)
		get_logged_user_course_html($mycours)
		get_user_course_categories()
==============================================================================
*/
/*
-----------------------------------------------------------
	Database functions
	some of these can go to database layer.
-----------------------------------------------------------
*/
/**
* Database function that gets the list of courses for a particular user.
* @param $user_id, the id of the user
* @return an array with courses
*/
function get_personal_course_list($user_id)
{
	$personal_course_list = array();
	$main_user_table = Database :: get_main_table(MAIN_USER_TABLE);
	$main_course_table = Database :: get_main_table(MAIN_COURSE_TABLE);
	$main_course_user_table = Database :: get_main_table(MAIN_COURSE_USER_TABLE);
	$personal_course_list_sql = "SELECT course.code k, course.directory d, course.visual_code c, course.db_name db, course.title i,
										course.tutor_name t, course.course_language l, course_rel_user.status s, course_rel_user.sort sort,
										course_rel_user.user_course_cat user_course_cat
										FROM    ".$main_course_table."       course,".$main_course_user_table."   course_rel_user
										WHERE course.code = course_rel_user.course_code"."
										AND   course_rel_user.user_id = '".$user_id."'
										ORDER BY course_rel_user.user_course_cat, course_rel_user.sort ASC,course.title,course.code";
	$course_list_sql_result = api_sql_query($personal_course_list_sql, __FILE__, __LINE__);
	$personal_course_list = array ();
	while ($result_row = mysql_fetch_array($course_list_sql_result))
	{
		$personal_course_list[] = $result_row;
	}
	return $personal_course_list;
}
/*
-----------------------------------------------------------
	Display functions
-----------------------------------------------------------
*/
/**
 * Warning: this function defines a global.
 */
function display_admin_links()
{
	global $rootAdminWeb;
	echo "<li><a href=\"".$rootAdminWeb."\">".get_lang("PlatformAdmin")."</a></li>";
}
function display_create_course_link()
{
	echo "<li><a href=\"main/create_course/add_course.php\">".get_lang("CourseCreate")."</a></li>";
}
function display_edit_course_list_links()
{
	echo "<li><a href=\"main/auth/courses.php\">".get_lang("CourseManagement")."</a></li>";
}

/**
*	Displays a digest e.g. short summary of new agenda and announcements items.
*	This used to be displayed in the right hand menu, but is now
*	disabled by default (see config settings in this file) because most people like
*	the what's new icons better.
*
*	@version 1.0
*/
function display_digest($toolsList, $digest, $orderKey, $courses)
{
	if (is_array($digest) && (CONFVAL_showExtractInfo == SCRIPTVAL_UnderCourseList || CONFVAL_showExtractInfo == SCRIPTVAL_Both))
	{
		// // // LEVEL 1 // // //
		reset($digest);
		echo "<br/><br/>\n";
		while (list ($key1) = each($digest))
		{
			if (is_array($digest[$key1]))
			{
				// // // Title of LEVEL 1 // // //
				echo "<b>\n";
				if ($orderKey[0] == 'keyTools')
				{
					$tools = $key1;
					echo $toolsList[$key1][name];
				}
				elseif ($orderKey[0] == 'keyCourse')
				{
					$courseSysCode = $key1;
					echo "<a href=\"", api_get_path(WEB_COURSE_PATH), $courses[$key1][coursePath], "\">", $courses[$key1][courseCode], "</a>\n";
				}
				elseif ($orderKey[0] == 'keyTime')
				{
					echo format_locale_date(CONFVAL_dateFormatForInfosFromCourses, strtotime($digest[$key1]));
				}
				echo "</b>\n";
				// // // End Of Title of LEVEL 1 // // //
				// // // LEVEL 2 // // //
				reset($digest[$key1]);
				while (list ($key2) = each($digest[$key1]))
				{
					// // // Title of LEVEL 2 // // //
					echo "<p>\n", "\n";
					if ($orderKey[1] == 'keyTools')
					{
						$tools = $key2;
						echo $toolsList[$key2][name];
					}
					elseif ($orderKey[1] == 'keyCourse')
					{
						$courseSysCode = $key2;
						echo "<a href=\"", api_get_path(WEB_COURSE_PATH), $courses[$key2]['coursePath'], "\">", $courses[$key2]['courseCode'], "</a>\n";
					}
					elseif ($orderKey[1] == 'keyTime')
					{
						echo format_locale_date(CONFVAL_dateFormatForInfosFromCourses, strtotime($key2));
					}
					echo "\n";
					echo "</p>";
					// // // End Of Title of LEVEL 2 // // //
					// // // LEVEL 3 // // //
					reset($digest[$key1][$key2]);
					while (list ($key3, $dataFromCourse) = each($digest[$key1][$key2]))
					{
						// // // Title of LEVEL 3 // // //
						if ($orderKey[2] == 'keyTools')
						{
							$level3title = "<a href=\"".$toolsList[$key3]["path"].$courseSysCode."\">".$toolsList[$key3]["name"]."</a>";
						}
						elseif ($orderKey[2] == 'keyCourse')
						{
							$level3title = "&#8226; <a href=\"".$toolsList[$tools]["path"].$key3."\">".$courses[$key3]['courseCode']."</a>\n";
						}
						elseif ($orderKey[2] == 'keyTime')
						{
							$level3title = "&#8226; <a href=\"".$toolsList[$tools]["path"].$courseSysCode."\">".format_locale_date(CONFVAL_dateFormatForInfosFromCourses, strtotime($key3))."</a>";
						}
						// // // End Of Title of LEVEL 3 // // //
						// // // LEVEL 4 (data) // // //
						reset($digest[$key1][$key2][$key3]);
						while (list ($key4, $dataFromCourse) = each($digest[$key1][$key2][$key3]))
						{
							echo $level3title, ' &ndash; ', substr(strip_tags($dataFromCourse), 0, CONFVAL_NB_CHAR_FROM_CONTENT);
							//adding ... (three dots) if the texts are too large and they are shortened
							if (strlen($dataFromCourse) >= CONFVAL_NB_CHAR_FROM_CONTENT)
							{
								echo '...';
							}
						}
						echo "<br/>\n";
					}
				}
			}
		}
	}
} // end function display_digest

/**
 * Display code for one specific course a logged in user is subscribed to.
 * Shows a link to the course, what's new icons...
 *
 * $mycours['d'] - course directory
 * $mycours['i'] - course title
 * $mycours['c'] - visual course code
 * $mycours['k']   - system course code
 * $mycours['db']  - course database
 *
 * @version 1.0.3
 * @todo refactor into different functions for database calls | logic | display
 * @todo replace single-character $mycours['d'] indices
 * @todo move code for what's new icons to a separate function to clear things up
 */
function get_logged_user_course_html($mycours)
{
	//initialise
	$result = '';
	//$statistic_database = Database::get_statistic_database();
	$user_id = api_get_user_id();
	$course_database = $mycours['db'];
	$course_tool_table = Database :: get_course_tool_list_table($course_database);
	$tool_edit_table = Database :: get_course_last_tool_edit_table($course_database);
	$course_group_user_table = Database :: get_course_group_user_table($course_database);
	$course_system_code = $mycours['k'];
	$course_visual_code = $mycours['c'];
	$course_title = $mycours['i'];
	$course_directory = $mycours['d'];
	$course_teacher = $mycours['t'];
	$course_info = Database :: get_course_info_from_code($course_system_code);
	$course_access_settings = CourseManager :: get_access_settings($course_system_code);
	$course_id = $course_info['course_id'];
	$course_visibility = $course_access_settings['visibility'];
	$user_in_course_status = CourseManager :: get_user_in_course_status($user_id, $course_system_code);
	//function logic - act on the data
	$is_virtual_course = CourseManager :: is_virtual_course_from_system_code($mycours['c']);
	if ($is_virtual_course)
	{
		// If the current user is also subscribed in the real course to which this
		// virtual course is linked, we don't need to display the virtual course entry in
		// the course list - it is combined with the real course entry.
		$target_course_code = CourseManager :: get_target_of_linked_course($course_system_code);
		$is_subscribed_in_target_course = CourseManager :: is_user_subscribed_in_course($user_id, $target_course_code);
		if ($is_subscribed_in_target_course)
		{
			return; //do not display this course entry
		}
	}
	$has_virtual_courses = CourseManager :: has_virtual_courses_from_code($course_system_code, $user_id);
	if ($has_virtual_courses)
	{
		$return_result = CourseManager :: determine_course_title_from_course_info($user_id, $course_info);
		$course_display_title = $return_result['title'];
		$course_display_code = $return_result['code'];
	}
	else
	{
		$course_display_title = $course_title;
		$course_display_code = $course_visual_code;
	}
	//display course entry
	$result .= "\n\t<li>";
	//show a hyperlink to the course, unless the course is closed and user is not course admin
	if ($course_visibility != COURSE_VISIBILITY_CLOSED || $user_in_course_status == COURSEMANAGER)
	{
		$result .= $course_display_title.' ';
		$result .= ' [<a href="'.api_get_path(WEB_COURSE_PATH).$course_directory.'/">OLD</a>] ';
		$result .= ' [<a href="index_weblcms.php?course='.$course_system_code.'">NEW</a>]';
	}
	else
	{
		$result .= $course_display_title." "." ".get_lang("CourseClosed")."";
	}
	// show the course_code and teacher if chosen to display this
	if (get_setting("display_coursecode_in_courselist") == "true" OR get_setting("display_teacher_in_courselist") == "true")
	{
		$result .= "<br/>";
	}
	if (get_setting("display_coursecode_in_courselist") == "true")
	{
		$result .= $course_display_code;
	}
	if (get_setting("display_coursecode_in_courselist") == "true" AND get_setting("display_teacher_in_courselist") == "true")
	{
		$result .= ' &ndash; ';
	}
	if (get_setting("display_teacher_in_courselist") == "true")
	{
		$result .= $course_teacher;
	}
	// display the what's new icons
	$result .= show_notification($mycours);

	if ((CONFVAL_showExtractInfo == SCRIPTVAL_InCourseList || CONFVAL_showExtractInfo == SCRIPTVAL_Both) && $nbDigestEntries > 0)
	{
		reset($digest);
		$result .= "<ul>";
		while (list ($key2) = each($digest[$thisCourseSysCode]))
		{
			$result .= "<li>";
			if ($orderKey[1] == 'keyTools')
			{
				$result .= "<a href=\"$toolsList[$key2] [\"path\"] $thisCourseSysCode \">";
				$result .= "$toolsList[$key2][\"name\"]</a>";
			}
			else
			{
				$result .= format_locale_date(CONFVAL_dateFormatForInfosFromCourses, strtotime($key2));
			}
			$result .= "</li>";
			$result .= "<ul>";
			reset($digest[$thisCourseSysCode][$key2]);
			while (list ($key3, $dataFromCourse) = each($digest[$thisCourseSysCode][$key2]))
			{
				$result .= "<li>";
				if ($orderKey[2] == 'keyTools')
				{
					$result .= "<a href=\"$toolsList[$key3] [\"path\"] $thisCourseSysCode \">";
					$result .= "$toolsList[$key3][\"name\"]</a>";
				}
				else
				{
					$result .= format_locale_date(CONFVAL_dateFormatForInfosFromCourses, strtotime($key3));
				}
				$result .= "<ul compact=\"compact\">";
				reset($digest[$thisCourseSysCode][$key2][$key3]);
				while (list ($key4, $dataFromCourse) = each($digest[$thisCourseSysCode][$key2][$key3]))
				{
					$result .= "<li>";
					$result .= htmlspecialchars(substr(strip_tags($dataFromCourse), 0, CONFVAL_NB_CHAR_FROM_CONTENT));
					$result .= "</li>";
				}
				$result .= "</ul>";
				$result .= "</li>";
			}
			$result .= "</ul>";
			$result .= "</li>";
		}
		$result .= "</ul>";
	}
	$result .= "</li>";
	$output = array ($mycours['user_course_cat'], $result);
	return $output;
}

/**
 * Returns the "what's new" icon notifications
 * @version
 */
function show_notification($mycours)
{
	$statistic_database = Database :: get_statistic_database();
	$user_id = api_get_user_id();
	$course_database = $mycours['db'];
	$course_tool_table = Database :: get_course_tool_list_table($course_database);
	$tool_edit_table = Database :: get_course_last_tool_edit_table($course_database);
	$course_group_user_table = Database :: get_course_group_user_table($course_database);
	// get the user's last access dates to all tools of this course
	$sqlLastTrackInCourse = "SELECT * FROM $statistic_database.track_e_lastaccess
									 USE INDEX (access_cours_code, access_user_id)
									 WHERE access_cours_code = '".$mycours['k']."'
									 AND access_user_id = '$user_id'";
	$resLastTrackInCourse = api_sql_query($sqlLastTrackInCourse, __FILE__, __LINE__);
	$oldestTrackDate = "3000-01-01 00:00:00";
	while ($lastTrackInCourse = mysql_fetch_array($resLastTrackInCourse))
	{
		$lastTrackInCourseDate[$lastTrackInCourse["access_tool"]] = $lastTrackInCourse["access_date"];
		if ($oldestTrackDate > $lastTrackInCourse["access_date"])
			$oldestTrackDate = $lastTrackInCourse["access_date"];
	}
	// get the last edits of all tools of this course
	$sql = "SELECT tet.*, tet.lastedit_date last_date, tet.tool tool, tet.ref ref,
						tet.lastedit_type type, tet.to_group_id group_id,
						ctt.image image, ctt.link link
					FROM $tool_edit_table tet, $course_tool_table ctt
					WHERE tet.lastedit_date > '$oldestTrackDate'
					AND ctt.name = tet.tool
					AND ctt.visibility = '1'
					AND tet.lastedit_user_id != $user_id
					ORDER BY tet.lastedit_date";
	$res = api_sql_query($sql);
	//get the group_id's with user membership
	$group_ids = GroupManager :: get_group_ids($course_database, $user_id);
	$groups_ids[] = 0; //add group 'everyone'
	//filter all selected items
	while ($res && ($item_property = mysql_fetch_array($res)))
	{
		if ((!isset ($lastTrackInCourseDate[$item_property['tool']]) || $lastTrackInCourseDate[$item_property['tool']] < $item_property['lastedit_date']) && (in_array($item_property['to_group_id'], $groups_ids) || $item_property['to_user_id'] == $user_id) && ($item_property['visibility'] == '1' || ($mycours['s'] == '1' && $item_property['visibility'] == '0') || !isset ($item_property['visibility'])))
		{
			$notifications[$item_property['tool']] = $item_property;
		}
	}
	//show all tool icons where there is something new
	$retvalue = '&nbsp;';
	if (isset ($notifications))
	{
		while (list ($key, $notification) = each($notifications))
		{
			$lastDate = date("d/m/Y H:i", convert_mysql_date($notification['lastedit_date']));
			$type = $notification['lastedit_type'];
			//$notification[image]=str_replace(".png","gif",$notification[image]);
			//$notification[image]=str_replace(".gif","_s.gif",$notification[image]);
			$retvalue .= '<a href="'.api_get_path(WEB_CODE_PATH).$notification['link'].'?cidReq='.$mycours['k'].'&amp;ref='.$notification['ref'].'">'.'<img title="-- '.get_lang($notification['tool']).' -- '.get_lang('_title_notification').": $type ($lastDate).\"".' src="'.api_get_path(WEB_CODE_PATH).'img/'.$notification['image'].'" border="0" align="middle" /></a>&nbsp;';
		}
	}
	return $retvalue;
}

/**
 * retrieves the user defined course categories
 * @author Patrick Cool <patrick.cool@UGent.be>, Ghent University
 * @return array containing all the titles of the user defined courses with the id as key of the array
*/
function get_user_course_categories()
{
	global $_uid;
	$table_category = Database::get_user_personal_table(USER_COURSE_CATEGORY_TABLE);
	$sql = "SELECT * FROM ".$table_category." WHERE user_id='".$_uid."'";
	$result = api_sql_query($sql,__FILE__,__LINE__);
	while ($row = mysql_fetch_array($result))
	{
		$output[$row['id']] = $row['title'];
	}
	return $output;
}

/*
==============================================================================
		MAIN CODE
==============================================================================
*/
/*
==============================================================================
		PERSONAL COURSE LIST
==============================================================================
*/
echo "<div class=\"maincontent\">"; // start of content for logged in users
// Display System announcements
$announcement = $_GET['announcement'] ? $_GET['announcement'] : -1;
$visibility = api_is_allowed_to_create_course() ? VISIBLE_TEACHER : VISIBLE_STUDENT;
SystemAnnouncementManager :: display_announcements($visibility, $announcement);

if (!empty ($_GET['include']) && !strstr($_GET['include'], '/') && strstr($_GET['include'], '.html'))
{
	include ('./home/'.$_GET['include']);
	$pageIncluded = true;
}
else
{
	echo "<h3>".get_lang("MyCourses")."</h3>";
	/*--------------------------------------
	              DISPLAY COURSES
	   --------------------------------------*/
	$list = '';
	$personal_course_list = get_personal_course_list($_uid);
	foreach ($personal_course_list as $mycours)
	{
		$thisCourseDbName = $mycours['db'];
		$thisCourseSysCode = $mycours['k'];
		$thisCoursePublicCode = $mycours['c'];
		$thisCoursePath = $mycours['d'];
		$sys_course_path = api_get_path(SYS_COURSE_PATH);
		
		$dbname = $mycours['k'];
		$status[$dbname] = $mycours['s'];
		$nbDigestEntries = 0; // number of entries already collected
		if ($maxCourse < $maxValvas)
			$maxValvas = $maxCourse;
		if ($maxCourse > 0)
		{
			$courses[$thisCourseSysCode]['coursePath'] = $thisCoursePath;
			$courses[$thisCourseSysCode]['courseCode'] = $thisCoursePublicCode;
		}
		/*
		-----------------------------------------------------------
			Digest Display
			take collected data and display it
		-----------------------------------------------------------
		*/
		$list[] = get_logged_user_course_html($mycours);
	} //end while mycourse...
}

if (is_array($list))
{
	$old_user_category = 0;
	$userdefined_categories = get_user_course_categories();
	echo "<ul>\n";
	foreach ($list as $key => $value)
	{
		if ($old_user_category<>$value[0])
		{
			if ($key<>0 OR $value[0]<>0) // there are courses in the previous category
			{
				echo "\n</ul>";
			}
			echo "\n\n\t<ul class=\"user_course_category\"><li>".$userdefined_categories[$value[0]]."</li></ul>\n";
			if ($key<>0 OR $value[0]<>0) // there are courses in the previous category
			{
				echo "<ul>";
			}
			$old_user_category=$value[0];

		}
		echo $value[1];

	}
	echo "\n</ul>\n";
}
echo "</div>"; // end of content section
// Register whether full admin or null admin course
// by course through an array dbname x user status
api_session_register('status');

/*
==============================================================================
		RIGHT MENU
==============================================================================
*/
echo "<div class=\"menu\">";

// api_display_language_form(); // moved to the profile page.
echo "<div class=\"menusection\">";
echo "<span class=\"menusectioncaption\">".get_lang("MenuUser")."</span>";
echo "<ul class=\"menulist\">";

$display_add_course_link = api_is_allowed_to_create_course() && ($_SESSION["studentview"] != "studentenview");
if ($display_add_course_link)
	display_create_course_link();
display_edit_course_list_links();
display_digest($toolsList, $digest, $orderKey, $courses);

echo "</ul>";
echo "</div>";
/*** hide right menu "general" and "platform admin" parts ***
	echo "<div class=\"menusection\">";
	echo "<span class=\"menusectioncaption\">".get_lang("MenuGeneral")."</span><ul class=\"menulist\">";

	$user_selected_language = $_SESSION["user_language_choice"];
	if (!isset ($user_selected_language))
		$user_selected_language = $platformLanguage;

	if(!file_exists('home/home_menu_'.$user_selected_language.'.html'))
	{
		include ('home/home_menu.html');
	}
	else
	{
		include('home/home_menu_'.$user_selected_language.'.html');
	}

	echo "</ul></div>";
	if (api_is_platform_admin())
	{
		echo "<div class=\"menusection\">";
		echo "<span class=\"menusectioncaption\">".get_lang("MenuAdmin")."</span>";
		echo "<ul class=\"menulist\">";
		display_admin_links();
		echo "</ul>";
		echo "</div>";
	}
**** end of hide right menu parts ***/

	// Load appropriate plugins for this menu bar
if (is_array($plugins['main_menu_logged']))
{
	foreach ($plugins['main_menu_logged'] as $this_plugin)
	{
		include (api_get_path(PLUGIN_PATH)."$this_plugin/index.php");
	}
}

echo "</div>"; // end of menu

/*
==============================================================================
		FOOTER
==============================================================================
*/
Display :: display_footer();
?>