<?php
/**
 * @package application.lib.profiler.publisher
 */
require_once Path :: get_application_library_path() . 'publisher/component/viewer.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_display.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';

/**
 * This class represents a profile publisher component which can be used
 * to preview a learning object in the learning object publisher.
 */
class ProfilePublisherViewerComponent extends PublisherViewerComponent
{
}
?>