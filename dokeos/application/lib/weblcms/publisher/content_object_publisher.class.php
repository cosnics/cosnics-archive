<?php
/**
 * @package application.lib.profiler.publisher
 */
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_repository_path(). 'lib/content_object_display.class.php';
require_once dirname(__FILE__).'/../content_object_publication_form.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';

//require_once Path :: get_application_library_path() . 'publisher/component/publication_candidate_table/publication_candidate_table.class.php';

/**
 * This class represents a profile publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class ContentObjectPublisher
{
	private $parent;
	
	function ContentObjectPublisher($parent)
	{
		$this->parent = $parent;
	}
	
	function get_publications_form($ids)
	{
		//$ids = $_POST[PublicationCandidateTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
		
		$html = array();
		
		if(is_null($ids)) return '';
		
		if(!is_array($ids))
		{
			$ids = array($ids);
		}
		
		if (count($ids) > 0)
		{
			$condition = new InCondition(ContentObject :: PROPERTY_ID, $ids, ContentObject :: get_table_name());
			$content_objects = RepositoryDataManager :: get_instance()->retrieve_content_objects(null, $condition);
			//DokeosUtilities :: order_content_objects_by_title($content_objects);
			
			$html[] = '<div class="content_object padding_10">';
			$html[] = '<div class="title">'. Translation :: get('SelectedContentObjects') .'</div>';
			$html[] = '<div class="description">';
			$html[] = '<ul class="attachments_list">';
			
			while($content_object = $content_objects->next_result())
			{
				$html[] = '<li><img src="'.Theme :: get_common_image_path().'treemenu_types/'.$content_object->get_type().'.png" alt="'.htmlentities(Translation :: get(ContentObject :: type_to_class($content_object->get_type()).'TypeName')).'"/> '.$content_object->get_title().'</li>';
			}
			
			$html[] = '</ul>';
			$html[] = '</div>';
			$html[] = '</div>';
		}

		$form = new ContentObjectPublicationForm(ContentObjectPublicationForm :: TYPE_MULTI, $ids, $this->parent, $this->parent->with_mail_option(), $this->parent->get_course());
		if ($form->validate())
		{
			$publication = $form->create_content_object_publications();
			
			$parameters = array();
			//$parameters['pcattree'] = $publication->get_category_id();
			
			if (!$publication)
			{
				$message = Translation :: get('ObjectNotPublished');
			}
			else
			{
				$message = Translation :: get('ObjectPublished');
			}
			
			/*if($publication->get_tool() == 'introduction')
			{
				//$redirect_parms = array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE);
				$parameters['go'] = WeblcmsManager :: ACTION_VIEW_COURSE;
			}*/
			
			$parameters['tool_action'] = null;
			
			$this->parent->redirect($message, (!$publication ? true : false), $parameters);
		}
		else
		{
			$html[] = $form->toHtml();
		}
		
		return implode("\n", $html);
	}
}
?>