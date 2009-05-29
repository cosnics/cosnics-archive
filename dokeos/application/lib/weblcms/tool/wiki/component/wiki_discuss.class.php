<?php

/*
 * This is the discuss page. Here a user can add feedback to a wiki_page.
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path().'/lib/complex_display/complex_display.class.php';
require_once Path :: get_repository_path().'/lib/complex_display/wiki/component/wiki_parser.class.php';
require_once Path :: get_repository_path().'lib/learning_object_pub_feedback.class.php';

class WikiToolDiscussComponent extends WikiToolComponent
{
	private $action_bar;
    private $wiki_page_id;
    private $wiki_id;
    private $cid;
    private $fid;
    private $publication_id;
    private $links;
    const TITLE_MARKER = '<!-- /title -->';
    const DESCRIPTION_MARKER = '<!-- /description -->';


	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

        $dm = RepositoryDataManager :: get_instance();
        $rm = new RepositoryManager();

        /*
         * publication and complex object id are requested.
         * These are used to retrieve
         *  1) the complex object ( reference is stored )
         *  2) the learning object ( actual inforamation about a wiki_page is stored here )
         *
         */
        $this->publication_id = Request :: get('pid');
        $this->cid = Request :: get('cid');

        $complexeObject = $dm->retrieve_complex_learning_object_item($this->cid);
        if(isset($complexeObject))
        {
            $this->wiki_page_id = $complexeObject->get_ref();
            $this->wiki_id = $complexeObject->get_parent();
        }
        $wiki_page = $dm->retrieve_learning_object($this->wiki_page_id);
        $this->links = RepositoryDataManager :: get_instance()->retrieve_learning_object($this->wiki_id)->get_links();

        $trail = new BreadcrumbTrail();
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI, Tool :: PARAM_PUBLICATION_ID => $this->publication_id)), DokeosUtilities::truncate_string($_SESSION['wiki_title'],20)));
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI_PAGE, Tool :: PARAM_PUBLICATION_ID => $this->publication_id, Tool :: PARAM_COMPLEX_ID => $this->cid)), DokeosUtilities::truncate_string($wiki_page->get_title(),20)));
        $trail->add_help('courses wiki tool');

        $this->display_header($trail, true);

        $this->action_bar = $this->get_toolbar();
        echo  '<div style="float:left; width: 135px;">'.$this->action_bar->as_html().'</div>';
        echo  '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">'.Translation :: get('DiscussThe'). ' ' .$wiki_page->get_title().' ' . Translation :: get('Page') .'<hr style="height:1px;color:#4271B5;width:100%;"></div>';

        /*
         *  We make use of the existing LearningObjectDisplay class, changing the type to wiki_page
         */
        $display = LearningObjectDisplay :: factory($wiki_page);
        /*
         *  Here we make the call to the wiki_parser.
         *  For more information about the parser, please read the information in the wiki_parser class.
         */

        $parser = new WikiToolParserComponent(Request :: get('pid'), $this->get_course_id(), $display->get_full_html());
        $parser->parse_wiki_text();

        $this->set_script();
        echo '<a href="#" onclick="showhide();">'. Translation :: get('Show/HideContent').'</a><br /><br />';
        echo '<div id="content" style="display:inline;">'.$parser->get_wiki_text().'</div><br />';

        /*
         *  We make use of the existing condition framework to show the data we want.
         *  If the publication id , and the compled object id are equal to the ones passed the feedback will be shown.
         */

        if(isset($this->cid)&& isset($this->publication_id))
        {
            $conditions[] = new EqualityCondition(LearningObjectPubFeedback :: PROPERTY_PUBLICATION_ID, $this->publication_id);
            $conditions[] = new EqualityCondition(LearningObjectPubFeedback :: PROPERTY_CLOI_ID, $this->cid);
            $condition = new AndCondition($conditions);
            $feedbacks = $dm->retrieve_learning_object_pub_feedback($condition);

            while($feedback = $feedbacks->next_result())
            {
                if($i == 0)
                {
                    echo '<div style="font-size:18px;">' . Translation :: get('Feedback') .'</div><hr>';
                    echo $this->show_add_feedback().'<br /><br />';
                }
                $this->fid = $feedback->get_feedback_id();
                /*
                 *  We retrieve the learning object, because that one contains the information we want to show.
                 *  We then display it using the LearningObjectDisplay and setting the type to feedback
                 */
                $feedback_display = $dm->retrieve_learning_object($this->fid);
                echo $this->show_feedback($feedback_display);
                $i++;

            }
        }

        echo '</div>';
        $this->display_footer();
    }

//    function run()
//	{
//		if(!$this->is_allowed(VIEW_RIGHT))
//		{
//			Display :: not_allowed();
//			return;
//		}
//
//        $this->action_bar = $this->get_toolbar();
//
//        $cd = ComplexDisplay :: factory($this);
//        $cd->run();
//
//        switch($cd->get_action())
//        {
//            case WikiDisplay :: ACTION_DISCUSS:
//                Events :: trigger_event('discuss', 'weblcms', array('course' => Request :: get('course'), Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), Tool :: PARAM_COMPLEX_ID => Request :: get('cid')));
//                break;
//        }
//    }
//
//	function get_url($parameters = array (), $filter = array(), $encode_entities = false)
//	{
//        //$parameters[Tool :: PARAM_ACTION] = GlossaryTool :: ACTION_BROWSE_GLOSSARIES;
//		return $this->get_parent()->get_url($parameters, $filter, $encode_entities);
//	}
//
//    function redirect($message = null, $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false)
//	{
//        //$parameters[Tool :: PARAM_ACTION] = GlossaryTool :: ACTION_BROWSE_GLOSSARIES;
//		$this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities);
//	}


    function build_feedback_actions()
    {
        $actions[] = array(
			'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE_FEEDBACK, 'fid' => $this->fid, 'cid' => $this->cid, 'pid' => $this->publication_id)),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png',
            'confirm' => true
			);

        $actions[] = array(
			'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_FEEDBACK, 'fid' => $this->fid, 'cid' => $this->cid, 'pid' => $this->publication_id)),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_image_path().'action_edit.png'
			);

        return DokeosUtilities :: build_toolbar($actions);

    }

    function show_add_feedback()
    {
        $actions[] = array(
			'href' => $this->get_url(array(WikiTool :: PARAM_ACTION => Tool :: ACTION_FEEDBACK_CLOI, 'pid' => $this->publication_id, 'cid' => $this->cid)),
			'label' => Translation :: get('AddFeedback'),
			'img' => Theme :: get_common_image_path().'action_add.png',
            'confirm' => false
			);

        return DokeosUtilities :: build_toolbar($actions);

    }

    function get_toolbar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_WIKI);

		$action_bar->set_search_url($this->get_url());

        //PAGE ACTIONS
        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('CreateWikiPage'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_CREATE_PAGE, 'pid' => $this->publication_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_CLOI, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Delete'),Theme :: get_common_image_path().'action_delete.png', $this->get_url(array(WikiTool :: PARAM_ACTION => Tool:: ACTION_DELETE_CLOI, 'pid' => $this->publication_id,'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL,true
			)
		);
        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('AddFeedback'), Theme :: get_common_image_path().'action_add.png', $this->get_url(array(WikiTool :: PARAM_ACTION => Tool :: ACTION_FEEDBACK_CLOI, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Discuss'), Theme :: get_common_image_path().'action_users.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_DISCUSS, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);


         $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('BrowseWiki'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool ::ACTION_VIEW_WIKI, 'pid' => $this->publication_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        //INFORMATION
        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('History'), Theme :: get_common_image_path().'action_versions.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        /*$action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('NotifyChanges'), Theme :: get_common_image_path().'action_subscribe.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);*/


        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Statistics'), Theme :: get_common_image_path().'action_reporting.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_PAGE_STATISTICS, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        //NAVIGATION
        if(!empty($this->links))
        {
            $p = new WikiToolParserComponent($this->publication_id,$this->get_course()->get_id(),$this->links);
            $toolboxlinks = $p->handle_toolbox_links($this->links);
            $this->links = explode(';',$this->links);
            $i=0;

            foreach($toolboxlinks as $link)
            {
                if(substr_count($link,'class="does_not_exist"'))
                {
                    $action_bar->add_navigation_link(
                    new ToolbarItem(
                        $p->get_title_from_wiki_tag($this->links[$i],true), null, $this->get_url(array(Tool :: PARAM_ACTION => WikiTool ::ACTION_CREATE_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link), 'title' =>$p->get_title_from_wiki_tag($this->links[$i],false))), ToolbarItem :: DISPLAY_ICON_AND_LABEL,null,'does_not_exist'
                    ));
                }
                else
                {
                    $action_bar->add_navigation_link(
                    new ToolbarItem(
                        $p->get_title_from_wiki_tag($this->links[$i],true), null, $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_VIEW_WIKI_PAGE, Tool :: PARAM_PUBLICATION_ID => $p->get_pid_from_url($link), Tool :: PARAM_COMPLEX_ID =>$p->get_cid_from_url($link) )), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                    ));
                }
                $i++;
            }
        }


		return $action_bar;
	}

    private function show_feedback($object)
    {
        $creationDate = $object->get_creation_date();

        $html = array();
		$html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_image_path() . 'learning_object/' .$object->get_icon_name().($object->is_latest_version() ? '' : '_na').'.png);">';
        $html[] = '<div class="title">'. htmlentities($object->get_title()) .' | '.htmlentities(date("F j, Y, H:i:s",$creationDate )).'</div>';
		$html[] = self::TITLE_MARKER;
		$html[] = $object->get_description();
		$html[] = self::DESCRIPTION_MARKER;
        $html[] = '<div style="float:right">'.$this->build_feedback_actions().'</div>';
        $html[] = '<br />';
		$html[] = '</div>';

        return implode("\n",$html);
    }

    private function set_script()
    {
        echo ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/showhide_content.js');;
    }
}

?>
