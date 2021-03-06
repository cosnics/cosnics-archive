<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a system announcement
 * @author Van Wayenbergh David
 */

abstract class ImportSystemAnnouncement extends Import
{
	abstract function is_valid($parameters);
	abstract function convert_to_lcms($parameters);
	abstract static function get_all($parameters);
}

?>
