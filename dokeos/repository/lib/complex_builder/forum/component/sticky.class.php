<?php

require_once dirname(__FILE__) . '/../forum_builder_component.class.php';
require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

class ForumBuilderStickyComponent extends ForumBuilderComponent
{
	function run()
	{
        $rdm = RepositoryDataManager::get_instance();

        $topic = $rdm->retrieve_complex_content_object_item(Request :: get(ComplexBuilder::PARAM_SELECTED_CLOI_ID));

        if($topic->get_type() == 1)
        {
            $topic->set_type(null);
            $message = 'TopicUnStickied';
        }else
        {
            $topic->set_type(1);
            $message = 'TopicStickied';
        }
        $topic->update();

        

        $this->redirect($message, '', array(ComplexBuilder::PARAM_ROOT_LO => Request :: get(ComplexBuilder::PARAM_ROOT_LO), ComplexBuilder::PARAM_BUILDER_ACTION => ComplexBuilder::ACTION_BROWSE_CLO));
	}
}

?>
