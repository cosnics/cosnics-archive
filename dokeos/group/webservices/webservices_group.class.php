<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_group.class.php';
require_once dirname(__FILE__) . '/../lib/group.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once Path :: get_library_path() . 'validator/validator.class.php';

ini_set('max_execution_time', -1);
ini_set('memory_limit',-1);

$handler = new WebServicesGroup();
$handler->run();

class WebServicesGroup
{
	private $webservice;
	private $functions;
    private $validator;
	
	function WebServicesGroup()
	{
		$this->webservice = Webservice :: factory($this);
        $this->validator = Validator :: get_validator('group');
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['get_group'] = array(
			'input' => new Group(),
			'output' => new Group()
		);

        $functions['get_groups'] = array(
            'array_input' => true,
			'input' => array(new Group()),
			'output' => array(new Group()),
            'array' => true,
		);
		
		$functions['create_group'] = array(
			'input' => new Group()
		);

        $functions['create_groups'] = array(
            'array_input' => true,
			'input' => array(new Group())
        );

        $functions['update_group'] = array(
			'input' => new Group()
		);

        $functions['update_groups'] = array(
            'array_input' => true,
			'input' => array(new Group())
        );

		$functions['delete_group'] = array(
			'input' => new Group()
		);

        $functions['delete_groups'] = array(
            'array_input' => true,
			'input' => array(new Group())
        );
		
		$functions['subscribe_user'] = array(
			'input' => new GroupRelUser()
		);

        $functions['subscribe_users'] = array(
            'array_input' => true,
			'input' => array(new GroupRelUser())
        );
		
		$functions['unsubscribe_user'] = array(
			'input' => new GroupRelUser()
		);

        $functions['unsubscribe_users'] = array(
            'array_input' => true,
			'input' => array(new GroupRelUser())
        );
		
		$this->webservice->provide_webservice($functions);

	}
	
	function get_group(&$input_group)
	{
        if($this->webservice->can_execute($input_group, 'get group'))
		{
            $gdm = DatabaseGroupDataManager :: get_instance();
            if($this->validator->validate_retrieve($input_group[input]))
            {
                $group = $gdm->retrieve_group_by_name($input_group[input][name]);
                if(!empty($group))
                {
                    return $group->get_default_properties();
                }
                else
                {
                    return $this->webservice->raise_error('Group '.$input_group[input][name].' not found.');
                }
            }
            else
            {
                return $this->webservice->raise_error("Could not retrieve group. Please check the data you've provided.");
            }
        }
        else
        {
            return $this->webservice->get_message();
        }
	}

    function get_groups(&$input_group)
	{
        if($this->webservice->can_execute($input_group, 'get groups'))
		{
            $gdm = DatabaseGroupDataManager :: get_instance();
            foreach($input_group[input] as $group)
            {
                if($this->validator->validate_retrieve($group))
                {
                    $groups[] = $gdm->retrieve_group_by_name($group[name])->get_default_properties();
                }
                else
                {
                    return $this->webservice->raise_error("Could not retrieve group. Please check the data you've provided.");
                }
            }
            return $groups;
        }
        else
        {
            return $this->webservice->get_message();
        }
	}
	
	function create_group(&$input_group)
	{
        if($this->webservice->can_execute($input_group, 'create group'))
		{
            if($this->validator->validate_create($input_group[input]))
            {
                $g = new Group(0,$input_group[input]);
                return $this->webservice->raise_message($g->create());
            }
            else
            {
                return $this->webservice->raise_error("Could not create group {$input_group[input][Group ::PROPERTY_NAME]}. Please check the data you've provided.");
            }
        }
        else
        {
            return $this->webservice->get_message();
        }
	}

    function create_groups(&$input_group)
	{
        if($this->webservice->can_execute($input_group, 'create groups'))
		{
            foreach($input_group[input] as $group)
            {
                if($this->validator->validate_create($group))
                {
                    $g = new Group(0,$group);
                    $g->create();
                }
                else
                {
                    return $this->webservice->raise_error("Could not create group {$group[Group ::PROPERTY_NAME]}. Please check the data you've provided.");
                }
            }
            return $this->webservice->raise_message('Groups created');
        }
        else
        {
            return $this->webservice->get_message();
        }
	}

    function update_group($input_group)
	{
		if($this->webservice->can_execute($input_group, 'update group'))
		{
            if($this->validator->validate_update($input_group[input]))
            {
                $g = new Group(0,$input_group[input]);
                return $this->webservice->raise_message($g->update());
            }
            else
            {
                return $this->webservice->raise_error("Could not update group {$input_group[input][Group ::PROPERTY_NAME]}. Please check the data you've provided.");
            }
        }
        else
        {
            return $this->webservice->get_message();
        }
	}

    function update_groups($input_group)
	{
		if($this->webservice->can_execute($input_group, 'update groups'))
		{
            foreach($input_group[input] as $group)
            {
                if($this->validator->validate_update($group))
                {
                    $g = new Group(0,$group);
                    $g->update();
                }
                else
                {
                    return $this->webservice->raise_error("Could not update group {$group[Group ::PROPERTY_NAME]}. Please check the data you've provided.");
                }
            }
            return $this->webservice->raise_message('Groups updated');
        }
        else
        {
            return $this->webservice->get_message();
        }
	}
	
	function delete_group(&$input_group)
	{
		if($this->webservice->can_execute($input_group, 'delete group'))
		{
            if($this->validator->validate_delete($input_group[input]))
            {
                $g = new Group(0,$input_group[input]);
                return $this->webservice->raise_message($g->delete());
            }
            else
            {
                return $this->webservice->raise_error("Could not delete group {$input_group[input][Group ::PROPERTY_NAME]}. Please check the data you've provided.");
            }
        }
        else
        {
            return $this->webservice->get_message();
        }
	}

    function delete_groups(&$input_group)
	{
		if($this->webservice->can_execute($input_group, 'delete groups'))
		{
            foreach($input_group[input] as $group)
            {
                if($this->validator->validate_delete($group))
                {
                    $g = new Group(0,$group);
                    $g->delete();
                }
                else
                {
                    return $this->webservice->raise_error("Could not delete group {$group[Group ::PROPERTY_NAME]}. Please check the data you've provided.");
                }
            }
            return $this->webservice->raise_message('Groups deleted');
        }
        else
        {
            return $this->webservice->get_message();
        }
	}
	
	function subscribe_user(&$input_group_rel_user)
	{
        if($this->webservice->can_execute($input_group_rel_user, 'subscribe user'))
		{            
            if($this->validator->validate_subscribe_or_unsubscribe($input_group_rel_user[input]))
            {
                $gru = new GroupRelUser($input_group_rel_user[input][group_id],$input_group_rel_user[input][user_id]);
                return $this->webservice->raise_message($gru->create());
            }
            else
            {
                return $this->webservice->raise_error("Could not subscribe user to group. Please check the data you've provided.");
            }
         }
        else
        {
            return $this->webservice->get_message();
        }
	}

    function subscribe_users(&$input_group_rel_user)
	{
        if($this->webservice->can_execute($input_group_rel_user, 'subscribe users'))
		{
            foreach($input_group_rel_user[input] as $group_rel_user)
            {
                if($this->validator->validate_subscribe_or_unsubscribe($group_rel_user))
                {
                    $gru = new GroupRelUser($group_rel_user[group_id],$group_rel_user[user_id]);
                    $gru->create();
                }
                else
                {
                    return $this->webservice->raise_error("Could not subscribe user {$group_rel_user[user_id]} to group {$group_rel_user[group_id]}. Please check the data you've provided.");
                }
            }
            return $this->webservice->raise_message('Users subscribed');
         }
        else
        {
            return $this->webservice->get_message();
        }
	}
	
	function unsubscribe_user(&$input_group_rel_user)
	{
        if($this->webservice->can_execute($input_group_rel_user, 'unsubscribe user'))
		{
            if($this->validator->validate_subscribe_or_unsubscribe($input_group_rel_user[input]))
            {
                $gru = new GroupRelUser($input_group_rel_user[input][group_id],$input_group_rel_user[input][user_id]);
                return $this->webservice->raise_message($gru->delete());
            }
            else
            {
                return $this->webservice->raise_error("Could not unsubscribe user from group. Please check the data you've provided.");
            }
         }
        else
        {
            return $this->webservice->get_message();
        }
	}

    function unsubscribe_users(&$input_group_rel_user)
	{
        if($this->webservice->can_execute($input_group_rel_user, 'unsubscribe users'))
		{
            foreach($input_group_rel_user[input] as $group_rel_user)
            {
                if($this->validator->validate_subscribe_or_unsubscribe($group_rel_user))
                {
                    $gru = new GroupRelUser($group_rel_user[group_id],$group_rel_user[user_id]);
                    $gru->delete();
                }
                else
                {
                    return $this->webservice->raise_error("Could not unsubscribe user {$group_rel_user[user_id]} from group {$group_rel_user[group_id]}. Please check the data you've provided.");
                }
            }
            return $this->webservice->raise_message('Users unsubscribed');
         }
        else
        {
            return $this->webservice->get_message();
        }
	}
	
}