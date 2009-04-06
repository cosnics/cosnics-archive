<?php

require_once dirname(__FILE__) . '/../assessment_builder_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path() . '/lib/learning_object/assessment/assessment.class.php';

class AssessmentBuilderBrowserComponent extends AssessmentBuilderComponent
{
	function run()
	{
		$this->display_header(new BreadCrumbTrail());
		$assessment = RepositoryDataManager :: get_instance()->retrieve_learning_object(Request :: get(ComplexBuilder :: PARAM_ROOT_LO));
		$action_bar = $this->get_action_bar($assessment);
		
		echo '<br />';
		echo $action_bar->as_html();
		echo '<br />';
		echo $this->get_clo_table_html(false);
		
		$this->display_footer();
	}
	
	function get_action_bar($assessment)
	{
		$pub = Request :: get('publish');
		
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$types = $assessment->get_allowed_types();
		foreach($types as $type)
		{
			$url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_CREATE_CLOI, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_ROOT_LO => $assessment->get_id()));
			$action_bar->add_common_action(new ToolbarItem(Translation :: get(DokeosUtilities :: underscores_to_camelcase($type . 'TypeName')), Theme :: get_common_image_path().'learning_object/' . $type . '.png', $url));	
		}
		
		if($pub && $pub != '')
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $_SESSION['redirect_url']));
		}

		return $action_bar;
	}
}

?>