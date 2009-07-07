<?php
/**
 * @package repository
 */
require_once dirname(__FILE__) . '/../../common/global.inc.php';
require_once Path :: get_home_path() . 'lib/home_manager/home_manager.class.php';
require_once Path :: get_home_path() . 'lib/home_data_manager.class.php';

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
    $user_id = Session :: get_user_id();
    $tab = Request :: post('tab');

    $hdm = HomeDataManager :: get_instance();

    $tab = $hdm->retrieve_home_tab($tab);

    if ($tab->get_user() == $user_id && $tab->can_be_deleted())
    {
        if ($tab->delete())
        {
            $json_result['success'] = '1';
            $json_result['message'] = Translation :: get('TabDeleted');
        }
        else
        {
            $json_result['success'] = '0';
            $json_result['message'] = Translation :: get('TabNotDeleted');
        }
    }
    else
    {
        $json_result['success'] = '0';
        $json_result['message'] = Translation :: get('TabNotDeleted');
    }
}
else
{
    $json_result['success'] = '0';
    $json_result['message'] = Translation :: get('NotAuthorized');
}

// Return a JSON object
echo json_encode($json_result);
?>