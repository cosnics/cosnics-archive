<?php

require_once Path :: get_repository_path() . 'lib/content_object_form.class.php';

class ForumManagerEditorComponent extends ForumManagerComponent
{
	function run()
	{
		if($this->is_allowed(EDIT_RIGHT))
		{
			$pid = Request :: get(ForumManager :: PARAM_FORUM_PUBLICATION);

			$datamanager = ForumDataManager :: get_instance();
			$publication = $datamanager->retrieve_forum_publication($pid);
			$content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($publication->get_forum_id());

			$form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_EDIT, ForumManager :: PARAM_FORUM_PUBLICATION => $pid)));

			$trail = new BreadcrumbTrail();

			$trail->add(new BreadCrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_EDIT, ForumManager :: PARAM_FORUM_PUBLICATION => $pid)), Translation :: get('Edit')));
			$trail->add_help('forum general');

			if( $form->validate() )
			{
				$succes = $form->update_content_object();

				if($form->is_version())
				{
					$old_id = $publication->get_forum_id();
					$publication->set_forum_id($content_object->get_latest_version()->get_id());
					$publication->update();
					
					RepositoryDataManager :: get_instance()->set_new_clo_version($old_id, $publication->get_forum_id());
				}

				$message = $succes ? Translation :: get('ForumUpdated') : Translation :: get('ForumNotUpdated');
				$this->redirect($message, !$succes, array(ForumManager :: PARAM_ACTION => null));
			}
			else
			{
				$this->display_header($trail, true);
				$form->display();
				$this->display_footer();
			}
		}
	}
}
?>