<?php
require_once Path :: get_group_path() . '/lib/data_manager/database.class.php';
require_once Path :: get_group_path() . '/lib/group.class.php';
require_once Path :: get_group_path() . '/lib/group_rel_user.class.php';
require_once Path :: get_library_path() . 'validator/validator.class.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user_validatorclass
 * The purpose of this class is to validate the given User-properties:
 * -To check if all the required properties are there
 * -To check if e.g. the name of a person exists and retrieve the respective ID where necessary
 * @author Samumon
 */
class GroupValidator extends Validator
{
    private $gdm;
    private $udm;

    function GroupValidator()
    {
        $this->gdm = DatabaseGroupDataManager ::get_instance();
        $this->udm = DatabaseUserDataManager ::get_instance();
    }

    private function get_required_group_property_names()
	{
		return array (Group :: PROPERTY_SORT, Group :: PROPERTY_PARENT);
	}

    private function get_required_group_rel_user_property_names()
	{
		return array (GroupRelUser :: PROPERTY_GROUP_ID, GroupRelUser :: PROPERTY_USER_ID);
	}

    function validate_retrieve(&$groupProperties)
    {
        if($groupProperties[name]==null)
        return false;

        return true;
    }

    function validate_create(&$groupProperties)
    {
        if(!$this->validate_properties($groupProperties,$this->get_required_group_property_names()))
        return false;

        if(!$this->validate_property_names($groupProperties, Group :: get_default_property_names()))
        return false;

        if(!$this->gdm->is_groupname_available($groupProperties[Group :: PROPERTY_NAME]))
        return false;

        /*
         * If the ID of the parent is 0, it's a root group and thus has no parent.
         */
        if($groupProperties[Group :: PROPERTY_PARENT]!='0')
        {
            $var = $this->get_group_id($groupProperties[Group :: PROPERTY_PARENT]);
            if(!$var)
            return false;
            else
            $groupProperties[User :: PROPERTY_PARENT] = $var;
        }

        return true;
    }

    function validate_update(&$groupProperties)
    {
        if(!$this->validate_properties($groupProperties,$this->get_required_group_property_names()))
        return false;

        if(!$this->validate_property_names($groupProperties, Group :: get_default_property_names()))
        return false;

        if(!$this->does_group_exist($groupProperties[id]))
        return false;
        
        if($groupProperties[Group :: PROPERTY_PARENT]!='0')
        {
            $var = $this->get_group_id($groupProperties[Group :: PROPERTY_PARENT]);
            if(!$var)
            return false;
            else
            $groupProperties[User :: PROPERTY_PARENT] = $var;
        }
        return true;
    }

    function validate_delete(&$groupProperties)
    {
        if(!$this->validate_properties($groupProperties,$this->get_required_group_property_names()))
        return false;

        if(!$this->validate_property_names($groupProperties, Group :: get_default_property_names()))
        return false;
        
        if(!$this->does_group_exist($groupProperties[id]))
        return false;

        return true;
    }

    function validate_subscribe_or_unsubscribe(&$input_group_rel_user)
    {
        if(!$this->validate_properties($input_group_rel_user,$this->get_required_group_rel_user_property_names()))
        return false;

        if(!$this->validate_property_names($input_group_rel_user, GroupRelUser :: get_default_property_names))
        return false;
        
        $var = $this->get_person_id($input_group_rel_user[GroupRelUser :: PROPERTY_USER_ID]);
        if(!$var)
        return false;
        else
        $input_group_rel_user[GroupRelUser :: PROPERTY_USER_ID] = $var;

        $var = $this->get_group_id($input_group_rel_user[GroupRelUser :: PROPERTY_GROUP_ID]);
        if(!$var)
        return false;
        else
        $input_group_rel_user[GroupRelUser :: PROPERTY_GROUP_ID] = $var;

        return true;
    }

    private function get_person_id($person_name)
    {
        $user = $this->udm->retrieve_user_by_username($person_name);
        if(isset($user) && count($user->get_default_properties())>0)
        {
           return $user->get_id();
        }
        else
        {
            return false;
        }
    }

    private function get_group_id($group_name)
    {
        $group = $this->gdm->retrieve_group_by_name($group_name);
        if(isset($group) && count($group->get_default_properties())>0)
        {
           return $group->get_id();
        }
        else
        {
            return false;
        }
    }

    private function does_group_exist($group_id)
    {
        return $this->gdm->count_groups(new EqualityCondition(Group :: PROPERTY_ID, $group_id))!=0;
    }

    private function does_user_exist($user_id)
    {
        return $this->udm->count_users(new EqualityCondition(User :: PROPERTY_USER_ID, $user_id))!=0;
    }
}
?>