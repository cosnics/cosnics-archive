<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines unknow data
 * @author Van Wayenbergh David
 */

abstract class ImportUnknownData
{
	abstract function is_valid_unknow_data();
	abstract function convert_to_content_object();
	abstract static function get_all($parameters);
}

?>
