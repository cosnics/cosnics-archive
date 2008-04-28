<?php
/**
 * @package main
 * @subpackage tracking
 */
require_once dirname(__FILE__).'/archivewizardpage.class.php';
/**
 * Page in the archive wizard in which confirmation is ask for the given options to the
 * user.
 */
class ConfirmationArchiveWizardPage extends ArchiveWizardPage
{
	/**
	 * Returns the title of this page
	 * @return string the title
	 */
	function get_title()
	{
		return Translation :: get('Archive_confirmation_title');
	}
	
	/**
	 * Returns the info of this page
	 * @return string the info
	 */
	function get_info()
	{
		return Translation :: get('Archive_confirmation_info');
	}
	
	/**
	 * Builds the form that must be visible on this page
	 */
	function buildForm()
	{
		$this->_formBuilt = true;
		$exports = $this->controller->exportValues();
		
		foreach($exports as $key => $export)
		{	
			if(substr($key, strlen($key) - strlen('event'), strlen($key)) == 'event')
			{
				$this->addElement('html', '<div style="margin-top: 3px;">' . $key . '</div>');
				$eventname = substr($key, 0, strlen($key) - strlen('event'));
				
				foreach($exports as $key2 => $export2)
				{
					if((strpos($key2, $eventname) !== false) && ($key2 != $key))
					{
						$id = substr($key2, strlen($eventname . 'event'));
						$tracker = $this->get_parent()->retrieve_tracker_registration($id);
						$this->addElement('html', '<div style="margin-top: 3px; left: 20px; position: relative;">' . $tracker->get_class() . '</div>');
					}
				}
			}
		}
		
		$sd = $exports['start_date'];
		$ed = $exports['end_date'];
		$startdate = $sd['Y'] . '-' . $sd['M'] .'-' . $sd['d'];
		$enddate = $ed['Y'] . '-' . $ed['M'] .'-' . $ed['d'];
		$period = $exports['period'];
		
		$this->addElement('html', '<div style="margin-top: 13px">' . Translation :: get('Start_date') . ' ' . $startdate . '</div>');
		$this->addElement('html', '<div style="margin-top: 3px">' . Translation :: get('End_date') . ' ' . $enddate . '</div>');
		$this->addElement('html', '<div style="margin-top: 3px">' . Translation :: get('Period') . ' ' . $period . ' ' . Translation :: get('Days') . '</div>');
		
		$prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get('Previous'), 'style=\'margin-top: 20px;\'');
		$prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Confirm').' >>', 'style=\'margin-top: 20px;\'');
		$this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
		$this->setDefaultAction('next');
	}
}
?>