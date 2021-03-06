<?php

require_once Path :: get_repository_path() . 'lib/content_object_form.class.php';
require_once Path :: get_repository_path() . 'lib/complex_content_object_item_form.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/content_object_repo_viewer.class.php';

class ForumDisplayForumSubforumEditorComponent extends ForumDisplayComponent
{
	function run()
	{
		if($this->get_parent()->get_parent()->is_allowed(EDIT_RIGHT))
		{
			$pid = Request :: get('pid');
			$subforum = Request :: get('subforum');
			$forum = Request :: get('forum');
			$is_subforum = Request :: get('is_subforum');

			if(!$pid || !$subforum)
			{
                //trail here
				$this->display_error_message(Translation :: get('NoParentSelected'));
			}

            $url = $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay::ACTION_EDIT_SUBFORUM,
										'pid' => $pid, 'subforum' => $subforum, 'is_subforum' => $is_subforum, 'forum' => $forum));

			$datamanager = RepositoryDataManager :: get_instance();
			$cloi = $datamanager->retrieve_complex_content_object_item($subforum);
			$content_object = $datamanager->retrieve_content_object($cloi->get_ref());

			$form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $url);

			if( $form->validate())
			{
				$form->update_content_object();
				if($form->is_version())
				{
					$old_id = $cloi->get_ref();
					$new_id = $content_object->get_latest_version()->get_id();
					$cloi->set_ref($new_id);
					$cloi->update();
					
					$children = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $old_id, ComplexContentObjectItem :: get_table_name()));
					while($child = $children->next_result())
					{
						$child->set_parent($new_id);
						$child->update();
					}
				}

				$this->my_redirect($pid, $is_subforum, $forum);
			}
			else
			{
				//trail here
				$form->display();
			}
		}
	}

	private function my_redirect($pid, $is_subforum, $forum)
	{
		$message = htmlentities(Translation :: get('SubforumEdited'));

		$params = array();
		$params['pid'] = $pid;
        $params[ComplexDisplay::PARAM_DISPLAY_ACTION] = ForumDisplay::ACTION_VIEW_FORUM;
		if($is_subforum)
			$params['forum'] = $forum;

		$this->redirect($message, '', $params);
	}

}
?>