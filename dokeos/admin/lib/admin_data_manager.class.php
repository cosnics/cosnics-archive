<?php
/**
 * @package admin.lib
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path() . 'configuration/configuration.class.php';

abstract class AdminDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    protected function AdminDataManager()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return AdminDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once dirname(__FILE__) . '/data_manager/' . strtolower($type) . '.class.php';
            $class = $type . 'AdminDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    abstract function create_storage_unit($name, $properties, $indexes);

    abstract function get_next_setting_id();

    abstract function get_next_language_id();

    abstract function get_next_registration_id();

    abstract function get_next_feedback_publication_id();

    abstract function get_next_validation_id();

    abstract function get_next_system_announcement_publication_id();

    abstract function create_language($language);

    abstract function create_registration($registration);

    abstract function create_setting($setting);

    abstract function create_system_announcement_publication($system_announcement_publication);

    abstract function retrieve_languages($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    abstract function count_settings($condition = null);

    abstract function retrieve_settings($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    abstract function retrieve_remote_packages($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    abstract function retrieve_registration($id);

    abstract function retrieve_remote_package($id);

    abstract function retrieve_registrations($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    abstract function count_remote_packages($condition = null);

    abstract function count_registrations($condition = null);

    abstract function retrieve_setting_from_variable_name($variable, $application = 'admin');

    abstract function retrieve_language_from_english_name($english_name);

    abstract function retrieve_feedback_publications($pid,$cid,$application);

    abstract function retrieve_feedback_publication($id);

    //abstract function retrieve_validations($pid,$cid,$application);

    abstract function retrieve_validation($id);

    abstract function update_setting($setting);

    abstract function update_registration($registration);

    abstract function update_system_announcement_publication($system_announcement_publication);

    abstract function delete_registration($registration);

    abstract function delete_setting($setting);

    abstract function delete_system_announcement_publication($system_announcement_publication);

    function get_languages()
    {
        $options = array();

        $languages = $this->retrieve_languages();
        while ($language = $languages->next_result())
        {
            $options[$language->get_folder()] = $language->get_original_name();
        }

        return $options;
    }

    /**
     * Count the system announcements
     * @param Condition $condition
     * @return int
     */
    abstract function count_system_announcement_publications($condition = null);

    /**
     * Retrieve a system announcement
     * @param int $id
     * @return SystemAnnouncementPublication
     */
    abstract function retrieve_system_announcement_publication($id);

    /**
     * Retrieve a series of system announcements
     * @param Condition $condition
     * @param array $order_by
     * @param int $offset
     * @param int $max_objects
     * @return SystemAnnouncementPublicationResultSet
     */
    abstract function retrieve_system_announcement_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    abstract function retrieve_system_announcement_publication_target_groups($system_announcement_publication);

    abstract function retrieve_system_announcement_publication_target_users($system_announcement_publication);

    abstract function get_next_category_id();

    abstract function select_next_display_order($parent_category_id);

    abstract function delete_category($category);

    abstract function update_category($category);

    abstract function create_category($category);

    abstract function count_categories($conditions = null);

    abstract function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null);

    abstract function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null);

    abstract function get_content_object_publication_attribute($publication_id);

    abstract function any_content_object_is_published($object_ids);

    abstract function count_publication_attributes($type = null, $condition = null);

    abstract function delete_content_object_publications($object_id);

    abstract function delete_settings($condition = null);

    abstract function delete_validation($validation);

    abstract function update_validation($validation);

    abstract function create_validation($validation);

    abstract function delete_feedback_publication($feeback_publication);

    abstract function update_feedback_publication($feedback_publication);

    abstract function create_feedback_publication($feedback_publication);

    abstract function retrieve_validations($condition = null, $order_by = array (), $offset = 0, $max_objects = -1);

    abstract function count_validations($condition =null);
}
?>