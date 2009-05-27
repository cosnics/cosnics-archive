<?php
/**
 * @package application.weblcms.tool.assessment.component
 */

require_once dirname(__FILE__).'/glossary_viewer/glossary_viewer_table.class.php';
require_once Path :: get_library_path().'/html/action_bar/action_bar_renderer.class.php';
//require_once dirname(__FILE__).'/../../../browser/learningobjectpublicationcategorytree.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/browser/learningobjectpublicationcategorytree.class.php';

/**
 * Represents the view component for the assessment tool.
 *
 */
class GlossaryDisplayGlossaryViewerComponent extends GlossaryDisplayComponent
{
	private $action_bar;

	const PARAM_VIEW = 'view';
	const VIEW_LIST = 'list';
	const VIEW_TABLE = 'table';

	function run()
	{
		$this->action_bar = $this->get_action_bar();

		$trail = new BreadCrumbTrail();
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => GlossaryTool :: ACTION_VIEW_GLOSSARY, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))), WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication(Request :: get('pid'))->get_learning_object()->get_title()));
		$this->display_header($trail);

		echo $this->action_bar->as_html();

		if($this->get_view() == self :: VIEW_TABLE)
		{
			$table = new GlossaryViewerTable($this->get_parent()->get_parent(), $this->get_user(), Request :: get(Tool :: PARAM_PUBLICATION_ID));
			echo $table->as_html();
		}
		else
		{
			$dm = RepositoryDataManager :: get_instance();
            $children = $dm->retrieve_complex_learning_object_items(new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID))->get_learning_object()->get_id()));
            while($child = $children->next_result())
    		{
    			$lo = $dm->retrieve_learning_object($child->get_ref());
    			echo $this->display_learning_object($lo,$child);
    		}
		}

		$this->display_footer();
	}

	function display_learning_object($lo,$cloi)
	{
		$html[] = '<div class="title" style="background-color: #e6e6e6; border: 1px solid grey; padding: 5px; font-weight: bold; color: #666666">';
		$html[] = '<div style="padding-top: 1px; float: left">';
		$html[] = $lo->get_title();
		$html[] = '</div>';
		$html[] = '<div style="float: right">';
		$html[] = $this->get_actions($cloi);
		$html[] = '</div>';
		$html[] = '<div class="clear">&nbsp</div>';
		$html[] = '</div>';
		$html[] = '<div class="description">';
		$html[] = $lo->get_description();
		$html[] = '</div><br />';

		return implode("\n", $html);
	}

	function get_actions($cloi)
	{
		if($this->get_parent()->get_parent()->is_allowed(EDIT_RIGHT))
		{
			$actions[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_CLOI, Tool :: PARAM_COMPLEX_ID => $cloi->get_id(), Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path().'action_edit.png'
			);
		}

		if($this->get_parent()->get_parent()->is_allowed(DELETE_RIGHT))
		{
			$actions[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE_CLOI, Tool :: PARAM_COMPLEX_ID => $cloi->get_id(), Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path().'action_delete.png'
			);
		}

		return DokeosUtilities :: build_toolbar($actions);
	}

	function get_view()
	{
		$view = Request :: get(self :: PARAM_VIEW);
		if(!$view)
			$view = self :: VIEW_TABLE;

		return $view;
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		/*if(!isset($_GET['pid']))
		{
			$action_bar->set_search_url($this->get_url());
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(GlossaryTool :: PARAM_ACTION => GlossaryTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}*/

		//$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_CREATE_CLOI, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), 'type' => 'glossary_item', self :: PARAM_VIEW => self :: VIEW_TABLE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ShowAsTable'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => GlossaryTool :: ACTION_VIEW_GLOSSARY, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), self :: PARAM_VIEW => self :: VIEW_TABLE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ShowAsList'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => GlossaryTool :: ACTION_VIEW_GLOSSARY, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), self :: PARAM_VIEW => self :: VIEW_LIST)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));


		return $action_bar;
	}
}

?>