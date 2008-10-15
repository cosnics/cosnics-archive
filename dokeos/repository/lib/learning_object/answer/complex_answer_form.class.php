<?php
/**
 * @package repository.learningobject
 * @subpackage answer
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item_form.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__) . '/complex_answer.class.php';

class ComplexAnswerForm extends ComplexLearningObjectItemForm
{
	const TOTAL_PROPERTIES = 1;
	// Inherited
    protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$this->addElement('text', ComplexAnswer :: PROPERTY_SCORE, Translation :: get('Score'));
    }
    // Inherited
    protected function build_editing_form()
    {
		parent :: build_editing_form();
    	$this->addElement('text', ComplexAnswer :: PROPERTY_SCORE, Translation :: get('Score'));
	}
	// Inherited
	function setDefaults($defaults = array ())
	{
		$cloi = $this->get_complex_learning_object_item();
	
		if (isset ($cloi))
		{
			$defaults[ComplexAnswer :: PROPERTY_SCORE] = $cloi->get_score();
		}
		parent :: setDefaults($defaults);
	}

	function set_csv_values($valuearray)
	{	
		$defaults[ComplexAnswer :: PROPERTY_SCORE] = $valuearray[0];
		parent :: set_values($defaults);
	}

	// Inherited
	function create_complex_learning_object_item()
	{ 
		$cloi = $this->get_complex_learning_object_item();
		$values = $this->exportValues();
		$cloi->set_score($values[ComplexAnswer :: PROPERTY_SCORE]); 
		return parent :: create_complex_learning_object_item();
	}
	// Inherited
	function update_complex_learning_object_item()
	{
		$cloi = $this->get_complex_learning_object_item();
		$values = $this->exportValues();
		$cloi->set_score($values[ComplexAnswer :: PROPERTY_SCORE]);
		return parent :: update_complex_learning_object_item();
	}
}

?>