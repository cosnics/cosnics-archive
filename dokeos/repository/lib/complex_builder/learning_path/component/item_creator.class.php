<?php

require_once dirname(__FILE__) . '/../learning_path_builder_component.class.php';
require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

class LearningPathBuilderItemCreatorComponent extends LearningPathBuilderComponent
{
	private $rdm;

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add_help('repository learnpath builder');

		$object = Request :: get('object');
		$root_lo = Request :: get(ComplexBuilder :: PARAM_ROOT_LO);
		$cloi_id = Request :: get(ComplexBuilder :: PARAM_CLOI_ID);
		$publish = Request :: get('publish');
		$type = $rtype = Request :: get(ComplexBuilder :: PARAM_TYPE);

		$this->rdm = RepositoryDataManager :: get_instance();

		if($this->get_cloi())
		{
			$lo = $this->rdm->retrieve_content_object($this->get_cloi()->get_ref());
		}
		else
		{
			$lo = $this->get_root_lo();
		}

		$exclude = $this->retrieve_used_items($this->get_root_lo()->get_id());
		$exclude[] = $this->get_root_lo()->get_id();

		if(!$type)
		{
			$type = $lo->get_allowed_types();
		}

		$pub = new ComplexRepoViewer($this, $type);
		if($rtype)
		{
			$pub->set_parameter(ComplexBuilder :: PARAM_TYPE, $rtype);
		}

		$pub->set_parameter(ComplexBuilder :: PARAM_ROOT_LO, $root_lo);
		$pub->set_parameter(ComplexBuilder :: PARAM_CLOI_ID, $cloi_id);
		$pub->set_parameter('publish', $publish);
		$pub->set_excluded_objects($exclude);
		$pub->parse_input();

		if(!isset($object))
		{
			$html[] = '<p><a href="' . $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO, ComplexBuilder :: PARAM_ROOT_LO => $root_lo, ComplexBuilder :: PARAM_CLOI_ID => $cloi_id, 'publish' => $publish)) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			if(!is_array($object))
			{
				$object = array($object);
			}

			$rdm = RepositoryDataManager :: get_instance();

			foreach($object as $obj)
			{
				$content_object = new LearningPathItem();
				$content_object->set_title('learning_path_item');
				$content_object->set_description('learning_path_item');
				$content_object->set_owner_id($this->get_user_id());
				$content_object->set_reference($obj);
				$content_object->set_parent_id(0);

				$content_object->create();

				$cloi = ComplexContentObjectItem :: factory('learning_path_item');
				$cloi->set_ref($content_object->get_id());

				$parent = $root_lo;
				if($cloi_id)
				{
					$parent_cloi = $rdm->retrieve_complex_content_object_item($cloi_id);
					$parent = $parent_cloi->get_ref();
				}

				$cloi->set_parent($parent);
				$cloi->set_display_order($rdm->select_next_display_order($parent));
				$cloi->set_user_id($this->get_user_id());
				$cloi->create();
			}

			$this->redirect(Translation :: get('ObjectAdded'), false, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO, ComplexBuilder :: PARAM_ROOT_LO => $root_lo, ComplexBuilder :: PARAM_CLOI_ID => $cloi_id, 'publish' => Request :: get('publish')));
		}

		$this->display_header($trail);
		echo '<br />' . implode("\n",$html);
		$this->display_footer();
	}

	private function retrieve_used_items($parent)
	{
		$items = array();

		$clois = $this->rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent, ComplexContentObjectItem :: get_table_name()));
		while($cloi = $clois->next_result())
		{
			if($cloi->is_complex())
			{
				$items[] = $cloi->get_ref();
				$items = array_merge($items,$this->retrieve_used_items($cloi->get_ref()));
			}
		}

		return $items;
	}
}

?>
