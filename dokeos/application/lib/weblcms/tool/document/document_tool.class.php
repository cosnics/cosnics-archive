<?php
/**
 * $Id: announcement_tool.class.php 16471 2008-10-09 07:46:00Z SSJMaglor $
 * Document tool
 * @package application.weblcms.tool
 * @subpackage announcement
 */

require_once dirname(__FILE__).'/document_tool_component.class.php';
/**
 * This tool allows a user to publish announcements in his or her course.
 */
class DocumentTool extends Tool
{
	const ACTION_VIEW_DOCUMENTS = 'view';
	const ACTION_DOWNLOAD = 'download';
	const ACTION_ZIP_AND_DOWNLOAD = 'zipanddownload';
	const ACTION_SLIDESHOW = 'slideshow';
	
	/**
	 * Inherited.
	 */
	function run()
	{
		$action = $this->get_action();
		$component = parent :: run();
		
		if($component) return;
		
		switch ($action)
		{
			case self :: ACTION_VIEW_DOCUMENTS :
				$component = DocumentToolComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_PUBLISH :
				$component = DocumentToolComponent :: factory('Publisher', $this);
				break;
			case self :: ACTION_DOWNLOAD :
				$component = DocumentToolComponent :: factory('Downloader', $this);
				break;
			case self :: ACTION_ZIP_AND_DOWNLOAD :
				$component = DocumentToolComponent :: factory('ZipAndDownload', $this);
				break;
			case self :: ACTION_SLIDESHOW :
				$component = DocumentToolComponent :: factory('Slideshow', $this);
				break;
			default :
				$component = DocumentToolComponent :: factory('Viewer', $this);
		}
		$component->run();
	}
	
	static function get_allowed_types()
	{
		return array('document');
	}
}
?>