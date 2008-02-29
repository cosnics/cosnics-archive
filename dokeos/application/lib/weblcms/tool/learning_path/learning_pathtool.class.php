<?php
/**
 * $Id$
 * Learning path tool
 * @package application.weblcms.tool
 * @subpackage learning_path
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/learning_pathbrowser.class.php';
/**
 * This tool allows a user to publish learning paths in his or her course.
 */
class LearningPathTool extends RepositoryTool
{
	// Inherited.
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			$this->display_header();
			Display :: display_not_allowed();
			$this->display_footer();
			return;
		}
		if (isset($_GET['admin']))
		{
			$_SESSION['wikiadmin'] = $_GET['admin'];
		}
		if ($_SESSION['wikiadmin'] && $this->is_allowed(ADD_RIGHT))
		{
			require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
			$pub = new LearningObjectPublisher($this, 'learning_path');
			$html[] = '<p><a href="' . $this->get_url(array('admin' => 0), true) . '"><img src="'.$this->get_parent()->get_path(WEB_IMG_PATH).'browser.gif" alt="'.Translation :: get_lang('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get_lang('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
			$this->display_header();
			echo implode("\n",$html);
			$this->display_footer();
		}
		else
		{
			$this->display_header();
			if($this->is_allowed(ADD_RIGHT))
			{
				echo '<p><a href="' . $this->get_url(array('admin' => 1), true) . '"><img src="'.$this->get_parent()->get_path(WEB_IMG_PATH).'publish.gif" alt="'.Translation :: get_lang('Publish').'" style="vertical-align:middle;"/> '.Translation :: get_lang('Publish').'</a></p>';
			}
			echo $this->perform_requested_actions();
			$browser = new LearningPathBrowser($this);
			echo $browser->as_html();
			$this->display_footer();
		}
	}
}
?>