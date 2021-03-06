<?php

/*
 * This is the discuss page. Here a user can add feedback to a wiki_page.
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path().'lib/complex_display/complex_display.class.php';
require_once Path :: get_repository_path().'lib/complex_display/wiki/component/wiki_parser.class.php';
require_once Path :: get_repository_path().'lib/content_object_pub_feedback.class.php';
require_once Path :: get_repository_path().'lib/complex_display/wiki/wiki_display.class.php';

class WikiDisplayWikiDiscussComponent extends WikiDisplayComponent
{
	private $action_bar;
    private $wiki_page_id;
    private $cid;
    private $fid;
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
        
        $this->cid = Request :: get('selected_cloi');

        $complexeObject = $dm->retrieve_complex_content_object_item($this->cid);
        if(isset($complexeObject))
        {
            $this->wiki_page_id = $complexeObject->get_ref();$dm->retrieve_content_object($this->wiki_page_id);
        }
        $wiki_page = $dm->retrieve_content_object($this->wiki_page_id);
        
        $this->action_bar = $this->get_parent()->get_toolbar($this,$this->get_root_lo()->get_id(),$this->get_root_lo(), $this->cid);//$this->get_toolbar();
        echo '<div id="trailbox2" style="padding:0px;">'.$this->get_parent()->get_breadcrumbtrail()->render().'<br /><br /><br /></div>';
        echo  '<div style="float:left; width: 135px;">'.$this->action_bar->as_html().'</div>';
        echo  '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">'.Translation :: get('DiscussThe'). ' ' .$wiki_page->get_title().' ' . Translation :: get('Page') .'<hr style="height:1px;color:#4271B5;width:100%;"></div>';

        /*
         *  We make use of the existing ContentObjectDisplay class, changing the type to wiki_page
         */
        $display = ContentObjectDisplay :: factory($wiki_page);
        /*
         *  Here we make the call to the wiki_parser.
         *  For more information about the parser, please read the information in the wiki_parser class.
         */

        $parser = new WikiDisplayWikiParserComponent($this->get_root_lo()->get_id(), $display->get_full_html(),$this->cid);
        $parser->parse_wiki_text();

        $this->set_script();
        echo '<a id="showhide" href="#">['. Translation :: get('Hide').']</a><br /><br />';
        echo '<div id="content" style="line-height: 110%;">'.$parser->get_wiki_text().'</div><br />';

        /*
         *  We make use of the existing condition framework to show the data we want.
         *  If the publication id , and the compled object id are equal to the ones passed the feedback will be shown.
         */

        if(isset($this->cid)&& $this->get_root_lo()->get_id() != null)
        {
            if(Request :: get('application') == 'wiki')
            {
                $conditions[] = new EqualityCondition(WikiPubFeedback :: PROPERTY_WIKI_PUBLICATION_ID, Request :: get('wiki_publication'));
                $conditions[] = new EqualityCondition(WikiPubFeedback :: PROPERTY_CLOI_ID, $this->cid);
                $condition = new AndCondition($conditions);
                $feedbacks = WikiDataManager :: get_instance()->retrieve_wiki_pub_feedbacks($condition);
            }
            else
            {
                $conditions[] = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_PUBLICATION_ID, Request :: get('pid'));
                $conditions[] = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_CLOI_ID, $this->cid);
                $condition = new AndCondition($conditions);
                $feedbacks = $dm->retrieve_content_object_pub_feedback($condition);
            }
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
                 *  We then display it using the ContentObjectDisplay and setting the type to feedback
                 */
                $feedback_display = $dm->retrieve_content_object($this->fid);
                echo $this->show_feedback($feedback_display);
                $i++;

            }
        }

        echo '</div>';
        
    }

    function build_feedback_actions()
    {
        $actions[] = array(
			'href' => $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_DELETE_FEEDBACK, 'fid' => $this->fid, 'selected_cloi' => $this->cid, 'pid' => Request :: get('pid'), 'wiki_publication' => Request :: get('wiki_publication'))),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png',
            'confirm' => true
			);

        $actions[] = array(
			'href' => $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_EDIT_FEEDBACK, 'fid' => $this->fid, 'selected_cloi' => $this->cid, 'pid' => Request :: get('pid'), 'wiki_publication' => Request :: get('wiki_publication'))),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_image_path().'action_edit.png'
			);

        return DokeosUtilities :: build_toolbar($actions);

    }

    function show_add_feedback()
    {
        $actions[] = array(
			'href' => $this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_FEEDBACK_CLOI, 'pid' => Request :: get('pid'), 'wiki_publication' => Request :: get('wiki_publication'), 'selected_cloi' => $this->cid)),
			'label' => Translation :: get('AddFeedback'),
			'img' => Theme :: get_common_image_path().'action_add.png',
            'confirm' => false
			);

        return DokeosUtilities :: build_toolbar($actions);

    }

    private function show_feedback($object)
    {
        $creationDate = $object->get_creation_date();

        $html = array();
		$html[] = '<div class="content_object" style="background-image: url('.Theme :: get_common_image_path() . 'content_object/' .$object->get_icon_name().($object->is_latest_version() ? '' : '_na').'.png);">';
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
