<?php
/**
 * $Id$
 * Forum tool - topic browser
 * @package application.weblcms.tool
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../../learningobjectpublicationbrowser.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/learningobjectform.class.php';
require_once dirname(__FILE__).'/forumpublicationlistrenderer.class.php';
require_once dirname(__FILE__).'/forumtopiclistrenderer.class.php';

class ForumTopicBrowser extends LearningObjectPublicationBrowser
{
	private $forum_publication;
	function ForumTopicBrowser($parent, $types)
	{
		parent :: __construct($parent, 'forum_topic');
		$renderer = new ForumTopicListRenderer($this);
		$this->set_publication_list_renderer($renderer);
		$datamanager = WeblcmsDataManager :: get_instance();
		$forum_id = $this->get_parameter('forum');
		$this->forum_publication = $datamanager->retrieve_learning_object_publication($forum_id);
	}

	function get_publications($from, $count, $column, $direction)
	{
		$forum = $this->forum_publication->get_learning_object();
		$topics = $forum->get_forum_topics();
		$index = 0;
		while ($topic = $topics->next_result())
		{
			$first = ($index == 0);
			$last = ($index == $topics->size() - 1);
			$forum_table_row = array();
			if($this->is_allowed(EDIT_RIGHT) || $this->is_allowed(DELETE_RIGHT))
			{
				$forum_table_row[] = $topic->get_id();
			}
			$forum_url = $this->get_url(array('topic'=>$topic->get_id()));
			$forum_table_row[] = '<a href="'.$forum_url.'">'.$topic->get_title().'</a>';
			$forum_table_row[] = ''.$topic->get_reply_count();
			$author = api_get_user_info($topic->get_owner_id());
			$forum_table_row[] = $author['firstName'].' '.$author['lastName'];
			$last_post = $topic->get_last_post();
			$last_post_author = api_get_user_info($last_post->get_owner_id());
			$forum_table_row[] = date('r',$last_post->get_creation_date()).' '.get_lang('By').' '.$last_post_author['firstName'].' '.$last_post_author['lastName'];
			$visible_publications[] = $forum_table_row;
			$index++;
		}
		return $visible_publications;
	}
	function get_publication_count()
	{
		$forum = $this->forum_publication->get_learning_object();
		return $forum->get_topic_count();
	}
	function as_html()
	{
		$forum = $this->forum_publication->get_learning_object();
		$html = '<b>'.$forum->get_title().'</b>';
		if($_GET['action'] == 'newtopic')
		{
			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, new AbstractLearningObject('forum_topic', $this->get_user_id()), 'create', 'post', $this->get_url(array('action'=>'newtopic')));
			if (!$form->validate())
			{
				$html .=  $form->toHTML();
			}
			else
			{
				$topic = $form->create_learning_object();
				$topic->set_parent_id($forum->get_id());
				$topic->update();
				$course = $this->get_course_id();
				$html .= Display::display_normal_message(get_lang('TopicAdded'),true);
			}
		}
		else
		{
			$html .= '<a href="'.$this->get_url(array('action'=>'newtopic')).'">'.get_lang('NewTopic').'</a>';
		}
		$html .= $this->listRenderer->as_html();
		return $html;
	}
}
?>