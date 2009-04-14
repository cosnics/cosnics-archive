<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_repository_path().'lib/learning_object_display.class.php';
require_once Path :: get_repository_path().'lib/learning_object_form.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class WikiToolHistoryComponent extends WikiToolComponent
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
        
        //DATA
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

        $display = LearningObjectDisplay :: factory($wiki_page);

        $version_data = array();
		$versions = $wiki_page->get_learning_object_versions(); 
        $publication_attr = array();

        $this->display_header(new BreadcrumbTrail());
        $this->action_bar = $this->get_toolbar();
        echo '<br />' . $this->action_bar->as_html();
        echo '<h2> History for the ' .$wiki_page->get_title() .' page </h2>';
        echo $display->get_full_html();        

        foreach ($wiki_page->get_learning_object_versions() as $version)
        {
            // If this learning object is published somewhere in an application, these locations are listed here.
            $publications = $dm->get_learning_object_publication_attributes($this->get_user(), $version->get_id());
            $publication_attr = array_merge($publication_attr, $publications);
        }

        if (count($versions) >= 2)
        {            
            //DokeosUtilities :: order_learning_objects_by_id_desc($versions);
            foreach ($versions as $version)
            {                
                $version_entry = array();
                $version_entry['id'] = $version->get_id();
                if (strlen($version->get_title()) > 20)
                {
                    $version_entry['title'] = substr($version->get_title(), 0, 20) .'...';
                }
                else
                {
                    $version_entry['title'] = $version->get_title();
                }
                $version_entry['date'] = date('d M y, H:i', $version->get_creation_date());
                $version_entry['comment'] = $version->get_comment();
                //$version_entry['viewing_link'] = $this->get_learning_object_viewing_url($version);

                /*$delete_url = $this->get_learning_object_deletion_url($version, 'version');
                if (isset($delete_url))
                {
                    $version_entry['delete_link'] = $delete_url;
                }*/

                /*$revert_url = $this->get_learning_object_revert_url($version, 'version');
                if (isset($revert_url))
                {
                    $version_entry['revert_link'] = $revert_url;
                }*/

                $version_data[] = $display->get_version_as_html($version_entry);
            }

            $form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_COMPARE, $wiki_page, 'compare', 'post', $this->get_url(array(RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $this->object_id)), array('version_data' => $version_data));
            if ($form->validate())
            {
                $params = $form->compare_learning_object();
                $this->redirect(RepositoryManager :: ACTION_COMPARE_LEARNING_OBJECTS, null, null, false, $params);
            }
            $form->display();
        }

        echo $display->get_version_quota_as_html($version_data);
        
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
				Translation :: get('Discuss'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_DISCUSS, 'wiki_page_id' => $this->wiki_page_id, 'wiki_id' => $this->wiki_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
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