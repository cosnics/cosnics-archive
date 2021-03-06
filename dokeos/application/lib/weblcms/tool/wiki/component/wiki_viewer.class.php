<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/complex_display.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/wiki/wiki_display.class.php';

class WikiToolViewerComponent extends WikiToolComponent
{
    private $cd;
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
        }
		$this->set_parameter(Tool :: PARAM_ACTION, WikiTool :: ACTION_VIEW_WIKI);
        $this->cd = ComplexDisplay :: factory($this, 'wiki');
        $o = WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get('pid'));
        if(empty($o))
        $o = RepositoryDataManager :: get_instance()->retrieve_content_object(Request :: get('pid'));
        else
        $o = $o->get_content_object();
        $this->cd->set_root_lo($o);
        $this->display_header(new BreadcrumbTrail());
        $this->cd->run();
        $this->display_footer();
    }

    
}
?>
