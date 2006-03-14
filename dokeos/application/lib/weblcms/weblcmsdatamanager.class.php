<?php
require_once dirname(__FILE__).'/../../../repository/lib/configuration.class.php';

abstract class WebLCMSDataManager
{
	private static $instance;

	protected function WebLCMSDataManager()
	{
		$this->initialize();
	}

	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'WebLCMSDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}
	
	abstract function initialize();
	
	/**
	 * Retrieves a single learning object publication from persistent
	 * storage.
	 * @param int $pid The numeric identifier of the publication.
	 * @return LearningObjectPublication The publication.
	 */
	abstract function retrieve_learning_object_publication($pid);
	
	/**
	 * Retrieves learning object publications from persistent storage.
	 * @param string $course The ID of the course to find publications in, or
	 *                       null if none.
	 * @param mixed $categories The IDs of the category that publications must
	 *                          located in, or null if none.
	 * @param mixed $users The IDs of the user who should have access to the
	 *                     publications, or null if none.
	 * @param mixed $groups The IDs of the groups who should have access to
	 *                      the publications, or null if none.
	 * @param Condition $conditions Conditions for publication selection. See
	 *                              the Conditions framework.
	 * @param array $orderBy The properties to order publications by.
	 * @param array $orderDesc An array representing the sorting direction
	 *                         for the corresponding property of $orderBy.
	 *                         Use SORT_ASC for ascending order, SORT_DESC
	 *                         for descending.
	 * @param int $firstIndex The index of the first publication to retrieve.
	 * @param int $maxObjects The maximum number of objects to retrieve.
	 * @return array An array of LearningObjectPublications.
	 */ 
	abstract function retrieve_learning_object_publications($course = null, $categories = null, $users = null, $groups = null, $conditions = null, $orderBy = array ('display_order'), $orderDesc = array (SORT_ASC), $firstIndex = 0, $maxObjects = -1);

	/**
	 * Counts learning object publications in persistent storage.
	 * @param string $course The ID of the course to find publications in, or
	 *                       null if none.
	 * @param mixed $categories The IDs of the category that publications must
	 *                          located in, or null if none.
	 * @param mixed $users The IDs of the user who should have access to the
	 *                     publications, or null if none.
	 * @param mixed $groups The IDs of the groups who should have access to
	 *                      the publications, or null if none.
	 * @param Condition $conditions Conditions for publication selection. See
	 *                              the Conditions framework.
	 * @return int The number of matching learning object publications.
	 */
	abstract function count_learning_object_publications($course = null, $categories = null, $users = null, $groups = null, $conditions = null);

	/**
	 * Creates a learning object publication in persistent storage, assigning
	 * an ID to it. Uses the object's set_id function and returns the ID.
	 * @param LearningObjectPublication $publication The publication to make
	 *                                               persistent.
	 * @return int The publication's newly assigned ID.
	 */     
	abstract function create_learning_object_publication($publication);
	
	/**
	 * Updates a learning object publication in persistent storage.
	 * @param LearningObjectPublication $publication The publication to update
	 *                                               in storage.
	 */
	abstract function update_learning_object_publication($publication);
	
	/**
	 * Removes learning object publication from persistent storage.
	 * @param LearningObjectPublication $publication The publication to remove
	 *                                               from storage.
	 */
	abstract function delete_learning_object_publication($publication);
}

?>