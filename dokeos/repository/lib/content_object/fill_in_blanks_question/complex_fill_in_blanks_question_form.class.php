<?php
/**
 * @package repository.learningobject
 * @subpackage complex_assessment
 */
require_once dirname(__FILE__) . '/../../complex_content_object_item_form.class.php';
require_once dirname(__FILE__) . '/complex_fill_in_blanks_question.class.php';
/**
 * This class represents a form to create or update complex assessments
 */
class ComplexFillInBlanksQuestionForm extends ComplexContentObjectItemForm
{
	protected function build_creation_form()
    {
    	parent :: build_creation_form();
    	$elements = $this->get_elements();
    	foreach($elements as $element)
    	{
    		$this->addElement($element);
    	}
    }
    // Inherited
    protected function build_editing_form()
    {
		parent :: build_editing_form();
    	$elements = $this->get_elements();
    	foreach($elements as $element)
    	{
    		$this->addElement($element);
    	}
	}
	
	public function get_elements()
	{
		$elements[] = $this->createElement('text', ComplexFillInBlanksQuestion :: PROPERTY_WEIGHT, Translation :: get('Weight'), array("size" => "50"));
		return $elements;
	}
	
	function setDefaults($defaults = array ())
	{
		$defaults = array_merge($defaults, $this->get_default_values());
		parent :: setDefaults($defaults);
	}
	
	function get_default_values()
	{
		$cloi = $this->get_complex_content_object_item();
	
		if (isset ($cloi))
		{
			$defaults[ComplexFillInBlanksQuestion :: PROPERTY_WEIGHT] = $cloi->get_weight() ? $cloi->get_weight() : 0;
		}
		
		return $defaults;
	}

	// Inherited
	function create_complex_content_object_item()
	{ 
		$cloi = $this->get_complex_content_object_item();
		$values = $this->exportValues();
		$cloi->set_weight($values[ComplexFillInBlanksQuestion :: PROPERTY_WEIGHT]); 
		return parent :: create_complex_content_object_item();
	}
	
	function create_cloi_from_values($values)
	{
		$cloi = $this->get_complex_content_object_item();
		$cloi->set_weight($values[ComplexFillInBlanksQuestion :: PROPERTY_WEIGHT]); 
		return parent :: create_complex_content_object_item();
	}
	
	function update_cloi_from_values($values)
	{
		$cloi = $this->get_complex_content_object_item();
		$cloi->set_weight($values[ComplexFillInBlanksQuestion :: PROPERTY_WEIGHT]); 
		return parent :: update_complex_content_object_item();
	}
	
	// Inherited
	function update_complex_content_object_item()
	{
		$cloi = $this->get_complex_content_object_item();
		$values = $this->exportValues();
		$cloi->set_weight($values[ComplexFillInBlanksQuestion :: PROPERTY_WEIGHT]);
		return parent :: update_complex_content_object_item();
	}
}
?>