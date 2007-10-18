<?php // $Id$
/*
vim: set expandtab tabstop=4 shiftwidth=4:
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================
*/
/**
==============================================================================
*	This is the file display library for Dokeos.
*	Include/require it in your code to use its functionality.
*
*	@package dokeos.library
==============================================================================
*/



/*
==============================================================================
		FILE DISPLAY FUNCTIONS
==============================================================================
*/
/**
 * Define the image to display for each file extension.
 * This needs an existing image repository to work.
 *
 * @author - Hugues Peeters <peeters@ipm.ucl.ac.be>
 * @param  - $file_name (string) - Name of a file
 * @return - The gif image to chose
 */
function choose_image($file_name)
{
	static $type, $image;

	/* TABLES INITIALISATION */
	if (!$type || !$image)
	{
		$type['word'      ] = array('doc', 'dot',  'rtf', 'mcw',  'wps');
		$type['web'       ] = array('htm', 'html', 'htx', 'xml',  'xsl',  'php');
		$type['image'     ] = array('gif', 'jpg',  'png', 'bmp',  'jpeg');
		$type['audio'     ] = array('wav', 'mid',  'mp2', 'mp3',  'midi', 'sib');
		$type['video'     ] = array('mp4', 'mov',  'rm',  'pls',  'mpg',  'mpeg', 'au');
		$type['excel'     ] = array('xls', 'xlt',  'xls', 'xlt');
		$type['compressed'] = array('zip', 'tar',  'rar', 'gz');
		$type['code'      ] = array('js',  'cpp',  'c',   'java', 'phps');
		$type['acrobat'   ] = array('pdf');
		$type['powerpoint'] = array('ppt');
		$type['flash'     ] = array('fla', 'swf');
		$type['text'      ] = array('txt');
		$type['oo_writer' ] = array('odt','sxw');
		$type['oo_calc'   ] = array('ods','sxc');
		$type['oo_impress'] = array('odp','sxi');
		$type['oo_draw'   ] = array('odg','sxd');

		$image['word'      ] = 'doc.gif';
		$image['web'       ] = 'html.gif';
		$image['image'     ] = 'gif.gif';
		$image['audio'     ] = 'wav.gif';
		$image['video'     ] = 'video.gif';
		$image['excel'     ] = 'xls.gif';
		$image['compressed'] = 'zip.gif';
		$image['code'      ] = 'txt.gif';
		$image['acrobat'   ] = 'pdf.gif';
		$image['powerpoint'] = 'ppt.gif';
		$image['flash'     ] = 'flash.gif';
		$image['text'      ] = 'txt.gif';
		$image['oo_writer' ] = 'odt.gif';
		$image['oo_calc'   ] = 'ods.gif';
		$image['oo_impress'] = 'odp.gif';
		$image['oo_draw'   ] = 'odg.gif';
	}

	/* FUNCTION CORE */
	$extension = array();
	if (ereg('\.([[:alnum:]]+)$', $file_name, $extension))
	{
		$extension[1] = strtolower($extension[1]);

		foreach ($type as $generic_type => $extension_list)
		{
			if (in_array($extension[1], $extension_list))
			{
				return $image[$generic_type];
			}
		}
	}

	return 'defaut.gif';
}

/**
 * Transform the file size in a human readable format.
 *
 * @param  - $file_size (int) - Size of the file in bytes
 * @return - A human readable representation of the file size
 */
function format_file_size($file_size)
{
	if($file_size >= 1073741824)
	{
		$file_size = round($file_size / 1073741824 * 100) / 100 . 'G';
	}
	elseif($file_size >= 1048576)
	{
		$file_size = round($file_size / 1048576 * 100) / 100 . 'M';
	}
	elseif($file_size >= 1024)
	{
		$file_size = round($file_size / 1024 * 100) / 100 . 'k';
	}
	else
	{
		$file_size = $file_size . 'B';
	}

	return $file_size;
}

/**
 * Transform a UNIX time stamp in human readable format date.
 *
 * @author - Hugues Peeters <peeters@ipm.ucl.ac.be>
 * @param  - $date - UNIX time stamp
 * @return - A human readable representation of the
 */
function format_date($date)
{
	return date('d.m.Y', $date);
}

/**
 * Transform the file path to a URL.
 *
 * @param  - $file_path (string) - Relative local path of the file on the hard disk
 * @return - Relative url
 */
function format_url($file_path)
{
	$path_component = explode('/', $file_path);

	$path_component = array_map('rawurlencode', $path_component);

	return implode('/', $path_component);
}




/**
 * Calculates the total size of a directory by adding the sizes (that
 * are stored in the database) of all files & folders in this directory.
 *
 * @param 	string  $path
 * @param 	boolean $can_see_invisible
 * @return 	Total size
 */
function get_total_folder_size($path, $can_see_invisible = false)
{
	$table_itemproperty = Database::get_course_table(LAST_TOOL_EDIT_TABLE);
	$table_document = Database::get_course_table(DOCUMENT_TABLE);
	$tool_document = TOOL_DOCUMENT;

	$visibility_rule = 'props.visibility ' . ($can_see_invisible ? '<> 2' : '= 1');

	$sql = <<<EOQ
SELECT SUM(size)
	FROM $table_itemproperty AS props, $table_document AS docs
	WHERE docs.id = props.ref
		AND props.tool = '$tool_document'
		AND path LIKE '$path/%'
		AND $visibility_rule
EOQ;

	$result = api_sql_query($sql,__FILE__,__LINE__);

	if($result && mysql_num_rows($result) != 0)
	{
		$row = mysql_fetch_row($result);
		return $row[0] == null ? 0 : $row[0];
	}
	else
	{
		return 0;
	}
}
?>