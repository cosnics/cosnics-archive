<?php

require_once dirname(__FILE__) . '/../wiki_builder_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/wiki/wiki.class.php';
require_once dirname(__FILE__) . '/browser/wiki_browser_table_cell_renderer.class.php';

class WikiBuilderBrowserComponent extends WikiBuilderComponent
{
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array('builder_action' => null, Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('Repository')));
		$trail->add(new Breadcrumb($this->get_url(array(ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id())), $this->get_root_lo()->get_title()));
		$trail->add_help('repository wiki builder');

		$this->display_header($trail);
		$wiki = $this->get_root_lo();
		$action_bar = $this->get_action_bar($wiki);

		echo '<br />';
		if($action_bar)
		{
			echo $action_bar->as_html();
			echo '<br />';
		}

		$display = ContentObjectDisplay :: factory($this->get_root_lo());
		echo $display->get_full_html();

		echo '<br />';
		echo $this->get_creation_links($wiki);
		echo '<div class="clear">&nbsp;</div><br />';

		echo $this->get_clo_table_html(false, null, new WikiBrowserTableCellRenderer($this->get_parent(), $this->get_clo_table_condition()));

		$this->display_footer();
	}
}

?>
