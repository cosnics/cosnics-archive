<?php
// $Id$
/*
============================================================================== 
	Dokeos - elearning and course management software
	
	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 Ghent University (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Bart Mollet (digitaal-leren@hogent.be)
	
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
 * ==============================================================================
 * Copy resources from one course to another one.
 * 
 * @author Bart Mollet <digitaal-leren@hogent.be>
 * @package dokeos.backup
 * ==============================================================================
 */
/*
==============================================================================
		INIT SECTION
==============================================================================
*/ 
api_use_lang_files('coursebackup');
include ('../inc/claro_init_global.inc.php');
include_once(api_get_library_path() . "/fileManage.lib.php");
require_once ('classes/CourseBuilder.class.php');
require_once ('classes/CourseRestorer.class.php');
require_once ('classes/CourseSelectForm.class.php');
$nameTools = get_lang('CopyCourse');
Display::display_header($nameTools);
api_display_tool_title($nameTools);
if (!api_is_allowed_to_edit())
{
	api_not_allowed();
}
/*
==============================================================================
		MAIN CODE
==============================================================================
*/ 
// If a CourseSelectForm is posted or we should copy all resources, then copy them
if ((isset ($_POST['action']) && $_POST['action'] == 'course_select_form') || (isset ($_POST['copy_option']) && $_POST['copy_option'] == 'full_copy'))
{
	if (isset ($_POST['action']) && $_POST['action'] == 'course_select_form')
	{
		$course = CourseSelectForm :: get_posted_course();
	}
	else
	{
		$cb = new CourseBuilder();
		$course = $cb->build();
	}
	$cr = new CourseRestorer($course);
	$cr->set_file_option($_POST['same_file_name_option']);
	$cr->restore($_POST['destination_course']);
	echo get_lang('CopyFinished');
}
// Else, if a CourseSelectForm is requested, show it
elseif (isset ($_POST['copy_option']) && $_POST['copy_option'] == 'select_items')
{
	$cb = new CourseBuilder();
	$course = $cb->build();
	echo get_lang('SelectItemsToCopy');
	echo '<br/><br/>';
	$hidden_fields['same_file_name_option'] = $_POST['same_file_name_option'];
	$hidden_fields['destination_course'] = $_POST['destination_course'];
	CourseSelectForm :: display_form($course, $hidden_fields);
}
// Else, show the default page
else
{
	$table_c = Database :: get_main_table(MAIN_COURSE_TABLE);
	$table_cu = Database :: get_main_table(MAIN_COURSE_USER_TABLE);
	$user_info = api_get_user_info();
	$course_info = api_get_course_info();
	$sql = 'SELECT * FROM '.$table_c.' c, '.$table_cu.' cu WHERE cu.course_code = c.code';
	if( ! api_is_platform_admin())
	{
		$sql .= ' AND cu.status=1 ';
	}
	$sql .= ' AND target_course_code IS NULL AND cu.user_id = '.$user_info['user_id'].' AND c.code != '."'".$course_info['sysCode']."'".' ';
	$res = api_sql_query($sql,__FILE__,__LINE__);
	if( mysql_num_rows($res) == 0)
	{
		Display::display_normal_message(get_lang('NoDestinationCoursesAvailable'));	
	}
	else
	{
?>
	<form method="post" action="copy_course.php">
	<?php
	echo get_lang('SelectDestinationCourse');
	echo ' <select name="destination_course"/>';
	while ($obj = mysql_fetch_object($res))
	{
		echo '<option value="'.$obj->code.'">'.$obj->title.'</option>';
	}
	echo '</select>';
?>
	<br/>
	<br/>
	<input type="radio" class="checkbox" id="copy_option_1" name="copy_option" value="full_copy" checked="checked"/>
	<label for="copy_option_1"><?php echo get_lang('FullCopy') ?></label>
	<br/>
	<input type="radio" class="checkbox" id="copy_option_2" name="copy_option" value="select_items"/>
	<label for="copy_option_2"><?php echo get_lang('LetMeSelectItems') ?></label>
	<br/>
	<br/>
	<?php echo get_lang('SameFilename') ?>
	<blockquote>
	<input type="radio" class="checkbox"  id="same_file_name_option_1" name="same_file_name_option" value="<?php echo FILE_SKIP ?>"/>
	<label for="same_file_name_option_1"><?php echo  get_lang('SameFilenameSkip') ?></label>
	<br/>
	<input type="radio" class="checkbox" id="same_file_name_option_2" name="same_file_name_option" value="<?php echo FILE_RENAME ?>"/>
	<label for="same_file_name_option_2"><?php echo get_lang('SameFilenameRename') ?></label>
	<br/>
	<input type="radio" class="checkbox"  id="same_file_name_option_3" name="same_file_name_option"  value="<?php echo FILE_OVERWRITE ?>"  checked="checked"/>
	<label for="same_file_name_option_3"><?php echo get_lang('SameFilenameOverwrite') ?></label>
	</blockquote>
	<br/>
	<input type="submit" value="<?php echo get_lang('CopyCourse') ?>"/>
	</form>
	<?php
	}

}
/*
==============================================================================
		FOOTER 
==============================================================================
*/ 
Display::display_footer();
?>
