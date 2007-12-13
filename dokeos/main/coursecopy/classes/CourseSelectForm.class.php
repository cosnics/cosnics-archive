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
require_once ('Course.class.php');
/**
 * Class to show a form to select resources
 * @author Bart Mollet <digitaal-leren@hogent.be>
 * @package dokeos.backup
 */
class CourseSelectForm
{
	/**
	 * Display the form
	 * @param array $hidden_fiels Hidden fields to add to the form.
	 */
	function display_form($course, $hidden_fields = null)
	{
		$resource_titles[RESOURCE_EVENT] = get_lang('Events');
		$resource_titles[RESOURCE_ANNOUNCEMENT] = get_lang('Announcements');
		$resource_titles[RESOURCE_DOCUMENT] = get_lang('Documents');
		$resource_titles[RESOURCE_LINK] = get_lang('Links');
		$resource_titles[RESOURCE_COURSEDESCRIPTION] = get_lang('CourseDescription');
		$resource_titles[RESOURCE_FORUM] = get_lang('Forums');
		$resource_titles[RESOURCE_QUIZ] = get_lang('Tests');
		$resource_titles[RESOURCE_LEARNPATH] = get_lang('Learnpaths');
		$resource_titles[RESOURCE_SCORM] = get_lang('Scorm');
		$resource_titles[RESOURCE_TOOL_INTRO] = get_lang('ToolIntro');
?>
		<script type="text/javascript">
		/* <![CDATA[ */
			function exp(item) {
				el = document.getElementById('div_'+item);
				if (el.style.display=='none'){
					el.style.display='';
					document.getElementById('img_'+item).src='../img/1.gif';
				}
				else{
					el.style.display='none';
					document.getElementById('img_'+item).src='../img/0.gif';
				}
			}
			function setCheckbox(type,value) {
 				d = document.course_select_form;
 				for (i = 0; i < d.elements.length; i++) {
   					if (d.elements[i].type == "checkbox") {
						var name = d.elements[i].attributes.getNamedItem('name').nodeValue;
 						if( name.indexOf(type) > 0 || type == 'all' ){
						     d.elements[i].checked = value;
						}
   					}
 				}
			}
		/* ]]> */
		</script>		
		<?php

		echo '<p>';
		echo get_lang('SelectResources');
		echo '</p>';
		echo '<form method="post" name="course_select_form">';
		echo '<input type="hidden" name="action" value="course_select_form"/>';
		echo '<input type="hidden" name="course" value="'.base64_encode(serialize($course)).'"/>';
		foreach ($course->resources as $type => $resources)
		{
			if (count($resources) > 0)
			{
				switch ($type)
				{
					case RESOURCE_LINKCATEGORY :
					case RESOURCE_FORUMCATEGORY :
					case RESOURCE_FORUMPOST :
					case RESOURCE_FORUMTOPIC :
					case RESOURCE_QUIZQUESTION:
						break;
					default :
						echo ' <img id="img_'.$type.'" src="../img/1.gif" onclick="javascript:exp('."'$type'".');" >';
						echo ' <b  onclick="javascript:exp('."'$type'".');" >'.$resource_titles[$type].'</b><br/>';
						echo '<div id="div_'.$type.'">';
						echo '<blockquote>';
						echo "[<a href=\"#\" onclick=\"javascript:setCheckbox('$type',true);\" >".get_lang('All')."</a> - <a href=\"#\" onclick=\"javascript:setCheckbox('$type',false);\" >".get_lang('None')."</a>]";
						echo '<br/>';
						foreach ($resources as $id => $resource)
						{
							echo '<input type="checkbox" name="resource['.$type.']['.$id.']" id="resource['.$type.']['.$id.']"/>';
							echo ' <label for="resource['.$type.']['.$id.']">';
							$resource->show();
							echo '</label>';
							echo '<br/>';
							echo "\n";
						}
						echo '</blockquote>';
						echo '</div>';
						echo '<script type="text/javascript">exp('."'$type'".')</script>';
				}
			}
		}
		if (is_array($hidden_fields))
		{
			foreach ($hidden_fields as $key => $value)
			{
				echo "\n";
				echo '<input type="hidden" name="'.$key.'" value="'.$value.'"/>';
			}
		}
		echo '<br/><input type="submit" value="'.get_lang('Ok').'"/>';
		echo '</form>';
	}
	/**
	 * Get the posted course
	 * @return course The course-object with all resources selected by the user
	 * in the form given by display_form(...)
	 */
	function get_posted_course()
	{
		$course = unserialize(base64_decode($_POST['course']));
		foreach ($course->resources as $type => $resources)
		{
			switch ($type)
			{
				case RESOURCE_LINKCATEGORY :
				case RESOURCE_FORUMCATEGORY :
				case RESOURCE_FORUMPOST :
				case RESOURCE_FORUMTOPIC :
				case RESOURCE_QUIZQUESTION :
					break;
				case RESOURCE_DOCUMENT:
					// Mark folders to import which are not selected by the user to import,
					// but in which a document was selected.
					$documents = $_POST['resource'][RESOURCE_DOCUMENT];
					foreach($resources as $id => $obj)
					{
						if( $obj->file_type == 'folder' && ! isset($_POST['resource'][RESOURCE_DOCUMENT][$id]))
						{
							foreach($documents as $id_to_check => $post_value)
							{
								$obj_to_check = $resources[$id_to_check];
								$shared_path_part = substr($obj_to_check->path,0,strlen($obj->path));
								if($id_to_check != $id && $obj->path == $shared_path_part)
								{
									$_POST['resource'][RESOURCE_DOCUMENT][$id] = 1;	
									break;
								}	
							}	
						}	
					}
				default :
					foreach ($resources as $id => $obj)
					{
						$resource_is_used_elsewhere = $course->is_linked_resource($obj);
						// check if document is in a quiz (audio/video)
						if( $type == RESOURCE_DOCUMENT && $course->has_resources(RESOURCE_QUIZ))
						{
							foreach($course->resources[RESOURCE_QUIZ] as $qid => $quiz)
							{
								if($quiz->media == $id)
								{
									$resource_is_used_elsewhere = true;	
								}	
							}
						}
						if (!isset ($_POST['resource'][$type][$id]) && !$resource_is_used_elsewhere)
						{
							unset ($course->resources[$type][$id]);
						}
					}
			}
		}
		return $course;
	}
}
?>