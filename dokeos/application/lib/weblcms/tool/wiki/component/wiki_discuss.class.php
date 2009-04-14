<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class WikiToolDiscussComponent extends WikiToolComponent
{
	private $action_bar;
    private $wiki_page_id;
    private $wiki_id;
    private $cid;

	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

        $this->wiki_page_id = Request :: get('wiki_page_id');
        $this->wiki_id = Request :: get('wiki_id');
        $dm = RepositoryDataManager :: get_instance();
        $wiki_page = $dm->retrieve_learning_object($this->wiki_page_id);
        /*
         * complex object id needed to delete / update
         */
        $condition = New EqualityCondition(ComplexLearningObjectItem :: PROPERTY_REF, $wiki_page->get_id());
        $cloi = $dm->retrieve_complex_learning_object_items($condition)->as_array();
        $this->cid = $cloi[0]->get_id();

		$this->display_header(new BreadcrumbTrail());

        $this->action_bar = $this->get_toolbar();
        echo '<br />' . $this->action_bar->as_html();
        
        echo '<h2> Discuss the ' .$wiki_page->get_title() .' page </h2>';

        $this->display_footer();
    }

    function get_toolbar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('View'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI_PAGE, 'wiki_page_id' => $this->wiki_page_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Create Wiki Page'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_CREATE_PAGE, 'wiki_id' => $this->wiki_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
		$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_EDIT_PAGE, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_DELETE_PAGE, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);        

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('History'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'wiki_page_id' => $this->wiki_page_id, 'wiki_id' => $this->wiki_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Notify Changes'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'wiki_page_id' => $this->wiki_page_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);


		return $action_bar;
	}
}

?>