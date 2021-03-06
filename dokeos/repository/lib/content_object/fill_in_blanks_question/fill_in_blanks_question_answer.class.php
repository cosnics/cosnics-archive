<?php

class FillInBlanksQuestionAnswer
{
	private $value;
	private $weight;
	private $comment;
	private $size;
	private $position;

    function FillInBlanksQuestionAnswer($value, $weight, $comment, $size, $position)
    {
    	$this->value = $value;
    	$this->weight = $weight;
    	$this->comment = $comment;
    	$this->size = $size;
    	$this->position = $position;
    }

    function get_comment()
    {
    	return $this->comment;
    }

    function get_value()
    {
    	return $this->value;
    }

    function get_weight()
    {
    	return $this->weight;
    }

    function get_size()
    {
    	return $this->size;
    }

    function get_position()
    {
    	return $this->position;
    }
}
?>