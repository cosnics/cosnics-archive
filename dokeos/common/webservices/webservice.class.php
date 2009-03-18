<?php
require_once Path :: get_library_path() . 'webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../../user/lib/data_manager/database.class.php';
require_once dirname(__FILE__).'/../../common/configuration/configuration.class.php';
require_once Path :: get_webservice_path().'/lib/webservice_credential.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once Path :: get_webservice_path() . 'lib/data_manager/database.class.php';

abstract class Webservice
{
    private $message;

	public static function factory($webservice_handler, $protocol = 'Soap', $implementation = 'Nusoap')
	{
		$file_protocol = DokeosUtilities :: camelcase_to_underscores($protocol);
		$file_implementation = DokeosUtilities :: camelcase_to_underscores($implementation); 
		
		require_once dirname(__FILE__) . '/' . $file_protocol . '/' . $file_implementation . '/' . $file_protocol . '_' . $file_implementation . '_webservice.class.php';
		$class = $protocol . $implementation . 'Webservice';
		return new $class($webservice_handler);
	}
	
	abstract function provide_webservice($functions);
	
	/**
	 * Call a webservice
	 * @param $wsdl - the location of the webservice
	 * @param $functions - array of functionnames, parameters and handler function
	 * ex :: array(0 => (array('name' => functionname, 'parameters' => array of parameters, 'handler' => handler function)))
	 */	
	
	abstract function call_webservice($wsdl, $functions);	
	
	abstract function raise_message($message);    

    function create_hash($ip, $hash)
	{
		return $this->dbhash = Hashing :: hash($ip.$hash);
	}

	function validate_function($hash) 
	{
		$wdm = WebserviceDataManager :: get_instance();
		$wdm->delete_expired_webservice_credentials();
		$credentials = $wdm->retrieve_webservice_credentials_by_ip($_SERVER['REMOTE_ADDR']);
        $credentials = $credentials->as_array();
        if(is_array($credentials))
		{
            foreach($credentials as $c)
            {
                $h = Hashing ::hash($c->get_hash().$SERVER['REMOTE_ADDR']);
                if(strcmp($h , $hash)===0)
                {
                    return $c->get_user_id();
                }
                else
                {
                    $this->message = 'Incorrect hash value.';
                    return null;                    
                }
                
            }
		}
		else
		{
			$this->message = 'Incorrect IP address.';
		}
	}

	function set_end_time($time)
	{
		return $endTime = $time + (10*60);  //timeframe 10 mins
		
	}

	function set_create_time($time)
	{
		return date("l, F d, Y h:i" ,$time);
	}

	function check_time_left($endTime)
	{
		if(time() > $endtime)
		{
			$this->message = 'your available time has been used up.';
            return true;
		}
		else
		{
			$restTime = $endTime - time();
			$this->message = 'you have ' . $endTime . ' time left.';
            return false;
		}
	}

	function validate_login($username,$input_hash)
	{
		$udm = DatabaseUserDataManager :: get_instance();
		$user = $udm->retrieve_user_by_username($username);
		$hash = Hashing :: hash($user->get_password().$_SERVER['REMOTE_ADDR']);
		if(isset($user))
		{
			if(strcmp($hash, $input_hash)==0) //loginservice validate succesful, credential needed to validate the other webservices
			{
				$this->credential = new WebserviceCredential(
				array('user_id' => $user->get_id(), 'hash' =>$this->create_hash($_SERVER['REMOTE_ADDR'], $hash), 'time_created' =>time(), 'end_time'=>$this->set_end_time(time()), 'ip' =>$_SERVER['REMOTE_ADDR'])
				);
				$this->credential->create();
				return $this->credential->get_default_properties();
			}
			else
			{
				$this->message = 'Wrong hash value submitted.';
                return false;
			}
		}
		else
		{
			$this->message = "User $username does not exist.";
            return false;
		}
	}

    public function check_rights($webservicename,$userid)
    {
        $wm = new WebserviceManager();
        $webservice = $wm->retrieve_webservice_by_name($webservicename); 
        if(isset($webservice))
        {            
            $ru = new RightsUtilities();
            if($ru->is_allowed('1', $webservice->get_id(), 'webservice', 'webservice', $userid ))
            {               
               return true;
            }
            else
            {
                $this->message = 'You are not allowed to use this webservice';
                return false; 
                
            }
        }
        else
        {
            $this->message = 'No webservice by that name';
            return false; 

        }
        
    }

    public function can_execute($input_user, $webservicename)
    {   
        $userid = $this->validate_function($input_user[hash]);        
        if(isset($userid))
        {
            if($this->check_rights($webservicename,$userid))
            {                
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        
    }

    public function get_message()
    {
        return $this->raise_message($this->message);
    }
	
}	
	
?>