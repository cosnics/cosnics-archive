<?php
/**
 * @package repository.learningobject
 * @subpackage personal_message
 * 
 *  @author Hans De Bisschop
 *  @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/../../content_object.class.php';
/**
 * This class represents a personal message
 */
class PersonalMessage extends ContentObject
{
	//Inherited
	function supports_attachments()
	{
		return true;
	}

	function is_versionable()
	{
		return true;
	}
	
	function is_versioning_required()
	{
		return true;
	}
}
?>