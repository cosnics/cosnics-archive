<?php

require_once Path :: get_common_path() . 'html/formvalidator/form_validator_page.class.php';


abstract class AssessmentViewerWizardPage extends FormValidatorPage
{
	/**
	 * The parent in which the wizard runs.
	 */
	private $parent;

	/**
	 * Constructor
	 * @param string $name A unique name of this page in the wizard
	 * @param Tool $parent The parent in which the wizard
	 * runs.
	 */
	public function AssessmentViewerWizardPage($name,$parent)
	{
		$this->parent = $parent;
		parent :: FormValidatorPage($name, 'post');
		$this->updateAttributes(array('action'=> $parent->get_parent()->get_url()));
	}

	/**
	 * Returns the parent in which this wizard runs
	 * @return Component
	 */
	function get_parent()
	{
		return $this->parent;
	}
}
?>