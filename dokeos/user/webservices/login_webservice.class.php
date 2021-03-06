<?php
/*
 * This is the login webservice, which must be initially called to login and obtain a hash password key.
 * This hash key must then be used to call other webservices.
 * The expected input is a User object where the username and password properties are filled in.
 * Said password must be as follows:
 * hash(*outside IP address*.*hashed password as it is stored in the Dokeos database*).
 * The hashing algoritm needs to be the same as the one used to hash your password in the Dokeos database.
 *
 * Authors:
 * Stefan Billiet & Nick De Feyter
 * University College of Ghent
 */

require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../../user/lib/user.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';
require_once Path :: get_webservice_path() . '/lib/webservice_credential.class.php';

$handler = new LoginWebservice();
$handler->run();

class LoginWebservice
{
	private $webservice;		
	
	function LoginWebservice()
	{
		$this->webservice = Webservice :: factory($this);		
	}
	
	function run()
	{	
		
		$functions = array();
		
		$functions['login'] = array(
			'input' => new User(),
			'output' => new WebserviceCredential()
		);
		
		$this->webservice->provide_webservice($functions); 
	}	
	
	function login($input_user)
	{
        $hash =  $this->webservice->validate_login($input_user[input][username],$input_user[input][password]);
		if(!empty($hash))
		{				
            return array('hash' => $hash);
		}
		else
		{
            return $this->webservice->raise_error($this->webservice->get_message());
		}
	}
}