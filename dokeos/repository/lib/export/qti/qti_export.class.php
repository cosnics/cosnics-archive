<?php

require_once dirname(__FILE__).'/../content_object_export.class.php';
require_once dirname(__FILE__).'/assessment/assessment_qti_export.class.php';
require_once dirname(__FILE__).'/question/question_qti_export.class.php';

/**
 * Exports learning object to the dokeos learning object format (xml)
 */
class QtiExport extends ContentObjectExport
{
	private $rdm;
	
	function QtiExport($content_object)
	{
		$this->rdm = RepositoryDataManager :: get_instance();
		parent :: __construct($content_object);	
	}
	
	public function export_content_object()
	{
		$exporter = self :: factory_qti($this->get_content_object());
		return $exporter->export_content_object();
	}
	
	static function factory_qti($content_object)
	{
		switch ($content_object->get_type())
		{
			case 'assessment':
				$exporter = new AssessmentQtiExport($content_object);
				break;
			case 'survey':
				$exporter = new AssessmentQtiExport($content_object);
				break;
			default:
				$exporter = QuestionQtiExport :: factory_question($content_object);
				break;
		}
		return $exporter;
	}

}
?>