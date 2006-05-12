<?php
/**
 * @package application.weblcms.tool
 */
require_once dirname(__FILE__).'/../learningobjectpublisher.class.php';
require_once dirname(__FILE__).'/../learningobjectpublishercomponent.class.php';
require_once dirname(__FILE__).'/publication_candidate_table/publicationcandidatetable.class.php';
/**
 * This class represents a learning object publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class LearningObjectBrowser extends LearningObjectPublisherComponent
{
	/*
	 * Inherited
	 */
	function as_html()
	{
		$publish_url_format = $this->get_url(array (LearningObjectPublisher :: PARAM_ACTION => 'publicationcreator', LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID => '__ID__'),false);
		$edit_and_publish_url_format = $this->get_url(array (LearningObjectPublisher :: PARAM_ACTION => 'publicationcreator', LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID => '__ID__', LearningObjectPublisher :: PARAM_EDIT => 1));
		$publish_url_format = str_replace('__ID__', '%d', $publish_url_format);
		$edit_and_publish_url_format = str_replace('__ID__', '%d', $edit_and_publish_url_format);
		$table = new PublicationCandidateTable($this->get_user_id(), $this->get_types(), $publish_url_format, $edit_and_publish_url_format);
		return $table->as_html();
	}
}
?>