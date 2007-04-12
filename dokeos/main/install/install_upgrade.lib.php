<?php
// $Id$
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004-2005 Dokeos S.A.
	Copyright (c) Roan Embrechts, Vrije Universiteit Brussel
	Copyright (c) Bart Mollet, Hogeschool Gent

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
* This file contains functions used by the install and upgrade scripts.
* The current functions are used to
* - fill existing tables with data;
* - write a .htaccess file in the courses folder for extra security;
* - write the Dokeos config file containing important settings like database names
* and paswords and other options.
*
* Ideas for future additions:
* - a function get_old_version_settings to retrieve the config file settings
*   of older versions before upgrading.
==============================================================================
*/
/*
==============================================================================
		CONSTANTS
==============================================================================
*/
define("DOKEOS_MAIN_DATABASE_FILE", "dokeos_main.sql");

define("LANGUAGE_DATA_FILENAME", "language_data.csv");
define("ROLE_DATA_FILENAME", "default_roles.csv");
define("RIGHT_DATA_FILENAME", "rights_data.csv");
define("LOCATION_DATA_FILENAME", "location_data.csv");
define("ROLE_RIGHT_LOCATION_DATA_FILENAME", "role_right_location_data.csv");
define("COUNTRY_DATA_FILENAME", "country_data.csv");
define("SETTING_OPTION_DATA_FILENAME", "setting_option_data.csv");
define("SETTING_CURRENT_DATA_FILENAME", "setting_current_data.csv");
define("COURSES_HTACCESS_FILENAME", "htaccess.dist");
define("DOKEOS_CONFIG_FILENAME", "configuration.dist.php");

/*
==============================================================================
		FUNCTIONS
==============================================================================
*/

/**
 * Returns a string 'true' or 'false' according to the parameter variable.
 *
 * @param boolean $var the variable to convert
 * @return string 'true' when parameter is true, 'false' otherwise
 * @author Christophe Gesche
 */
function boolean_to_string($var)
{
	return $var?'true':'false';
}

/**
* We assume this function is called from install scripts that reside inside
* the install folder.
*/
function set_file_folder_permissions()
{
	@chmod('.',0755); //set permissions on install dir
	@chmod('..',0755); //set permissions on parent dir of install dir
	@chmod('language_data.csv',0755);
	@chmod('setting_current_data.csv',0755);
	@chmod('setting_option_data.csv',0755);
	@chmod('country_data.csv.csv',0755);
}

/**
* Fills the language table with all available languages.
*/
function fill_language_table($language_table)
{
	$file_path = dirname(__FILE__).'/'.LANGUAGE_DATA_FILENAME;
	$add_language_sql = "LOAD DATA INFILE '".mysql_real_escape_string($file_path)."' INTO TABLE $language_table FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\'';";
	@ mysql_query($add_language_sql);
}

/**
* Fills the role table with a set of default roles.
*/
function fill_role_table($role_table)
{
	$file_path = dirname(__FILE__).'/'.ROLE_DATA_FILENAME;
	$add_role_sql = "LOAD DATA INFILE '".mysql_real_escape_string($file_path)."' INTO TABLE $role_table FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\'';";
	@ mysql_query($add_role_sql);
}

/**
* Fills the rights table with all basic rights.
*/
function fill_right_table($right_table)
{
	$file_path = dirname(__FILE__).'/'.RIGHT_DATA_FILENAME;
	$add_right_sql = "LOAD DATA INFILE '".mysql_real_escape_string($file_path)."' INTO TABLE $right_table FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\'';";
	@ mysql_query($add_right_sql);
}

/**
* Fills the location table with the locations for the default course.
*/
function fill_location_table($location_table)
{
	$file_path = dirname(__FILE__).'/'.LOCATION_DATA_FILENAME;
	$add_location_sql = "LOAD DATA INFILE '".mysql_real_escape_string($file_path)."' INTO TABLE $location_table FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\'';";
	@ mysql_query($add_location_sql);
}

/**
* Fills the role-right-location-value table with settings for the default course.
*/
function fill_role_right_location_table($role_right_location_table)
{
	$file_path = dirname(__FILE__).'/'.ROLE_RIGHT_LOCATION_DATA_FILENAME;
	$add_data_sql = "LOAD DATA INFILE '".mysql_real_escape_string($file_path)."' INTO TABLE $role_right_location_table FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\'';";
	@ mysql_query($add_data_sql);
}

/**
* Fills the current settings table with the Dokeos default settings.
* After using the LOAD DATA INFILE instruction, the database stores some
* variables literally as '$variable'. The instructions after that replace
* that literal by the actual value of the variable.
*/
function fill_current_settings_table($current_settings_table, $installation_settings)
{
	$institutionForm = $installation_settings['institution_form'];
	$institutionUrlForm = $installation_settings['institution_url_form'];
	$campusForm = $installation_settings['campus_form'];
	$emailForm = $installation_settings['email_form'];
	$adminLastName = $installation_settings['admin_last_name'];
	$adminFirstName = $installation_settings['admin_first_name'];
	$languageForm = $installation_settings['language_form'];
	$allowSelfReg = $installation_settings['allow_self_registration'];
	$allowSelfRegProf = $installation_settings['allow_teacher_self_registration'];
	$adminPhoneForm = $installation_settings['admin_phone_form'];

	$file_path = dirname(__FILE__).'/'.SETTING_CURRENT_DATA_FILENAME;
	$add_setting_current_sql = "LOAD DATA INFILE '".mysql_real_escape_string($file_path)."' INTO TABLE $current_settings_table FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\'';";
	@ mysql_query($add_setting_current_sql);

	//replace literal '$variable' by the contents of variable $variable
	mysql_query("UPDATE $current_settings_table SET selected_value='$institutionForm' WHERE  selected_value='\$institutionForm'");
	mysql_query("UPDATE $current_settings_table SET selected_value='$institutionUrlForm' WHERE  selected_value='\$institutionUrlForm'");
	mysql_query("UPDATE $current_settings_table SET selected_value='$campusForm' WHERE  selected_value='\$campusForm'");
	mysql_query("UPDATE $current_settings_table SET selected_value='$emailForm' WHERE  selected_value='\$emailForm'");
	mysql_query("UPDATE $current_settings_table SET selected_value='$adminLastName' WHERE  selected_value='\$adminLastName'");
	mysql_query("UPDATE $current_settings_table SET selected_value='$adminFirstName' WHERE  selected_value='\$adminFirstName'");
	mysql_query("UPDATE $current_settings_table SET selected_value='$languageForm' WHERE  selected_value='\$languageForm'");
	mysql_query("UPDATE $current_settings_table SET selected_value='".trueFalse($allowSelfReg)."' WHERE  selected_value='\$allowSelfReg'");
	mysql_query("UPDATE $current_settings_table SET selected_value='".trueFalse($allowSelfRegProf)."' WHERE  selected_value='\$allowSelfRegProf'");
	mysql_query("UPDATE $current_settings_table SET selected_value='$adminPhoneForm' WHERE  selected_value='\$adminPhoneForm'");
}

/**
* Fills the table with the possible options for all settings.
*/
function fill_settings_options_table($settings_options_table)
{
	$file_path = dirname(__FILE__).'/'.SETTING_OPTION_DATA_FILENAME;
	$add_setting_option_sql = "LOAD DATA INFILE '".mysql_real_escape_string($file_path)."' INTO TABLE $settings_options_table FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\'';";
	@ mysql_query($add_setting_option_sql);
}

/**
* Fills the countries table with a list of countries.
*/
function fill_track_countries_table($track_countries_table)
{
	$file_path = dirname(__FILE__).'/'.COUNTRY_DATA_FILENAME;
	$add_country_sql = "LOAD DATA INFILE '".mysql_real_escape_string($file_path)."' INTO TABLE $track_countries_table FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\'';";
	@ mysql_query($add_country_sql);
}

/**
 * Adds a .htaccess file to the courses directory
 * @param string $url_append The path from your webroot to your dokeos root
 */
function write_courses_htaccess_file($url_append)
{
	$file_path = dirname(__FILE__).'/'.COURSES_HTACCESS_FILENAME;
	$content = file_get_contents($file_path);
	$content = str_replace('{DOKEOS_URL_APPEND_PATH}', $url_append, $content);
	$fp = @ fopen('../../courses/.htaccess', 'w');
	if ($fp)
	{
		fwrite($fp, $content);
		return fclose($fp);
	}
	return false;
}

/**
 * Writes the main Dokeos config file.
 * @param string $path Path to the config file
 */
function write_dokeos_config_file($path, $values)
{
	global $dokeos_version;
	global $urlAppendPath;

	$is_single_database = $values['database_single'];

	$file_path = dirname(__FILE__).'/'.DOKEOS_CONFIG_FILENAME;
	$content = file_get_contents($file_path);
	$config['{DOKEOS_VERSION}'] = $dokeos_version;
	$config['{DATE_GENERATED}'] = date('r');
	$config['{DATABASE_HOST}'] = $values['database_host'];
	$config['{DATABASE_USER}'] = $values['database_username'];
	$config['{DATABASE_PASSWORD}'] = $values['database_password'];
	$config['{TRACKING_ENABLED}'] = boolean_to_string($values['enable_tracking']);
	$config['{SINGLE_DATABASE}'] = boolean_to_string($is_single_database);
	$config['{COURSE_TABLE_PREFIX}'] = ($is_single_database ? 'crs_' : '');
	$config['{DATABASE_GLUE}'] = ($is_single_database ? '_' : '`.`');
	$config['{DATABASE_PREFIX}'] = $values['database_prefix'];
	$config['{DATABASE_MAIN}'] = $values["database_main_db"];
	$config['{DATABASE_STATS}'] = ($is_single_database ? $values["database_main_db"] : $values["database_tracking"]);
	$config['{DATABASE_SCORM}'] = ($is_single_database ? $values["database_main_db"] : $values["database_scorm"]);
	$config['{DATABASE_USERDB}'] = ($is_single_database ? $values["database_main_db"] : $values["database_user"]);
	$config['{DATABASE_REPOSITORY}'] = ($is_single_database ? $values["database_main_db"] : $values["database_repository"]);
	$config['{DATABASE_WEBLCMS}'] = ($is_single_database ? $values["database_main_db"] : $values["database_weblcms"]);
	$config['{DATABASE_PERSONALCALENDAR}'] = ($is_single_database ? $values["database_main_db"] : $values["database_personal_calendar"]);
	$config['{ROOT_WEB}'] = $values['platform_url'];
	$config['{ROOT_SYS}'] = str_replace('\\', '/', realpath($values['platform_url']).'/');
	$config['{URL_APPEND_PATH}'] = $urlAppendPath;
	$config['{GARBAGE_DIR}'] = str_replace("\\", "/", realpath("../garbage/")."/");
	$config['{PLATFORM_LANGUAGE}'] = $values['platform_language'];
	$config['{SECURITY_KEY}'] = md5(uniqid(rand().time()));
	$config['{ENCRYPT_PASSWORD}'] = boolean_to_string($values['encrypt_password']);
	foreach ($config as $key => $value)
	{
		$content = str_replace($key, $value, $content);
	}
	$fp = @ fopen($path, 'w');

	if (!$fp)
	{
		echo '<b><font color="red">Your script doesn\'t have write access to the config directory</font></b><br />
						<em>('.str_replace('\\', '/', realpath($path)).')</em><br /><br />
						You probably do not have write access on Dokeos root directory,
						i.e. you should <em>CHMOD 777</em> or <em>755</em> or <em>775</em>.<br /><br />
						Your problems can be related on two possible causes:<br />
						<ul>
						  <li>Permission problems.<br />Try initially with <em>chmod -R 777</em> and increase restrictions gradually.</li>
						  <li>PHP is running in <a href="http://www.php.net/manual/en/features.safe-mode.php" target="_blank">Safe-Mode</a>. If possible, try to switch it off.</li>
						</ul>
						<a href="http://www.dokeos.com/forum/" target="_blank">Read about this problem in Support Forum</a><br /><br />
						Please go back to step 5.
					    <p><input type="submit" name="step5" value="&lt; Back" /></p>
					    </td></tr></table></form></body></html>';

		exit ();
	}
	fwrite($fp, $content);
	fclose($fp);
}

/**
* Creates the structure of the main database and fills it
* with data. Placeholder symbols in the main database file
* have to be replaced by the settings entered by the user during installation.
*
* @param array $installation_settings list of settings entered by the user
*/
function load_main_database($installation_settings)
{
	$dokeos_main_sql_file_string = file_get_contents(DOKEOS_MAIN_DATABASE_FILE);

	//replace symbolic parameters with user-specified values
	foreach ($installation_settings as $key => $value)
	{
		$dokeos_main_sql_file_string = str_replace($key, mysql_real_escape_string($value), $dokeos_main_sql_file_string);
	}

	//split in array of sql strings
	$sql_instructions = array();
	$success = split_sql_file($sql_instructions, $dokeos_main_sql_file_string);

	//execute the sql instructions
	$count = count($sql_instructions);
	for ($i = 0; $i < $count; $i++)
	{
		$this_sql_query = $sql_instructions[$i]['query'];
		mysql_query($this_sql_query);
	}
}

/**
 * Function copied and adapted from phpMyAdmin 2.6.0 PMA_splitSqlFile (also GNU GPL)
 *
 * Removes comment lines and splits up large sql files into individual queries
 *
 * Last revision: September 23, 2001 - gandon
 *
 * @param   array    the splitted sql commands
 * @param   string   the sql commands
 * @param   integer  the MySQL release number (because certains php3 versions
 *                   can't get the value of a constant from within a function)
 *
 * @return  boolean  always true
 *
 * @access  public
 */
function split_sql_file(&$ret, $sql)
{
    // do not trim, see bug #1030644
    //$sql          = trim($sql);
    $sql          = rtrim($sql, "\n\r");
    $sql_len      = strlen($sql);
    $char         = '';
    $string_start = '';
    $in_string    = FALSE;
    $nothing      = TRUE;
    $time0        = time();

    for ($i = 0; $i < $sql_len; ++$i) {
        $char = $sql[$i];

        // We are in a string, check for not escaped end of strings except for
        // backquotes that can't be escaped
        if ($in_string) {
            for (;;) {
                $i         = strpos($sql, $string_start, $i);
                // No end of string found -> add the current substring to the
                // returned array
                if (!$i) {
                    $ret[] = $sql;
                    return TRUE;
                }
                // Backquotes or no backslashes before quotes: it's indeed the
                // end of the string -> exit the loop
                else if ($string_start == '`' || $sql[$i-1] != '\\') {
                    $string_start      = '';
                    $in_string         = FALSE;
                    break;
                }
                // one or more Backslashes before the presumed end of string...
                else {
                    // ... first checks for escaped backslashes
                    $j                     = 2;
                    $escaped_backslash     = FALSE;
                    while ($i-$j > 0 && $sql[$i-$j] == '\\') {
                        $escaped_backslash = !$escaped_backslash;
                        $j++;
                    }
                    // ... if escaped backslashes: it's really the end of the
                    // string -> exit the loop
                    if ($escaped_backslash) {
                        $string_start  = '';
                        $in_string     = FALSE;
                        break;
                    }
                    // ... else loop
                    else {
                        $i++;
                    }
                } // end if...elseif...else
            } // end for
        } // end if (in string)

        // lets skip comments (/*, -- and #)
        else if (($char == '-' && $sql_len > $i + 2 && $sql[$i + 1] == '-' && $sql[$i + 2] <= ' ') || $char == '#' || ($char == '/' && $sql_len > $i + 1 && $sql[$i + 1] == '*')) {
            $i = strpos($sql, $char == '/' ? '*/' : "\n", $i);
            // didn't we hit end of string?
            if ($i === FALSE) {
                break;
            }
            if ($char == '/') $i++;
        }

        // We are not in a string, first check for delimiter...
        else if ($char == ';') {
            // if delimiter found, add the parsed part to the returned array
            $ret[]      = array('query' => substr($sql, 0, $i), 'empty' => $nothing);
            $nothing    = TRUE;
            $sql        = ltrim(substr($sql, min($i + 1, $sql_len)));
            $sql_len    = strlen($sql);
            if ($sql_len) {
                $i      = -1;
            } else {
                // The submited statement(s) end(s) here
                return TRUE;
            }
        } // end else if (is delimiter)

        // ... then check for start of a string,...
        else if (($char == '"') || ($char == '\'') || ($char == '`')) {
            $in_string    = TRUE;
            $nothing      = FALSE;
            $string_start = $char;
        } // end else if (is start of string)

        elseif ($nothing) {
            $nothing = FALSE;
        }

        // loic1: send a fake header each 30 sec. to bypass browser timeout
        $time1     = time();
        if ($time1 >= $time0 + 30) {
            $time0 = $time1;
            header('X-pmaPing: Pong');
        } // end if
    } // end for

    // add any rest to the returned array
    if (!empty($sql) && preg_match('@[^[:space:]]+@', $sql)) {
        $ret[] = array('query' => $sql, 'empty' => $nothing);
    }

    return TRUE;
} // end of the 'PMA_splitSqlFile()' function

?>