<?php
/**
 * @package application.weblcms
 * @subpackage browser.detailrenderer
 */
require_once dirname(__FILE__).'/../content_object_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/list_publication_feedback_list_renderer.class.php';
require_once dirname(__FILE__).'../../../content_object_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/content_object_form.class.php';
require_once Path :: get_application_path().'common/feedback_manager/feedback_manager.class.php';
/**
 * Renderer to display all details of learning object publication
 */
class ContentObjectPublicationDetailsRenderer extends ContentObjectPublicationListRenderer
{
	function ContentObjectPublicationDetailsRenderer($browser, $parameters = array (), $actions)
	{
		parent :: ContentObjectPublicationListRenderer($browser, $parameters, $actions);
		if($browser->get_parent()->get_course()->get_allow_feedback())
		{
			//$item = new ToolbarItem(Translation :: get('AddFeedback'), Theme :: get_common_image_path().'action_add.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_FEEDBACK, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
			//$browser->get_parent()->add_actionbar_item($item);
		}
	}

	/**
	 * Returns the HTML output of this renderer.
	 * @return string The HTML output
	 */
	function as_html()
	{
		$publication_id = $this->browser->get_publication_id();
		$dm = WeblcmsDataManager :: get_instance();
		$publication = $dm->retrieve_content_object_publication($publication_id);
		//$form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_CREATE,new AbstractContentObject('feedback',Session :: get_user_id()),'new_feedback','post',$this->browser->get_url(array('pid'=>$this->browser->get_publication_id())));
		$this->browser->get_parent()->set_parameter('pid',$publication_id);
		//$pub = new ContentObjectPublisher($this->browser->get_parent(), 'feedback', true);

		/*if($form->validate())
		{
			//creation feedback object
			$feedback = $form->create_content_object();
			//creation publication feedback object
			$publication_feedback= new ContentObjectPublicationFeedback(null, $feedback, $this->browser->get_course_id(), $publication->get_tool().'_feedback', $this->browser->get_publication_id(),$this->browser->get_user_id(), time(), 0, 0);
			$publication_feedback->set_show_on_homepage(0);
			$publication_feedback->create();
			//$this->browser->get_parent()->redirect();
			$html[] = Display :: normal_message(Translation :: get('FeedbackAdded'),true);
		}*/

		$html[] = '<h3>' . Translation :: get('ContentObjectPublicationDetails') . '</h3>';
		$html[] = $this->render_publication($publication);
		//dump($this->browser->get_parent()->get_course());
                
		//$html[] = $pub->as_html();
		$html[] = '<br />';
        $html[] = $this->get_feedback();
		return implode("\n", $html);
	}

	/**
	 * Renders a single publication.
	 * @param ContentObjectPublication $publication The publication.
	 * @return string The rendered HTML.
	 */
	function render_publication($publication,$first= false, $last= false)
	{
		$html = array ();
		$last_visit_date = $this->browser->get_last_visit_date();
		$icon_suffix = '';
		if($publication->is_hidden())
		{
			$icon_suffix = '_na';
		}
		elseif( $publication->get_publication_date() >= $last_visit_date)
		{
			$icon_suffix = '_new';
		}

		/*$html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path().'content_object/'.$publication->get_content_object()->get_icon_name().$icon_suffix.'.png);">';
		$html[] = '<div class="title'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_title($publication);
		$html[] = '</div>';
		$html[] = '<div class="description'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_description($publication);
		$html[] = $this->render_attachments($publication);
		$html[] = '</div>';
		$html[] = '<div class="publication_info'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_publication_information($publication);
		$html[] = '</div>';
		$html[] = '<div class="publication_actions">';
		$html[] = $this->render_publication_actions($publication,$first,$last);
		$html[] = '</div>';
		$html[] = '</div>';*/

		$html[] = '<div class="announcements level_1" style="background-image: url(' . Theme :: get_common_image_path().'content_object/'.$publication->get_content_object()->get_icon_name().$icon_suffix.'.png);">';
		$html[] = '<div class="title'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_title($publication);
		$html[] = '</div>';
		$html[] = '<div style="padding-top: 1px;" class="description'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_description($publication);
		$html[] = $this->render_attachments($publication);
		$html[] = '</div>';
		$html[] = '<div class="publication_info'. ($publication->is_visible_for_target_users() ? '' : ' invisible').'">';
		$html[] = $this->render_publication_information($publication);
		$html[] = '</div>';
		$html[] = '<div class="publication_actions">';
		$html[] = $this->render_publication_actions($publication,$first,$last);
		$html[] = '</div>';
		$html[] = '</div>';

		return implode("\n", $html);
	}

	function render_publication_actions($publication,$first,$last)
	{
		$html = array();
		$icons = array();

		$html[] = '<span style="white-space: nowrap;">';
		if ($this->is_allowed(DELETE_RIGHT))
		{
			$icons[] = $this->render_delete_action($publication);
		}
		if ($this->is_allowed(EDIT_RIGHT))
		{
			$icons[] = $this->render_edit_action($publication);
			$icons[] = $this->render_visibility_action($publication);
		}
		$html[] = implode('&nbsp;', $icons);
		$html[] = '</span>';
		return implode($html);
	}

	/**
	 * Renders the means to toggle visibility for the given publication.
	 * @param ContentObjectPublication $publication The publication.
	 * @return string The HTML rendering.
	 */
	function render_visibility_action($publication)
	{
		$visibility_url = $this->get_url(array (Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), 'details' => '1'), array(), true);
		if($publication->is_hidden())
		{
			$visibility_img = 'action_invisible.png';
		}
		elseif($publication->is_forever())
		{
			$visibility_img = 'action_visible.png';
		}
		else
		{
			$visibility_img = 'action_period.png';
			$visibility_url = 'javascript:void(0)';
		}
		$visibility_link = '<a href="'.$visibility_url.'"><img src="'.Theme :: get_common_image_path().$visibility_img.'"  alt=""/></a>';
		return $visibility_link;
	}

	/**
	 * Renders the means to edit the given publication.
	 * @param ContentObjectPublication $publication The publication.
	 * @return string The HTML rendering.
	 */
	function render_edit_action($publication)
	{
		$edit_url = $this->get_url(array (Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), 'details' => '1'), array(), true);
		$edit_link = '<a href="'.$edit_url.'"><img src="'.Theme :: get_common_image_path().'action_edit.png"  alt=""/></a>';
		return $edit_link;
	}
}
?>