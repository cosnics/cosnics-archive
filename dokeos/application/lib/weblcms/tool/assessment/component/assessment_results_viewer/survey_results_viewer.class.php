<?php
require_once dirname(__FILE__).'/results_viewer.class.php';

class SurveyResultsViewer extends ResultsViewer
{
	function to_html()
	{
		return 'Survey results viewer';
	}
}
?>