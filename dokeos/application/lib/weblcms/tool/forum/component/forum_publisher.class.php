<?php

require_once dirname(__FILE__) . '/../forum_tool.class.php';
require_once dirname(__FILE__) . '/../forum_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/content_object_publisher.class.php';

class ForumToolPublisherComponent extends ForumToolComponent
{
	function run()
	{
		if(!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
		$trail->add_help('courses forum tool');

		$object = Request :: get('object');
		$pub = new ContentObjectRepoViewer($this, 'forum', true);

		if(!isset($object))
		{
			$html[] =  $pub->as_html();
		}
		else
		{
			//$html[] = 'ContentObject: ';
			$publisher = new ContentObjectPublisher($pub);
			$html[] = $publisher->get_publications_form($object);
		}

		$this->display_header($trail, true);
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>