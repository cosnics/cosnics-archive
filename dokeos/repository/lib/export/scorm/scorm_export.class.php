<?php

require_once dirname(__FILE__).'/../content_object_export.class.php';
require_once dirname(__FILE__).'/learning_path/learning_path_scorm_export.class.php';

/**
 * Exports learning object to the dokeos learning object format (xml)
 */
class ScormExport extends ContentObjectExport
{
	private $rdm;
	
	function ScormExport($content_object)
	{
		$this->rdm = RepositoryDataManager :: get_instance();
		parent :: __construct($content_object);	
	}
	
	public function export_content_object()
	{
		$exporter = self :: factory_scorm($this->get_content_object());
		return $exporter->export_content_object();
	}
	
	function get_rdm()
	{
		return $this->rdm;
	}
	
	static function factory_scorm($content_object)
	{
		switch ($content_object->get_type())
		{
			case 'learning_path':
				$exporter = new LearningPathScormExport($content_object);
				break;
			default:
				$exporter = null;
				break;
		}
		return $exporter;
	}
}
?>