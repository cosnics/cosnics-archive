<?php
/**
 * $Id: note.class.php 15410 2008-05-26 13:41:21Z Scara84 $
 * @package repository.learningobject
 * @subpackage note
 */
require_once dirname(__FILE__) . '/../../content_object.class.php';
/**
 * This class represents an note
 */
class Note extends ContentObject
{
	//Inherited
	function supports_attachments()
	{
		return true;
	}
}
?>