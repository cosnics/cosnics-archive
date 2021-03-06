<?php
/**
 * $Id$
 * @package repository.metadata
 * @subpackage ieee_lom
 */
/**
 * A Duration field used in IEEE LOM.
 * This object contains a duration value and a description
 */
class Duration {
	/**
	 * The duration
	 */
	private $duration;
	/**
	 * The description
	 */
	private $description;
	/**
	 * Constructor
	 * @param string|null $duration
	 * @param LangString|null $description
	 */
    function Duration($duration = null,$description = null) {
    	$this->duration = $duration;
    	$this->description = $description;
    }
    /**
     * Gets the duration
     * @return string|null
     */
    function get_duration()
    {
    	return $this->duration;
    }
    /**
     * Gets the description
     * @return LangString|null
     */
    function get_description()
    {
    	return $this->description;
    }
}
?>