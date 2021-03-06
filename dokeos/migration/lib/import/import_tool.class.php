<?php
/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a tool
 * @author Van Wayenbergh David
 */
abstract class ImportTool extends Import
{
	abstract function is_valid();
	abstract function convert_to_lcms($course);
	abstract static function get_all($parameters);
}
?>