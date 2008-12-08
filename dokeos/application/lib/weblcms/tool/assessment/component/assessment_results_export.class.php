<?php
require_once dirname(__FILE__).'/assessment_results_export_form/results_export_form.class.php';
require_once dirname(__FILE__).'/assessment_results_export_form/export.class.php';

class AssessmentToolResultsExportComponent extends AssessmentToolComponent
{
	
	private $rdm;
	private $wdm;
	
	function run()
	{
		if (!$this->is_allowed(VIEW_RIGHT) || !$this->is_allowed(EDIT_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
		$toolbar = $this->get_toolbar();
		
		if (isset($_GET[AssessmentTool :: PARAM_USER_ASSESSMENT]))
		{
			$id = $_GET[AssessmentTool :: PARAM_USER_ASSESSMENT];
			$type = 'user_assessment';
			$export_form = new AssessmentResultsExportForm($this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_EXPORT_RESULTS, AssessmentTool :: PARAM_USER_ASSESSMENT => $id)));
		} 
		else
		{
			$id = $_GET[AssessmentTool :: PARAM_ASSESSMENT];
			$type = 'assessment';
			$export_form = new AssessmentResultsExportForm($this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_EXPORT_RESULTS, AssessmentTool :: PARAM_ASSESSMENT => $id)));
		}
		
		if ($export_form->validate())
		{
			$values = $export_form->exportValues();
			$filetype = $values['filetype'];
			$this->export($type, $id, $filetype);
		}
		else
		{
			$this->display_header($trail);
			echo $toolbar->as_html();
			echo $export_form->toHtml();
			$this->display_footer();
		}
	}
	
	function export($type, $id, $filetype)
	{
		$exporter = Export :: factory($filetype, 'export_'.$type.$id);
		$results_exporter = ResultsExport :: factory($filetype);
		
		$this->rdm = RepositoryDataManager :: get_instance();
		$this->wdm = WeblcmsDataManager :: get_instance();
		
		$data = $results_exporter->export_results($type, $id);
		$exporter->write_to_file($data);
	}
	
	
}
?>