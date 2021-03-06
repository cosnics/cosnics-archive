<?php
/**
 * @package application.lib.profiler.profile_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once Path :: get_repository_path() . 'lib/content_object.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';
require_once dirname(__FILE__) . '/../profile_publication.class.php';

class DefaultProfilePublicationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultProfilePublicationTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ProfileTableColumn[]
     */
    private static function get_default_columns()
    {
        $udm = UserDataManager :: get_instance();
        $user_alias = $udm->get_database()->get_alias(User :: get_table_name());

        $columns = array();
        $columns[] = new ObjectTableColumn(ProfilePublication :: PROPERTY_PROFILE);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_USERNAME, true, $user_alias);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true, $user_alias);
        $columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true, $user_alias);
        return $columns;
    }
}
?>