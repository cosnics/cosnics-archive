<?php
/**
 * @package repository.learningobject
 * @subpackage exercise
 */
/**
 * This class represents an option in a ordering question.
 */
class OrderingQuestionOption
{
    /**
     * The value of the option
     */
    private $value;
    /**
     * The order of the option
     */
    private $order;

    /**
     * Creates a new option for a ordering question
     * @param string $value The value of the option
     * @param int $rank The rank of this answer in the question
     */
    function OrderingQuestionOption($value, $order)
    {
        $this->value = $value;
        $this->order = $order;
    }

    /**
     * Gets the order of this option
     * @return int
     */
    function get_order()
    {
        return $this->order;
    }

    /**
     * Gets the value of this option
     * @return string
     */
    function get_value()
    {
        return $this->value;
    }
}
?>