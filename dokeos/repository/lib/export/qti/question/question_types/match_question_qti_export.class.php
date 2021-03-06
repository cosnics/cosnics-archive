<?php
require_once dirname(__FILE__).'/../question_qti_export.class.php';

class MatchQuestionQtiExport extends QuestionQtiExport
{
	function export_content_object()
	{
		$question = $this->get_content_object();
		$answers = $question->get_options();
		
		$item_xml = array();
		$item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q'.$question->get_id().'" title="'.$question->get_title().'" adaptive="false" timeDependent="false">';
		$item_xml[] = '<responseDeclaration identifier="RESPONSE" cardinality="single" baseType="string">';
		$item_xml[] = $this->get_response_xml($answers); 
		$item_xml[] = '</responseDeclaration>';
		$item_xml[] = $this->get_outcome_xml();
		$item_xml[] = $this->get_interaction_xml($answers);
		$item_xml[] = '<responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct" />';
		$item_xml[] = '</assessmentItem>';
		
		$xml = implode("", $item_xml);
		
		return parent :: create_qti_file($xml);
	}
	
	function get_response_xml($answers)
	{
		$response_xml = array();
		$mapping_xml = array();
		
		$response_xml[] = '<correctResponse>';
		$mapping_xml[] = '<mapping defaultValue="0">';
		
		foreach($answers as $answer)
		{
			$response_xml[] = '<value>' . htmlspecialchars($answer->get_value()) . '</value>';
			$mapping_xml[] = '<mapEntry mapKey="' . htmlspecialchars($answer->get_value()) . '" mappedValue="' . $answer->get_weight() . '" />';
		}
		
		$response_xml[] = '</correctResponse>';
		$mapping_xml[] = '</mapping>';
		
		$response = implode('', $response_xml) . implode('', $mapping_xml);
		
		return $response;
	}
	
	function get_outcome_xml()
	{
		$outcome_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float">';
		$outcome_xml[] = '<defaultValue>';
		$outcome_xml[] = '<value>0</value>';
		$outcome_xml[] = '</defaultValue>';
		$outcome_xml[] = '</outcomeDeclaration>';
		return implode('', $outcome_xml);
	}
	
	function get_interaction_xml($answers)
	{
		$interaction_xml[] = '<itemBody>';
		$interaction_xml[] = '<prompt>'.$this->include_question_images($this->get_content_object()->get_description()).'</prompt>';
		$interaction_xml[] = '<textEntryInteraction responseIdentifier="RESPONSE" expectedLength="50" />';
		/*foreach($answers as $i => $answer)
		{
			$interaction_xml[] = '<feedbackInline outcomeIdentifier="FEEDBACK'.$i.'" identifier="INCORRECT" showHide="hide">'.$this->include_question_images($answer->get_comment()).'</feedbackInline>';
		}*/
		$interaction_xml[] = '</itemBody>';
		return implode('', $interaction_xml);
	}
}
?>