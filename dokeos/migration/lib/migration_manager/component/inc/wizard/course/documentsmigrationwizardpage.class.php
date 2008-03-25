<?php
/**
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/../migrationwizardpage.class.php';
require_once dirname(__FILE__) . '/../../../../../migrationdatamanager.class.php'; 
require_once dirname(__FILE__) . '/../../../../../logger.class.php'; 
require_once dirname(__FILE__) . '/../../../../../import.class.php'; 
/**
 * Class for course documents migration
 * @author Van Wayenbergh David
 */
class DocumentsMigrationWizardPage extends MigrationWizardPage
{
	//private $logfile;
	//private $mgdm;
	//private $old_system;
	//private $failed_elements;
	private $include_deleted_files;
	//private $succes;
	//private $command_execute;
	
	function DocumentsMigrationWizardPage($page_name, $parent, $command_execute = false)
	{
		MigrationWizardPage :: MigrationWizardPage($page_name, $parent);
		$this->command_execute = $command_execute;
	}
	
	/**
	 * @return string Title of the page
	 */
	function get_title()
	{
		return Translation :: get_lang('Documents_title');
	}
	
	/**
	 * @return string Info of the page
	 */
	function get_info()
	{		
		for($i=0; $i<1; $i++)
		{
			$message = $message . '<br />' . $this->succes[$i] . ' ' . $this->get_message($i) . ' ' .
				Translation :: get_lang('migrated');
			
			if(count($this->failed_elements[$i]) > 0)
				$message = $message . '<br / >' . count($this->failed_elements[$i]) . ' ' .
					 $this->get_message($i) . ' ' . Translation :: get_lang('failed');
			
			foreach($this->failed_elements[$i] as $felement)
			{
				$message = $message . '<br />' . $felement ;
			}
			
			$message = $message . '<br />';
		}
		
		$message = $message . '<br />' . Translation :: get_lang('Dont_forget');
		
		return $message;
	}
	
	function next_step_info()
	{
		return Translation :: get_lang('Documents_info');
	}
	
	function get_message($index)
	{
		switch($index)
		{
			case 0: return Translation :: get_lang('Documents'); 
			default: return Translation :: get_lang('Documents'); 
		}
	}
	
	
	function buildForm()
	{
		$this->_formBuilt = true;
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get_lang('Next').' >>');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
	}
	
	function perform()
	{
		$logger = new Logger('migration.txt', true);
		
		if($logger->is_text_in_file('documents'))
		{
			echo(Translation :: get_lang('Documents') . ' ' .
				 Translation :: get_lang('already_migrated') . '<br />');
			return false;
		}
		
		if($this->command_execute)
			require(dirname(__FILE__) . '/../../../../../../settings.inc.php');
		else
			$exportvalues = $this->controller->exportValues();
			
		$this->old_system = $exportvalues['old_system'];
		$old_directory = $exportvalues['old_directory'];
		$this->include_deleted_files = $exportvalues['migrate_deleted_files'];
		
		//Create logfile
		$this->logfile = new Logger('documents.txt');
		$this->logfile->set_start_time();
		
		//Create migrationdatamanager
		$this->mgdm = MigrationDataManager :: getInstance($this->old_system, $old_directory);
		
		if(isset($exportvalues['migrate_documents']) && $exportvalues['migrate_documents'] == 1)
		{	
			//Migrate the calendar events and resources
			if(isset($exportvalues['migrate_courses']) && isset($exportvalues['migrate_users']) &&
					 $exportvalues['migrate_courses'] == 1 && $exportvalues['migrate_users'] == 1)
			{
				$courseclass = Import :: factory($this->old_system, 'course');
				$courses = array();
				$courses = $courseclass->get_all(array('mgdm' => $this->mgdm));
				
				foreach($courses as $i => $course)
				{
					if ($this->mgdm->get_failed_element('dokeos_main.course', $course->get_code()))
					{
						continue;
					}	
			
					$this->migrate_documents($course);
					unset($courses[$i]);
					flush();
				}
			}
			else
			{
				echo(Translation :: get_lang('Documents') . ' ' .
				     Translation :: get_lang('failed') . ' ' .
				     Translation :: get_lang('because') . ' ' . 
				     Translation :: get_lang('Users') . ' ' .
				     Translation :: get_lang('skipped') . '<br />');
				$this->logfile->add_message('Calendar events failed because users skipped');
				$this->succes[1] = 0;
			}
			
		}
		else
		{
			echo(Translation :: get_lang('Documents')
				 . ' ' . Translation :: get_lang('skipped') . '<br />');
			$this->logfile->add_message('Documents skipped');
			
			return false;
		}

		//Close the logfile
		$this->logfile->write_passed_time();
		$this->logfile->close_file();
		$logger->write_text('documents');
		$logger->close_file();
		return true;
	}
	
	/**
	 * Migrate the calendar events
	 */
	function migrate_documents($course)
	{
		$this->logfile->add_message('Starting migration documents for course ' . $course->get_code());
		
		$class_document = Import :: factory($this->old_system, 'document');
		$documents = array();
		$documents = $class_document->get_all_documents($course, $this->mgdm, $this->include_deleted_files);
		
		foreach($documents as $j => $document)
		{
			if($document->is_valid_document($course))
			{
				$lcms_document = $document->convert_to_new_document($course);
				if($lcms_document)
					$this->logfile->add_message('SUCCES: document added ( ' . $lcms_document->get_id() . ' )');
				$this->succes[0]++;
				unset($lcms_document);
			}
			else
			{
				$message = 'FAILED: Document is not valid ( ID ' . $document->get_id() . ' )';
				$this->logfile->add_message($message);
				$this->failed_elements[0][] = $message;
			}
			unset($documents[$j]);
		}
		

		$this->logfile->add_message('Documents migrated for course ' . $course->get_code());
	}

}
?>
