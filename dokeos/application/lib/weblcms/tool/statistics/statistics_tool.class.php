<?php
/**
 * $Id$
 * Statistics tool
 * @package application.weblcms.tool
 * @subpackage statistics
 */
require_once dirname(__FILE__).'/../tool.class.php';
require_once 'HTML/Table.php';
require_once 'data_renderer/bar_chart_data_renderer.class.php';

class StatisticsTool extends Tool
{
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		$this->display_header($trail);
		$dm = WeblcmsDataManager :: get_instance();
		$parent = $this->get_parent();
		foreach ($parent->get_registered_tools() as $tool)
		{
			$number_of_publications = $dm->count_learning_object_publications($this->get_course_id(),null,null,null,new EqualityCondition('tool',$tool->name));
			$data[htmlspecialchars(Translation :: get(Tool :: type_to_class($tool->name).'Title'))] = $number_of_publications;
		}
		$renderer = new BarChartDataRenderer($this,$data);
		$renderer->display();
		$this->display_footer();
	}
}
?>